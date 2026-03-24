<?php

namespace App\Http\Controllers;

use App\Models\EleicaoCidade;
use App\Models\Presenca;
use App\Models\TokenVotacao;
use Illuminate\Http\Request;

class PresencaController extends Controller
{
    public function index(EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);

        $presencas = Presenca::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->orderBy('escopo')
            ->orderBy('nome')
            ->get();

        $totalTokensAlianca = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->where('escopo', 'alianca')
            ->count();

        $totalTokensVida = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->where('escopo', 'vida')
            ->count();

        $eleicaoCidade->load('eleicao');

        return view('mesario.presencas.index', compact(
            'eleicaoCidade', 'presencas', 'totalTokensAlianca', 'totalTokensVida'
        ));
    }

    public function store(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);

        $request->validate([
            'nome'   => ['required', 'string', 'max:255'],
            'escopo' => ['required', 'in:vida,alianca'],
        ]);

        $escopo  = $request->escopo;
        $eleicao = $eleicaoCidade->eleicao;

        if ($escopo === 'alianca') {
            abort_if(!$eleicaoCidade->aberta, 403, 'A votação aliança não está aberta.');

            $totalTokens = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
                ->where('cidade_id', $eleicaoCidade->cidade_id)
                ->where('escopo', 'alianca')
                ->count();

            if ($eleicaoCidade->qtd_remoto > 0 && $totalTokens >= $eleicaoCidade->qtd_remoto) {
                return back()->withErrors([
                    'nome' => "Limite de votantes remotos aliança atingido ({$eleicaoCidade->qtd_remoto}). Ajuste a configuração de membros se necessário.",
                ])->withInput();
            }
        } else {
            abort_if(!$eleicao->aberta_vida, 403, 'A votação Realidade de Vida não está aberta.');

            if ($eleicaoCidade->qtd_vida == 0) {
                return back()->withErrors([
                    'nome' => 'Esta missão não possui votantes remotos de Realidade de Vida configurados.',
                ])->withInput();
            }

            $totalTokensVida = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
                ->where('cidade_id', $eleicaoCidade->cidade_id)
                ->where('escopo', 'vida')
                ->count();

            if ($totalTokensVida >= $eleicaoCidade->qtd_vida) {
                return back()->withErrors([
                    'nome' => "Limite de votantes remotos vida atingido ({$eleicaoCidade->qtd_vida}). Ajuste a configuração de membros se necessário.",
                ])->withInput();
            }
        }

        $resultado = TokenVotacao::gerar($eleicaoCidade->eleicao_id, $eleicaoCidade->cidade_id, $escopo);

        Presenca::create([
            'eleicao_id' => $eleicaoCidade->eleicao_id,
            'cidade_id'  => $eleicaoCidade->cidade_id,
            'nome'       => $request->nome,
            'token'      => $resultado['token'],
            'escopo'     => $escopo,
            'votou'      => false,
        ]);

        return redirect()->route('mesario.presencas.token', [
            'eleicaoCidade' => $eleicaoCidade->id,
            'token'         => $resultado['token'],
            'escopo'        => $escopo,
        ]);
    }

    public function token(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);

        $token  = $request->query('token');
        $escopo = $request->query('escopo', 'alianca');
        abort_if(!$token, 404);

        return view('mesario.presencas.token', compact('eleicaoCidade', 'token', 'escopo'));
    }

    public function importar(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);

        $request->validate([
            'csv'    => ['required', 'file', 'mimes:csv,txt', 'max:1024'],
            'escopo' => ['required', 'in:vida,alianca'],
        ], [
            'csv.mimes' => 'O arquivo deve ser .csv ou .txt.',
            'csv.max'   => 'O arquivo não pode ultrapassar 1 MB.',
        ]);

        $escopo  = $request->escopo;
        $eleicao = $eleicaoCidade->eleicao;

        if ($escopo === 'alianca') {
            abort_if(!$eleicaoCidade->aberta, 403, 'A votação aliança não está aberta.');
        } else {
            abort_if(!$eleicao->aberta_vida, 403, 'A votação Realidade de Vida não está aberta.');
        }

        $linhas = array_values(array_filter(
            array_map('trim', file($request->file('csv')->getRealPath())),
            fn($l) => $l !== ''
        ));

        if (!empty($linhas) && strtolower($linhas[0]) === 'nome') {
            array_shift($linhas);
        }

        if (empty($linhas)) {
            return back()->withErrors(['csv' => 'O arquivo não contém nomes válidos.']);
        }

        $totalTokens = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->where('escopo', $escopo)
            ->count();

        $limite = $escopo === 'alianca' ? $eleicaoCidade->qtd_remoto : $eleicaoCidade->qtd_vida;

        if ($escopo === 'vida' && $limite == 0) {
            return back()->withErrors(['csv' => 'Esta missão não possui votantes remotos de Realidade de Vida configurados.']);
        }

        $disponiveis = $limite > 0 ? $limite - $totalTokens : count($linhas);

        if ($disponiveis <= 0) {
            return back()->withErrors(['csv' => "Limite de votantes remotos {$escopo} atingido ({$limite}). Nenhum token gerado."]);
        }

        $linhas     = array_slice($linhas, 0, $disponiveis);
        $resultados = [];

        foreach ($linhas as $nome) {
            $gerado = TokenVotacao::gerar($eleicaoCidade->eleicao_id, $eleicaoCidade->cidade_id, $escopo);

            Presenca::create([
                'eleicao_id' => $eleicaoCidade->eleicao_id,
                'cidade_id'  => $eleicaoCidade->cidade_id,
                'nome'       => $nome,
                'token'      => $gerado['token'],
                'escopo'     => $escopo,
                'votou'      => false,
            ]);

            $resultados[] = ['nome' => $nome, 'token' => $gerado['token']];
        }

        return view('mesario.presencas.importados', compact('eleicaoCidade', 'resultados', 'escopo'));
    }
}
