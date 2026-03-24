<?php

namespace App\Http\Controllers;

use App\Models\EleicaoCidade;
use App\Models\Presenca;
use App\Models\TokenVotacao;
use App\Models\Voto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VotacaoController extends Controller
{
    public function index()
    {
        $cidadeId = auth()->user()->cidade_id;

        // Find an election that is open (aliança or vida) for this city
        $eleicaoCidade = EleicaoCidade::with('eleicao')
            ->where('cidade_id', $cidadeId)
            ->whereHas('eleicao', fn($q) => $q->where('status', 'aberta'))
            ->first();

        $aliancaAberta = $eleicaoCidade && $eleicaoCidade->aberta;
        $vidaAberta    = $eleicaoCidade && $eleicaoCidade->eleicao->aberta_vida;

        return view('maquina.index', compact('eleicaoCidade', 'aliancaAberta', 'vidaAberta'));
    }

    public function validarToken(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $cidadeId = auth()->user()->cidade_id;
        $hash     = hash('sha256', strtoupper(trim($request->token)));

        $tokenVotacao = TokenVotacao::where('token_hash', $hash)
            ->where('cidade_id', $cidadeId)
            ->where('usado', false)
            ->first();

        if (!$tokenVotacao) {
            return back()->withErrors(['token' => 'Token inválido, já utilizado ou não pertence a esta cidade.']);
        }

        // Check if voting of this escopo is open
        if ($tokenVotacao->escopo === 'vida') {
            if (!$tokenVotacao->eleicao->aberta_vida) {
                return back()->withErrors(['token' => 'A votação Realidade de Vida não está aberta no momento.']);
            }
        } else {
            $eleicaoCidade = EleicaoCidade::where('eleicao_id', $tokenVotacao->eleicao_id)
                ->where('cidade_id', $cidadeId)
                ->where('aberta', true)
                ->first();

            if (!$eleicaoCidade) {
                return back()->withErrors(['token' => 'A votação aliança desta cidade não está aberta.']);
            }
        }

        session([
            'votacao_hash'       => $hash,
            'votacao_cidade_id'  => $cidadeId,
            'votacao_eleicao_id' => $tokenVotacao->eleicao_id,
            'votacao_escopo'     => $tokenVotacao->escopo,
            'votacao_origem'     => 'remoto',
            'votacao_maquina_id' => null,
        ]);

        return redirect()->route('maquina.votar');
    }

    public function liberarPresencial(Request $request)
    {
        $request->validate([
            'senha'  => ['required', 'string'],
            'escopo' => ['required', 'in:vida,alianca'],
        ]);

        $user     = auth()->user();
        $cidadeId = $user->cidade_id;

        if (!Hash::check($request->senha, $user->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta. Autorização negada.']);
        }

        $eleicaoCidade = EleicaoCidade::with('eleicao')
            ->where('cidade_id', $cidadeId)
            ->whereHas('eleicao', fn($q) => $q->where('status', 'aberta'))
            ->first();

        if (!$eleicaoCidade) {
            return back()->withErrors(['senha' => 'Nenhuma eleição aberta para esta cidade.']);
        }

        if ($request->escopo === 'alianca') {
            if (!$eleicaoCidade->aberta) {
                return back()->withErrors(['senha' => 'A votação aliança desta cidade não está aberta.']);
            }
            if ($eleicaoCidade->qtd_presencial > 0 && $eleicaoCidade->votos_presenciais >= $eleicaoCidade->qtd_presencial) {
                return back()->withErrors(['senha' => "Limite de votos presenciais aliança atingido ({$eleicaoCidade->qtd_presencial})."]);
            }
        } else {
            if (!$eleicaoCidade->eleicao->aberta_vida) {
                return back()->withErrors(['senha' => 'A votação Realidade de Vida não está aberta.']);
            }
            if ($eleicaoCidade->qtd_presencial_vida > 0 && $eleicaoCidade->votos_presenciais_vida >= $eleicaoCidade->qtd_presencial_vida) {
                return back()->withErrors(['senha' => "Limite de votos presenciais vida atingido ({$eleicaoCidade->qtd_presencial_vida})."]);
            }
        }

        $sessaoHash = hash('sha256', Str::uuid()->toString());

        session([
            'votacao_hash'       => $sessaoHash,
            'votacao_cidade_id'  => $cidadeId,
            'votacao_eleicao_id' => $eleicaoCidade->eleicao_id,
            'votacao_escopo'     => $request->escopo,
            'votacao_origem'     => 'presencial',
            'votacao_maquina_id' => $user->id,
        ]);

        return redirect()->route('maquina.votar');
    }

    public function votar()
    {
        $hash      = session('votacao_hash');
        $cidadeId  = session('votacao_cidade_id');
        $eleicaoId = session('votacao_eleicao_id');
        $origem    = session('votacao_origem', 'remoto');
        $escopo    = session('votacao_escopo', 'alianca');

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('maquina.index')->withErrors(['token' => 'Sessão expirada. Insira o token novamente.']);
        }

        if ($origem === 'remoto') {
            $tokenValido = TokenVotacao::where('token_hash', $hash)->where('usado', false)->exists();
            if (!$tokenValido) {
                session()->forget(['votacao_hash', 'votacao_cidade_id', 'votacao_eleicao_id', 'votacao_escopo', 'votacao_origem', 'votacao_maquina_id']);
                return redirect()->route('maquina.index')->withErrors(['token' => 'Este token já foi utilizado.']);
            }
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

        return view('maquina.votar', compact('eleicaoCidade', 'perguntas'));
    }

    public function confirmarVoto(Request $request)
    {
        $hash      = session('votacao_hash');
        $cidadeId  = session('votacao_cidade_id');
        $eleicaoId = session('votacao_eleicao_id');
        $origem    = session('votacao_origem', 'remoto');
        $maquinaId = session('votacao_maquina_id');
        $escopo    = session('votacao_escopo', 'alianca');

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('maquina.index');
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

        DB::transaction(function () use ($hash, $escopo, $origem, $maquinaId, $perguntas, $request, $eleicaoCidade) {
            if ($origem === 'remoto') {
                TokenVotacao::where('token_hash', $hash)->update(['usado' => true]);

                Presenca::where('eleicao_id', $eleicaoCidade->eleicao_id)
                    ->where('cidade_id', $eleicaoCidade->cidade_id)
                    ->where('escopo', $escopo)
                    ->where('votou', false)
                    ->orderBy('id')
                    ->limit(1)
                    ->update(['votou' => true]);
            }

            foreach ($perguntas as $pergunta) {
                $respostas = $request->input('respostas.' . $pergunta->id, []);
                foreach ($respostas as $opcaoId) {
                    Voto::create([
                        'token_hash'  => $hash,
                        'pergunta_id' => $pergunta->id,
                        'opcao_id'    => $opcaoId,
                        'origem'      => $origem,
                        'maquina_id'  => $maquinaId,
                        'created_at'  => now(),
                    ]);
                }
            }

            // Track vida votes and auto-close when all members voted
            if ($escopo === 'vida') {
                if ($origem === 'presencial') {
                    $eleicaoCidade->increment('votos_presenciais_vida');
                }
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

            // Only aliança voting counts toward city-level quotas and auto-close
            if ($escopo === 'alianca') {
                $eleicaoCidade->increment('votos_registrados');
                if ($origem === 'presencial') {
                    $eleicaoCidade->increment('votos_presenciais');
                }

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

        return redirect()->route('maquina.confirmado');
    }

    public function confirmado()
    {
        return view('maquina.confirmado');
    }
}
