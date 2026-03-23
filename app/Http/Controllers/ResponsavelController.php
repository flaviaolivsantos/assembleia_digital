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
        $user = auth()->user();

        if ($user->perfil === 'admin') {
            $eleicoes = Eleicao::with(['cidades.cidade', 'cidades.abertaPor', 'cidades.encerradaPor'])
                ->whereIn('status', ['rascunho', 'aberta', 'encerrada'])
                ->orderByDesc('data_eleicao')
                ->get();
        } else {
            $eleicoes = Eleicao::with(['cidades' => function ($q) use ($user) {
                $q->where('cidade_id', $user->cidade_id)
                  ->with(['cidade', 'abertaPor', 'encerradaPor']);
            }])
                ->whereIn('status', ['rascunho', 'aberta', 'encerrada'])
                ->whereHas('cidades', fn($q) => $q->where('cidade_id', $user->cidade_id))
                ->orderByDesc('data_eleicao')
                ->get();
        }

        return view('responsavel.index', compact('eleicoes'));
    }

    // ─── Aliança (per-city) ────────────────────────────────────────────

    public function abrir(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->aberta, 403, 'Votação aliança já está aberta.');

        return view('responsavel.abrir', compact('eleicaoCidade'));
    }

    public function confirmarAbrir(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->aberta, 403, 'Votação aliança já está aberta.');

        $request->validate(['senha' => ['required']]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicaoCidade->update([
            'aberta'        => true,
            'data_abertura' => now(),
            'aberta_por'    => auth()->id(),
        ]);

        $eleicaoCidade->eleicao->update(['status' => 'aberta']);

        LogEleicao::registrar($eleicaoCidade->eleicao_id, 'votacao_aberta', "Votação aliança aberta em {$eleicaoCidade->cidade->nome}.");

        return redirect()->route('responsavel.index')->with('sucesso', 'Votação aliança aberta com sucesso.');
    }

    public function encerrar(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(!$eleicaoCidade->aberta, 403, 'Votação aliança não está aberta.');

        return view('responsavel.encerrar', compact('eleicaoCidade'));
    }

    public function confirmarEncerrar(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(!$eleicaoCidade->aberta, 403, 'Votação aliança não está aberta.');

        $request->validate([
            'senha'         => ['required'],
            'justificativa' => ['required', 'string', 'min:10'],
        ]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicaoCidade->update([
            'aberta'            => false,
            'data_encerramento' => now(),
            'encerrada_por'     => auth()->id(),
        ]);

        $eleicao = $eleicaoCidade->eleicao;

        // Encerra a eleição se todas as aliança estão fechadas E vida está fechada
        $todasAliancaFechadas = $eleicao->cidades()->where('aberta', true)->doesntExist();
        if ($todasAliancaFechadas && !$eleicao->aberta_vida) {
            $eleicao->update(['status' => 'encerrada']);
        }

        LogEleicao::registrar(
            $eleicaoCidade->eleicao_id,
            'votacao_encerrada',
            "Votação aliança encerrada em {$eleicaoCidade->cidade->nome}. Justificativa: {$request->justificativa}"
        );

        return redirect()->route('responsavel.index')->with('sucesso', 'Votação aliança encerrada.');
    }

    // ─── Vida (election-wide) ─────────────────────────────────────────

    public function abrirVida(Eleicao $eleicao)
    {
        $this->autorizarVida($eleicao);
        abort_if($eleicao->aberta_vida, 403, 'Votação vida já está aberta.');

        return view('responsavel.abrir_vida', compact('eleicao'));
    }

    public function confirmarAbrirVida(Request $request, Eleicao $eleicao)
    {
        $this->autorizarVida($eleicao);
        abort_if($eleicao->aberta_vida, 403, 'Votação vida já está aberta.');

        $request->validate(['senha' => ['required']]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicao->update([
            'aberta_vida'        => true,
            'data_abertura_vida' => now(),
            'aberta_por_vida'    => auth()->id(),
            'status'             => 'aberta',
        ]);

        LogEleicao::registrar($eleicao->id, 'vida_aberta', 'Votação Realidade de Vida aberta.');

        return redirect()->route('responsavel.index')->with('sucesso', 'Votação Realidade de Vida aberta com sucesso.');
    }

    public function encerrarVida(Eleicao $eleicao)
    {
        $this->autorizarVida($eleicao);
        abort_if(!$eleicao->aberta_vida, 403, 'Votação vida não está aberta.');

        return view('responsavel.encerrar_vida', compact('eleicao'));
    }

    public function confirmarEncerrarVida(Request $request, Eleicao $eleicao)
    {
        $this->autorizarVida($eleicao);
        abort_if(!$eleicao->aberta_vida, 403, 'Votação vida não está aberta.');

        $request->validate([
            'senha'         => ['required'],
            'justificativa' => ['required', 'string', 'min:10'],
        ]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicao->update([
            'aberta_vida'            => false,
            'data_encerramento_vida' => now(),
            'encerrada_por_vida'     => auth()->id(),
        ]);

        // Encerra a eleição se todas as aliança também estão fechadas
        $todasAliancaFechadas = $eleicao->cidades()->where('aberta', true)->doesntExist();
        if ($todasAliancaFechadas) {
            $eleicao->update(['status' => 'encerrada']);
        }

        LogEleicao::registrar(
            $eleicao->id,
            'vida_encerrada',
            "Votação Realidade de Vida encerrada. Justificativa: {$request->justificativa}"
        );

        return redirect()->route('responsavel.index')->with('sucesso', 'Votação Realidade de Vida encerrada.');
    }

    // ─── Membros ──────────────────────────────────────────────────────

    public function editarMembros(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->data_encerramento, 403, 'Votação aliança já encerrada.');

        return view('responsavel.membros', compact('eleicaoCidade'));
    }

    public function atualizarMembros(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if($eleicaoCidade->data_encerramento, 403, 'Votação aliança já encerrada.');

        $totalTokensAlianca = \App\Models\TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->where('escopo', 'alianca')
            ->count();

        $request->validate([
            'qtd_eleitorado' => ['required', 'integer', 'min:0'],
            'qtd_vida'       => ['required', 'integer', 'min:0'],
            'qtd_presencial' => ['required', 'integer', 'min:0'],
            'qtd_remoto'     => ['required', 'integer', 'min:' . $totalTokensAlianca],
            'justificativa'  => $eleicaoCidade->aberta || $eleicaoCidade->data_encerramento
                ? ['required', 'string', 'min:10']
                : ['nullable', 'string'],
        ], [
            'qtd_remoto.min'         => "Quantidade remoto aliança não pode ser menor que os tokens já gerados ({$totalTokensAlianca}).",
            'justificativa.min'      => 'A justificativa deve ter pelo menos 10 caracteres.',
            'justificativa.required' => 'A justificativa é obrigatória.',
        ]);

        $qtdPresencial = (int) $request->qtd_presencial;
        $qtdRemoto     = (int) $request->qtd_remoto;
        $qtdTotal      = $qtdPresencial + $qtdRemoto;

        if ($qtdTotal < $eleicaoCidade->votos_registrados) {
            return back()->withErrors([
                'qtd_presencial' => "O total aliança ({$qtdTotal}) não pode ser menor que os votos já registrados ({$eleicaoCidade->votos_registrados}).",
            ])->withInput();
        }

        $anterior = $eleicaoCidade->qtd_membros;
        $eleicaoCidade->update([
            'qtd_eleitorado' => (int) $request->qtd_eleitorado,
            'qtd_vida'       => (int) $request->qtd_vida,
            'qtd_presencial' => $qtdPresencial,
            'qtd_remoto'     => $qtdRemoto,
            'qtd_membros'    => $qtdTotal,
        ]);

        LogEleicao::registrar(
            $eleicaoCidade->eleicao_id,
            'membros_alterados',
            "Membros em {$eleicaoCidade->cidade->nome}: vida={$request->qtd_vida}, aliança={$qtdTotal} ({$qtdPresencial} presencial + {$qtdRemoto} remoto). Anterior={$anterior}."
            . ($request->justificativa ? " Justificativa: {$request->justificativa}" : '')
        );

        return redirect()->route('responsavel.index')->with('sucesso', 'Quantidade de membros atualizada.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function autorizarVida(Eleicao $eleicao): void
    {
        if (auth()->user()->perfil === 'admin') {
            return;
        }
        abort_unless(
            $eleicao->cidades()->where('cidade_id', auth()->user()->cidade_id)->exists(),
            403
        );
    }
}
