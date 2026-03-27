<?php

namespace App\Http\Controllers;

use App\Models\EleicaoCidade;
use App\Models\Eleicao;
use App\Models\LogEleicao;
use App\Models\LogSistema;
use App\Models\Voto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResponsavelController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->perfil === 'admin') {
            $eleicoes = Eleicao::with(['cidades.cidade', 'cidades.abertaPor', 'cidades.encerradaPor', 'perguntas'])
                ->whereIn('status', ['rascunho', 'aberta', 'encerrada'])
                ->orderByDesc('data_eleicao')
                ->get();
        } else {
            $eleicoes = Eleicao::with(['cidades' => function ($q) use ($user) {
                $q->where('cidade_id', $user->cidade_id)
                  ->with(['cidade', 'abertaPor', 'encerradaPor']);
            }, 'perguntas'])
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

        return redirect()->route('responsavel.zeresima.alianca', $eleicaoCidade);
    }

    public function zeresimaAlianca(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        $eleicaoCidade->load('cidade', 'abertaPor');
        $eleicao = $eleicaoCidade->eleicao;
        $eleicao->load('perguntas.opcoes');
        return view('responsavel.zeresima', [
            'eleicao'       => $eleicao,
            'eleicaoCidade' => $eleicaoCidade,
            'escopo'        => 'alianca',
        ]);
    }

    public function zeresimaVida(Eleicao $eleicao)
    {
        $eleicao->load('perguntas.opcoes', 'cidades.cidade');
        return view('responsavel.zeresima', [
            'eleicao'       => $eleicao,
            'eleicaoCidade' => $eleicao->cidades->first(),
            'escopo'        => 'vida',
        ]);
    }

    public function relatorios(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);

        $eleicao = $eleicaoCidade->eleicao;
        $eleicao->load('perguntas', 'cidades.cidade');

        $perguntaIds = $eleicao->perguntas->pluck('id');

        $votos = Voto::whereIn('pergunta_id', $perguntaIds)
            ->with(['pergunta', 'opcao', 'maquina'])
            ->orderBy('created_at')
            ->get();

        $todasCidades = $eleicao->cidades;

        $logsEleicao = LogEleicao::where('eleicao_id', $eleicao->id)
            ->with('usuario')
            ->orderBy('created_at')
            ->get();

        $logsSistema = LogSistema::with('usuario')
            ->orderByDesc('created_at')
            ->get();

        return view('responsavel.relatorios', compact('eleicao', 'eleicaoCidade', 'votos', 'todasCidades', 'logsEleicao', 'logsSistema'));
    }

    public function relatoriosImprimir(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);

        $eleicao = $eleicaoCidade->eleicao;
        $eleicao->load('perguntas', 'cidades.cidade');

        $perguntaIds = $eleicao->perguntas->pluck('id');

        $votos = Voto::whereIn('pergunta_id', $perguntaIds)
            ->with(['pergunta', 'opcao', 'maquina'])
            ->orderBy('created_at')
            ->get();

        $filtro = request('filtro', 'todos');

        return view('responsavel.relatorios-imprimir', compact('eleicao', 'eleicaoCidade', 'votos', 'filtro'));
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

        return redirect()->route('responsavel.zeresima.vida', $eleicao);
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

    // ─── Reabrir (admin only) ─────────────────────────────────────────

    public function reabrirAlianca(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin', 403);
        abort_if($eleicaoCidade->aberta, 403, 'Votação aliança já está aberta.');

        return view('responsavel.reabrir_alianca', compact('eleicaoCidade'));
    }

    public function confirmarReobrirAlianca(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin', 403);
        abort_if($eleicaoCidade->aberta, 403, 'Votação aliança já está aberta.');

        $request->validate([
            'senha'         => ['required'],
            'justificativa' => ['required', 'string', 'min:10'],
        ]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicaoCidade->update([
            'aberta'            => true,
            'data_encerramento' => null,
            'encerrada_por'     => null,
        ]);

        $eleicaoCidade->eleicao->update(['status' => 'aberta']);

        LogEleicao::registrar(
            $eleicaoCidade->eleicao_id,
            'votacao_reaberta',
            "Votação aliança REABERTA em {$eleicaoCidade->cidade->nome} pelo administrador. Motivo: {$request->justificativa}"
        );

        return redirect()->route('responsavel.index')->with('sucesso', 'Votação aliança reaberta.');
    }

    public function reabrirVida(Eleicao $eleicao)
    {
        abort_if(auth()->user()->perfil !== 'admin', 403);
        abort_if($eleicao->aberta_vida, 403, 'Votação vida já está aberta.');

        return view('responsavel.reabrir_vida', compact('eleicao'));
    }

    public function confirmarReobrirVida(Request $request, Eleicao $eleicao)
    {
        abort_if(auth()->user()->perfil !== 'admin', 403);
        abort_if($eleicao->aberta_vida, 403, 'Votação vida já está aberta.');

        $request->validate([
            'senha'         => ['required'],
            'justificativa' => ['required', 'string', 'min:10'],
        ]);

        if (!Hash::check($request->senha, auth()->user()->password)) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $eleicao->update([
            'aberta_vida'            => true,
            'data_encerramento_vida' => null,
            'encerrada_por_vida'     => null,
            'status'                 => 'aberta',
        ]);

        LogEleicao::registrar(
            $eleicao->id,
            'vida_reaberta',
            "Votação Realidade de Vida REABERTA pelo administrador. Motivo: {$request->justificativa}"
        );

        return redirect()->route('responsavel.index')->with('sucesso', 'Votação Realidade de Vida reaberta.');
    }

    // ─── Membros ──────────────────────────────────────────────────────

    public function editarMembros(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->data_encerramento, 403, 'Votação aliança já encerrada.');

        return view('responsavel.membros', compact('eleicaoCidade'));
    }

    public function atualizarMembros(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->data_encerramento, 403, 'Votação aliança já encerrada.');

        $totalTokensAlianca = \App\Models\TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->where('escopo', 'alianca')
            ->count();

        $totalTokensVida = \App\Models\TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->where('escopo', 'vida')
            ->count();

        $request->validate([
            'qtd_eleitorado'      => ['required', 'integer', 'min:0'],
            'qtd_presencial_vida' => ['required', 'integer', 'min:' . $eleicaoCidade->votos_presenciais_vida],
            'qtd_vida'            => ['required', 'integer', 'min:' . $totalTokensVida],
            'qtd_presencial'      => ['required', 'integer', 'min:0'],
            'qtd_remoto'          => ['required', 'integer', 'min:' . $totalTokensAlianca],
            'justificativa'       => $eleicaoCidade->aberta || $eleicaoCidade->data_encerramento
                ? ['required', 'string', 'min:10']
                : ['nullable', 'string'],
        ], [
            'qtd_presencial_vida.min' => "Quantidade presencial vida não pode ser menor que os votos presenciais já registrados ({$eleicaoCidade->votos_presenciais_vida}).",
            'qtd_vida.min'            => "Quantidade remoto vida não pode ser menor que os tokens já gerados ({$totalTokensVida}).",
            'qtd_remoto.min'          => "Quantidade remoto aliança não pode ser menor que os tokens já gerados ({$totalTokensAlianca}).",
            'justificativa.min'       => 'A justificativa deve ter pelo menos 10 caracteres.',
            'justificativa.required'  => 'A justificativa é obrigatória.',
        ]);

        $qtdPresencialVida = (int) $request->qtd_presencial_vida;
        $qtdVida           = (int) $request->qtd_vida;
        $qtdTotalVida      = $qtdPresencialVida + $qtdVida;
        $qtdPresencial     = (int) $request->qtd_presencial;
        $qtdRemoto         = (int) $request->qtd_remoto;
        $qtdTotal          = $qtdPresencial + $qtdRemoto;

        if ($qtdTotalVida < $eleicaoCidade->votos_registrados_vida) {
            return back()->withErrors([
                'qtd_presencial_vida' => "O total vida ({$qtdTotalVida}) não pode ser menor que os votos vida já registrados ({$eleicaoCidade->votos_registrados_vida}).",
            ])->withInput();
        }

        if ($qtdTotal < $eleicaoCidade->votos_registrados) {
            return back()->withErrors([
                'qtd_presencial' => "O total aliança ({$qtdTotal}) não pode ser menor que os votos já registrados ({$eleicaoCidade->votos_registrados}).",
            ])->withInput();
        }

        $anterior = $eleicaoCidade->qtd_membros;
        $eleicaoCidade->update([
            'qtd_eleitorado'      => (int) $request->qtd_eleitorado,
            'qtd_presencial_vida' => $qtdPresencialVida,
            'qtd_vida'            => $qtdVida,
            'qtd_presencial'      => $qtdPresencial,
            'qtd_remoto'          => $qtdRemoto,
            'qtd_membros'         => $qtdTotal,
        ]);

        LogEleicao::registrar(
            $eleicaoCidade->eleicao_id,
            'membros_alterados',
            "Membros em {$eleicaoCidade->cidade->nome}: vida presencial={$qtdPresencialVida}, vida remoto={$qtdVida}; aliança={$qtdTotal} ({$qtdPresencial} presencial + {$qtdRemoto} remoto). Anterior aliança={$anterior}."
            . ($request->justificativa ? " Justificativa: {$request->justificativa}" : '')
        );

        LogSistema::registrar('membros_atualizados', "Membros atualizados para {$eleicaoCidade->cidade->nome}.");

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
