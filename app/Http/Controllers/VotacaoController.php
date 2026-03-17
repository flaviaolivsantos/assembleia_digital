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

        $eleicaoCidade = EleicaoCidade::with('eleicao')
            ->where('cidade_id', $cidadeId)
            ->where('aberta', true)
            ->first();

        return view('maquina.index', compact('eleicaoCidade'));
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
            return back()->withErrors(['token' => 'Token invalido, ja utilizado ou nao pertence a esta cidade.']);
        }

        $eleicaoCidade = EleicaoCidade::where('eleicao_id', $tokenVotacao->eleicao_id)
            ->where('cidade_id', $cidadeId)
            ->where('aberta', true)
            ->first();

        if (!$eleicaoCidade) {
            return back()->withErrors(['token' => 'A votacao desta cidade nao esta aberta.']);
        }

        session([
            'votacao_hash'       => $hash,
            'votacao_cidade_id'  => $cidadeId,
            'votacao_eleicao_id' => $tokenVotacao->eleicao_id,
            'votacao_origem'     => 'remoto',
            'votacao_maquina_id' => null,
        ]);

        return redirect()->route('maquina.votar');
    }

    // Libera votação presencial via senha do usuário maquina
    public function liberarPresencial(Request $request)
    {
        $request->validate([
            'senha' => ['required', 'string'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->senha, $user->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta. Autorizacao negada.']);
        }

        $cidadeId = $user->cidade_id;
        $eleicaoCidade = EleicaoCidade::where('cidade_id', $cidadeId)
            ->where('aberta', true)
            ->first();

        if (!$eleicaoCidade) {
            return back()->withErrors(['senha' => 'Nenhuma votacao aberta para esta cidade.']);
        }

        if ($eleicaoCidade->qtd_presencial > 0 && $eleicaoCidade->votos_presenciais >= $eleicaoCidade->qtd_presencial) {
            return back()->withErrors(['senha' => "Limite de votos presenciais atingido ({$eleicaoCidade->qtd_presencial})."]);
        }

        $sessaoHash = hash('sha256', Str::uuid()->toString());

        session([
            'votacao_hash'       => $sessaoHash,
            'votacao_cidade_id'  => $cidadeId,
            'votacao_eleicao_id' => $eleicaoCidade->eleicao_id,
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

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('maquina.index')->withErrors(['token' => 'Sessao expirada. Insira o token novamente.']);
        }

        // Valida token apenas para votação remota
        if ($origem === 'remoto') {
            $tokenValido = TokenVotacao::where('token_hash', $hash)->where('usado', false)->exists();
            if (!$tokenValido) {
                session()->forget(['votacao_hash', 'votacao_cidade_id', 'votacao_eleicao_id', 'votacao_origem', 'votacao_maquina_id']);
                return redirect()->route('maquina.index')->withErrors(['token' => 'Este token ja foi utilizado.']);
            }
        }

        $eleicaoCidade = EleicaoCidade::with('eleicao.perguntas.opcoes')
            ->where('eleicao_id', $eleicaoId)
            ->where('cidade_id', $cidadeId)
            ->firstOrFail();

        $perguntas = $eleicaoCidade->eleicao->perguntas->map(function ($pergunta) use ($cidadeId) {
            $pergunta->opcoesDaCidade = $pergunta->opcoesPorCidade($cidadeId)->get();
            return $pergunta;
        });

        return view('maquina.votar', compact('eleicaoCidade', 'perguntas'));
    }

    public function confirmarVoto(Request $request)
    {
        $hash      = session('votacao_hash');
        $cidadeId  = session('votacao_cidade_id');
        $eleicaoId = session('votacao_eleicao_id');
        $origem    = session('votacao_origem', 'remoto');
        $maquinaId = session('votacao_maquina_id');

        if (!$hash || !$cidadeId || !$eleicaoId) {
            return redirect()->route('maquina.index');
        }

        $eleicaoCidade = EleicaoCidade::with('eleicao.perguntas')
            ->where('eleicao_id', $eleicaoId)
            ->where('cidade_id', $cidadeId)
            ->where('aberta', true)
            ->firstOrFail();

        $perguntas = $eleicaoCidade->eleicao->perguntas;

        // Valida quantidade de respostas por pergunta
        foreach ($perguntas as $pergunta) {
            $respostas = $request->input('respostas.' . $pergunta->id, []);

            if (count($respostas) !== (int) $pergunta->qtd_respostas) {
                return back()->withErrors([
                    'respostas.' . $pergunta->id => "Selecione exatamente {$pergunta->qtd_respostas} opcao(oes) para: \"{$pergunta->pergunta}\"",
                ])->withInput();
            }

            // Verifica se as opções pertencem à cidade correta
            $opcaoIdsValidos = $pergunta->opcoesPorCidade($cidadeId)->pluck('id')->toArray();
            foreach ($respostas as $opcaoId) {
                if (!in_array((int) $opcaoId, $opcaoIdsValidos)) {
                    abort(403, 'Opcao invalida para esta cidade.');
                }
            }
        }

        DB::transaction(function () use ($hash, $origem, $maquinaId, $perguntas, $request, $eleicaoCidade) {
            // Apenas votação remota consome token e marca presença
            if ($origem === 'remoto') {
                TokenVotacao::where('token_hash', $hash)->update(['usado' => true]);

                Presenca::where('eleicao_id', $eleicaoCidade->eleicao_id)
                    ->where('cidade_id', $eleicaoCidade->cidade_id)
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

            // Atualiza contadores de votos
            $eleicaoCidade->increment('votos_registrados');
            if ($origem === 'presencial') {
                $eleicaoCidade->increment('votos_presenciais');
            }

            // Verifica encerramento automático
            $eleicaoCidade->refresh();
            if ($eleicaoCidade->votos_registrados >= $eleicaoCidade->qtd_membros && $eleicaoCidade->qtd_membros > 0) {
                $eleicaoCidade->update([
                    'aberta'            => false,
                    'data_encerramento' => now(),
                ]);
            }
        });

        session()->forget(['votacao_hash', 'votacao_cidade_id', 'votacao_eleicao_id', 'votacao_origem', 'votacao_maquina_id']);

        return redirect()->route('maquina.confirmado');
    }

    public function confirmado()
    {
        return view('maquina.confirmado');
    }
}
