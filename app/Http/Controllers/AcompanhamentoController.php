<?php

namespace App\Http\Controllers;

use App\Models\Eleicao;

class AcompanhamentoController extends Controller
{
    public function index()
    {
        return view('acompanhamento.index', [
            'eleicoes' => $this->dados(),
        ]);
    }

    public function dados()
    {
        $user    = auth()->user();
        $isAdmin = $user->perfil === 'admin';

        $eleicoes = Eleicao::where('status', 'aberta')
            ->with(['cidades' => function ($q) use ($user, $isAdmin) {
                if (!$isAdmin) {
                    $q->where('cidade_id', $user->cidade_id);
                }
                $q->with('cidade')->orderBy('cidade_id');
            }])
            ->get();

        $dados = $eleicoes->map(function ($eleicao) {
            return [
                'titulo'  => $eleicao->titulo,
                'missoes' => $eleicao->cidades->map(function ($ec) {
                    $pct = $ec->qtd_membros > 0
                        ? round(($ec->votos_registrados / $ec->qtd_membros) * 100)
                        : 0;
                    return [
                        'nome'    => $ec->cidade->nome,
                        'membros' => $ec->qtd_membros,
                        'votaram' => $ec->votos_registrados,
                        'faltam'  => max(0, $ec->qtd_membros - $ec->votos_registrados),
                        'pct'     => $pct,
                        'status'  => $ec->aberta ? 'aberta' : ($ec->data_encerramento ? 'encerrada' : 'aguardando'),
                    ];
                })->values(),
            ];
        })->values();

        if (request()->expectsJson()) {
            return response()->json($dados);
        }

        return $dados;
    }
}
