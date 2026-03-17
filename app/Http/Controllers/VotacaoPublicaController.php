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
            return back()->withErrors(['token' => 'Token invalido ou ja utilizado.']);
        }

        $eleicaoCidade = EleicaoCidade::where('eleicao_id', $tokenVotacao->eleicao_id)
            ->where('cidade_id', $tokenVotacao->cidade_id)
            ->where('aberta', true)
            ->first();

        if (!$eleicaoCidade) {
            return back()->withErrors(['token' => 'A votacao para este token nao esta aberta no momento.']);
        }

        session([
            'votacao_hash'       => $hash,
            'votacao_cidade_id'  => $tokenVotacao->cidade_id,
            'votacao_eleicao_id' => $tokenVotacao->eleicao_id,
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

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('votacao.index')->withErrors(['token' => 'Sessao expirada. Insira o token novamente.']);
        }

        $tokenValido = TokenVotacao::where('token_hash', $hash)->where('usado', false)->exists();
        if (!$tokenValido) {
            session()->forget(['votacao_hash', 'votacao_cidade_id', 'votacao_eleicao_id', 'votacao_origem', 'votacao_maquina_id']);
            return redirect()->route('votacao.index')->withErrors(['token' => 'Este token ja foi utilizado.']);
        }

        $eleicaoCidade = EleicaoCidade::with('eleicao.perguntas.opcoes')
            ->where('eleicao_id', $eleicaoId)
            ->where('cidade_id', $cidadeId)
            ->firstOrFail();

        $perguntas = $eleicaoCidade->eleicao->perguntas->map(function ($pergunta) use ($cidadeId) {
            $pergunta->opcoesDaCidade = $pergunta->opcoesPorCidade($cidadeId)->get();
            return $pergunta;
        });

        $confirmRoute = 'votacao.confirmarVoto';

        return view('maquina.votar', compact('eleicaoCidade', 'perguntas', 'confirmRoute'));
    }

    public function confirmarVoto(Request $request)
    {
        $hash      = session('votacao_hash');
        $cidadeId  = session('votacao_cidade_id');
        $eleicaoId = session('votacao_eleicao_id');

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('votacao.index');
        }

        $eleicaoCidade = EleicaoCidade::with('eleicao.perguntas')
            ->where('eleicao_id', $eleicaoId)
            ->where('cidade_id', $cidadeId)
            ->where('aberta', true)
            ->firstOrFail();

        $perguntas = $eleicaoCidade->eleicao->perguntas;

        foreach ($perguntas as $pergunta) {
            $respostas = $request->input('respostas.' . $pergunta->id, []);

            if (count($respostas) !== (int) $pergunta->qtd_respostas) {
                return back()->withErrors([
                    'respostas.' . $pergunta->id => "Selecione exatamente {$pergunta->qtd_respostas} opcao(oes) para: \"{$pergunta->pergunta}\"",
                ])->withInput();
            }

            $opcaoIdsValidos = $pergunta->opcoesPorCidade($cidadeId)->pluck('id')->toArray();
            foreach ($respostas as $opcaoId) {
                if (!in_array((int) $opcaoId, $opcaoIdsValidos)) {
                    abort(403, 'Opcao invalida para esta cidade.');
                }
            }
        }

        DB::transaction(function () use ($hash, $perguntas, $request, $eleicaoCidade) {
            TokenVotacao::where('token_hash', $hash)->update(['usado' => true]);

            Presenca::where('eleicao_id', $eleicaoCidade->eleicao_id)
                ->where('cidade_id', $eleicaoCidade->cidade_id)
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

            $eleicaoCidade->increment('votos_registrados');
            $eleicaoCidade->refresh();
            if ($eleicaoCidade->votos_registrados >= $eleicaoCidade->qtd_membros && $eleicaoCidade->qtd_membros > 0) {
                $eleicaoCidade->update([
                    'aberta'            => false,
                    'data_encerramento' => now(),
                ]);
            }
        });

        session()->forget(['votacao_hash', 'votacao_cidade_id', 'votacao_eleicao_id', 'votacao_origem', 'votacao_maquina_id']);

        return redirect()->route('votacao.confirmado');
    }

    public function confirmado()
    {
        return view('votacao.confirmado');
    }
}
