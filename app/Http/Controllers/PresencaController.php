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
            ->orderBy('nome')
            ->get();

        $totalTokens = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->count();

        return view('mesario.presencas.index', compact('eleicaoCidade', 'presencas', 'totalTokens'));
    }

    public function store(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(!$eleicaoCidade->aberta, 403, 'A votacao nao esta aberta.');

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $totalTokens = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->count();

        if ($eleicaoCidade->qtd_remoto > 0 && $totalTokens >= $eleicaoCidade->qtd_remoto) {
            return back()->withErrors([
                'nome' => "Limite de votantes remotos atingido ({$eleicaoCidade->qtd_remoto}). Ajuste a configuracao de membros se necessario.",
            ])->withInput();
        }

        $resultado = TokenVotacao::gerar($eleicaoCidade->eleicao_id, $eleicaoCidade->cidade_id);

        Presenca::create([
            'eleicao_id' => $eleicaoCidade->eleicao_id,
            'cidade_id'  => $eleicaoCidade->cidade_id,
            'nome'       => $request->nome,
            'token'      => $resultado['token'],
            'votou'      => false,
        ]);

        return redirect()->route('mesario.presencas.token', [
            'eleicaoCidade' => $eleicaoCidade->id,
            'token'         => $resultado['token'],
        ]);
    }

    public function token(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);

        $token = $request->query('token');
        abort_if(!$token, 404);

        return view('mesario.presencas.token', compact('eleicaoCidade', 'token'));
    }

    public function importar(Request $request, EleicaoCidade $eleicaoCidade)
    {
        abort_if(auth()->user()->perfil !== 'admin' && $eleicaoCidade->cidade_id !== auth()->user()->cidade_id, 403);
        abort_if(!$eleicaoCidade->aberta, 403, 'A votacao nao esta aberta.');

        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:1024'],
        ], [
            'csv.mimes' => 'O arquivo deve ser .csv ou .txt.',
            'csv.max'   => 'O arquivo nao pode ultrapassar 1 MB.',
        ]);

        $linhas = array_values(array_filter(
            array_map('trim', file($request->file('csv')->getRealPath())),
            fn($l) => $l !== ''
        ));

        // Remove cabeçalho se for "nome"
        if (!empty($linhas) && strtolower($linhas[0]) === 'nome') {
            array_shift($linhas);
        }

        if (empty($linhas)) {
            return back()->withErrors(['csv' => 'O arquivo nao contem nomes validos.']);
        }

        $totalTokens = TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
            ->where('cidade_id', $eleicaoCidade->cidade_id)
            ->count();

        $disponiveis = $eleicaoCidade->qtd_remoto > 0
            ? $eleicaoCidade->qtd_remoto - $totalTokens
            : count($linhas);

        if ($disponiveis <= 0) {
            return back()->withErrors(['csv' => "Limite de votantes remotos atingido ({$eleicaoCidade->qtd_remoto}). Nenhum token gerado."]);
        }

        $linhas = array_slice($linhas, 0, $disponiveis);

        $resultados = [];
        foreach ($linhas as $nome) {
            $gerado = TokenVotacao::gerar($eleicaoCidade->eleicao_id, $eleicaoCidade->cidade_id);

            Presenca::create([
                'eleicao_id' => $eleicaoCidade->eleicao_id,
                'cidade_id'  => $eleicaoCidade->cidade_id,
                'nome'       => $nome,
                'token'      => $gerado['token'],
                'votou'      => false,
            ]);

            $resultados[] = ['nome' => $nome, 'token' => $gerado['token']];
        }

        return view('mesario.presencas.importados', compact('eleicaoCidade', 'resultados'));
    }
}
