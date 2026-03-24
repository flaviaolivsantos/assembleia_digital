<?php

namespace App\Http\Controllers;

use App\Models\EleicaoCidade;
use App\Models\Presenca;
use App\Models\TokenVotacao;
use App\Models\Voto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VotacaoPublicaController extends Controller
{
    public function index()
    {
        return view('votacao.index');
    }

    public function validarToken(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $hash = hash('sha256', strtoupper(trim($request->token)));

        $tokenVotacao = TokenVotacao::where('token_hash', $hash)
            ->where('usado', false)
            ->first();

        if (!$tokenVotacao) {
            return back()->withErrors(['token' => 'Token inválido ou já utilizado.']);
        }

        // Check if voting is open based on escopo
        if ($tokenVotacao->escopo === 'vida') {
            $eleicao = $tokenVotacao->eleicao;
            if (!$eleicao->aberta_vida) {
                return back()->withErrors(['token' => 'A votação Realidade de Vida não está aberta no momento.']);
            }
        } else {
            $eleicaoCidade = EleicaoCidade::where('eleicao_id', $tokenVotacao->eleicao_id)
                ->where('cidade_id', $tokenVotacao->cidade_id)
                ->where('aberta', true)
                ->first();

            if (!$eleicaoCidade) {
                return back()->withErrors(['token' => 'A votação para este token não está aberta no momento.']);
            }
        }

        session([
            'votacao_hash'       => $hash,
            'votacao_cidade_id'  => $tokenVotacao->cidade_id,
            'votacao_eleicao_id' => $tokenVotacao->eleicao_id,
            'votacao_escopo'     => $tokenVotacao->escopo,
            'votacao_origem'     => 'remoto',
            'votacao_maquina_id' => null,
        ]);

        return redirect()->route('votacao.votar');
    }

    public function votar()
    {
        $hash      = session('votacao_hash');
        $cidadeId  = session('votacao_cidade_id');
        $eleicaoId = session('votacao_eleicao_id');
        $escopo    = session('votacao_escopo', 'alianca');

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('votacao.index')->withErrors(['token' => 'Sessão expirada. Insira o token novamente.']);
        }

        $tokenValido = TokenVotacao::where('token_hash', $hash)->where('usado', false)->exists();
        if (!$tokenValido) {
            session()->forget(['votacao_hash', 'votacao_cidade_id', 'votacao_eleicao_id', 'votacao_escopo', 'votacao_origem', 'votacao_maquina_id']);
            return redirect()->route('votacao.index')->withErrors(['token' => 'Este token já foi utilizado.']);
        }

        $eleicaoCidade = EleicaoCidade::with('eleicao.perguntas.opcoes')
            ->where('eleicao_id', $eleicaoId)
            ->where('cidade_id', $cidadeId)
            ->firstOrFail();

        $perguntas = $eleicaoCidade->eleicao->perguntas
            ->filter(fn($p) => $p->escopo === $escopo)
            ->map(function ($pergunta) use ($cidadeId) {
                $pergunta->opcoesDaCidade = $pergunta->opcoesPorCidade($cidadeId)->get();
                return $pergunta;
            })
            ->filter(fn($p) => $p->opcoesDaCidade->count() > 0);

        $confirmRoute = 'votacao.confirmarVoto';

        return view('maquina.votar', compact('eleicaoCidade', 'perguntas', 'confirmRoute'));
    }

    public function confirmarVoto(Request $request)
    {
        $hash      = session('votacao_hash');
        $cidadeId  = session('votacao_cidade_id');
        $eleicaoId = session('votacao_eleicao_id');
        $escopo    = session('votacao_escopo', 'alianca');

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('votacao.index');
        }

        $eleicaoCidade = EleicaoCidade::with('eleicao.perguntas')
            ->where('eleicao_id', $eleicaoId)
            ->where('cidade_id', $cidadeId)
            ->firstOrFail();

        $eleicao = $eleicaoCidade->eleicao;

        // Validate that voting is still open for this escopo
        if ($escopo === 'alianca') {
            abort_unless($eleicaoCidade->aberta, 403, 'A votação aliança foi encerrada.');
        } else {
            abort_unless($eleicao->aberta_vida, 403, 'A votação Realidade de Vida foi encerrada.');
        }

        $perguntas = $eleicao->perguntas->filter(fn($p) => $p->escopo === $escopo)
            ->filter(fn($p) => $p->opcoesPorCidade($cidadeId)->count() > 0);

        foreach ($perguntas as $pergunta) {
            $respostas = $request->input('respostas.' . $pergunta->id, []);

            if (count($respostas) !== (int) $pergunta->qtd_respostas) {
                return back()->withErrors([
                    'respostas.' . $pergunta->id => "Selecione exatamente {$pergunta->qtd_respostas} opção(ões) para: \"{$pergunta->pergunta}\"",
                ])->withInput();
            }

            $opcaoIdsValidos = $pergunta->opcoesPorCidade($cidadeId)->pluck('id')->toArray();
            foreach ($respostas as $opcaoId) {
                if (!in_array((int) $opcaoId, $opcaoIdsValidos)) {
                    abort(403, 'Opção inválida para esta cidade.');
                }
            }
        }

        DB::transaction(function () use ($hash, $escopo, $perguntas, $request, $eleicaoCidade) {
            TokenVotacao::where('token_hash', $hash)->update(['usado' => true]);

            Presenca::where('eleicao_id', $eleicaoCidade->eleicao_id)
                ->where('cidade_id', $eleicaoCidade->cidade_id)
                ->where('escopo', $escopo)
                ->where('votou', false)
                ->orderBy('id')
                ->limit(1)
                ->update(['votou' => true]);

            foreach ($perguntas as $pergunta) {
                $respostas = $request->input('respostas.' . $pergunta->id, []);
                foreach ($respostas as $opcaoId) {
                    Voto::create([
                        'token_hash'  => $hash,
                        'pergunta_id' => $pergunta->id,
                        'opcao_id'    => $opcaoId,
                        'origem'      => 'remoto',
                        'maquina_id'  => null,
                        'created_at'  => now(),
                    ]);
                }
            }

            // Track vida votes and auto-close when all members voted
            if ($escopo === 'vida') {
                $eleicaoCidade->increment('votos_registrados_vida');
                $eleicaoCidade->refresh();

                $eleicao = $eleicaoCidade->eleicao;
                $totalVidaMembros = $eleicao->cidades()->sum(DB::raw('qtd_presencial_vida + qtd_vida'));
                $totalVidaVotos   = $eleicao->cidades()->sum('votos_registrados_vida');
                if ($totalVidaMembros > 0 && $totalVidaVotos >= $totalVidaMembros) {
                    $eleicao->update([
                        'aberta_vida'            => false,
                        'data_encerramento_vida' => now(),
                    ]);
                    if ($eleicao->cidades()->where('aberta', true)->doesntExist()) {
                        $eleicao->update(['status' => 'encerrada']);
                    }
                }
            }

            // Only aliança voting increments city counters and auto-closes
            if ($escopo === 'alianca') {
                $eleicaoCidade->increment('votos_registrados');
                $eleicaoCidade->refresh();
                if ($eleicaoCidade->votos_registrados >= $eleicaoCidade->qtd_membros && $eleicaoCidade->qtd_membros > 0) {
                    $eleicaoCidade->update([
                        'aberta'            => false,
                        'data_encerramento' => now(),
                    ]);
                }
            }
        });

        session()->forget(['votacao_hash', 'votacao_cidade_id', 'votacao_eleicao_id', 'votacao_escopo', 'votacao_origem', 'votacao_maquina_id']);

        return redirect()->route('votacao.confirmado')->with('voto_registrado', true);
    }

    public function confirmado()
    {
        if (!session('voto_registrado')) {
            return redirect()->route('votacao.index');
        }

        return view('votacao.confirmado');
    }
}
