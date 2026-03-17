<?php

namespace App\Http\Controllers;

use App\Models\EleicaoCidade;
use App\Models\Eleicao;
use App\Models\LogEleicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResponsavelController extends Controller
{
    public function index()
    {
        $query = EleicaoCidade::with('eleicao', 'cidade')
            ->whereHas('eleicao', fn($q) => $q->whereIn('status', ['rascunho', 'aberta', 'encerrada']));

        if (auth()->user()->perfil !== 'admin') {
            $query->where('cidade_id', auth()->user()->cidade_id);
        }

        $eleicoes = $query->get();

        return view('responsavel.index', compact('eleicoes'));
    }

    public function abrir(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->aberta, 403, 'Votacao ja esta aberta.');

        return view('responsavel.abrir', compact('eleicaoCidade'));
    }

    public function confirmarAbrir(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->aberta, 403, 'Votacao ja esta aberta.');

        $request->validate([
            'senha' => ['required'],
        ]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicaoCidade->update([
            'aberta'       => true,
            'data_abertura' => now(),
            'aberta_por'   => auth()->id(),
        ]);

        $eleicaoCidade->eleicao->update(['status' => 'aberta']);

        LogEleicao::registrar($eleicaoCidade->eleicao_id, 'votacao_aberta', "Votação aberta em {$eleicaoCidade->cidade->nome}.");

        return redirect()->route('responsavel.index')->with('sucesso', 'Votacao aberta com sucesso.');
    }

    public function encerrar(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(!$eleicaoCidade->aberta, 403, 'Votacao nao esta aberta.');

        return view('responsavel.encerrar', compact('eleicaoCidade'));
    }

    public function confirmarEncerrar(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(!$eleicaoCidade->aberta, 403, 'Votacao nao esta aberta.');

        $request->validate([
            'senha'       => ['required'],
            'justificativa' => ['required', 'string', 'min:10'],
        ]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicaoCidade->update([
            'aberta'             => false,
            'data_encerramento'  => now(),
            'encerrada_por'      => auth()->id(),
        ]);

        // Verifica se todas as cidades encerraram
        $todasEncerradas = $eleicaoCidade->eleicao->cidades()
            ->where('aberta', true)
            ->doesntExist();

        if ($todasEncerradas) {
            $eleicaoCidade->eleicao->update(['status' => 'encerrada']);
        }

        LogEleicao::registrar(
            $eleicaoCidade->eleicao_id,
            'votacao_encerrada',
            "Votação encerrada em {$eleicaoCidade->cidade->nome}. Justificativa: {$request->justificativa}"
        );

        return redirect()->route('responsavel.index')->with('sucesso', 'Votacao encerrada.');
    }

    public function editarMembros(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->data_encerramento, 403, 'Votacao ja encerrada.');

        return view('responsavel.membros', compact('eleicaoCidade'));
    }

    public function atualizarMembros(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->data_encerramento, 403, 'Votacao ja encerrada.');

        $totalTokensGerados = \App\Models\TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->count();

        $request->validate([
            'qtd_eleitorado' => ['required', 'integer', 'min:0'],
            'qtd_presencial' => ['required', 'integer', 'min:0'],
            'qtd_remoto'     => ['required', 'integer', 'min:' . $totalTokensGerados],
            'justificativa'  => ['required', 'string', 'min:10'],
        ], [
            'qtd_remoto.min'     => "Quantidade remoto nao pode ser menor que os tokens ja gerados ({$totalTokensGerados}).",
            'justificativa.min'  => 'A justificativa deve ter pelo menos 10 caracteres.',
            'justificativa.required' => 'A justificativa é obrigatória.',
        ]);

        $qtdEleitorado = (int) $request->qtd_eleitorado;
        $qtdPresencial = (int) $request->qtd_presencial;
        $qtdRemoto     = (int) $request->qtd_remoto;
        $qtdTotal      = $qtdPresencial + $qtdRemoto;

        if ($qtdTotal < $eleicaoCidade->votos_registrados) {
            return back()->withErrors([
                'qtd_presencial' => "O total ({$qtdTotal}) nao pode ser menor que os votos ja registrados ({$eleicaoCidade->votos_registrados}).",
            ])->withInput();
        }

        $anterior = $eleicaoCidade->qtd_membros;
        $eleicaoCidade->update([
            'qtd_eleitorado' => $qtdEleitorado,
            'qtd_presencial' => $qtdPresencial,
            'qtd_remoto'     => $qtdRemoto,
            'qtd_membros'    => $qtdTotal,
        ]);

        LogEleicao::registrar(
            $eleicaoCidade->eleicao_id,
            'membros_alterados',
            "Membros em {$eleicaoCidade->cidade->nome} alterados de {$anterior} para {$qtdTotal} ({$qtdPresencial} presencial + {$qtdRemoto} remoto). Justificativa: {$request->justificativa}"
        );

        return redirect()->route('responsavel.index')->with('sucesso', 'Quantidade de membros atualizada.');
    }
}
