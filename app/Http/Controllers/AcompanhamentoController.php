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

    public function imprimir()
    {
        return view('acompanhamento.imprimir', [
            'eleicoes'  => $this->dados(),
            'geradoEm'  => now()->format('d/m/Y \à\s H:i:s'),
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
                'titulo'     => $eleicao->titulo,
                'tem_alianca' => $eleicao->cidades->contains('aberta', true)
                                 || $eleicao->cidades->contains(fn($ec) => $ec->data_encerramento !== null),
                'tem_vida'   => $eleicao->aberta_vida
                                || $eleicao->data_encerramento_vida !== null,
                'missoes' => $eleicao->cidades->map(function ($ec) use ($eleicao) {
                    $pct = $ec->qtd_membros > 0
                        ? round(($ec->votos_registrados / $ec->qtd_membros) * 100)
                        : 0;

                    $vidaMembros = ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0);
                    $vidaVotaram = $ec->votos_registrados_vida ?? 0;
                    $vidaPct     = $vidaMembros > 0
                        ? round(($vidaVotaram / $vidaMembros) * 100)
                        : 0;

                    return [
                        'nome'         => $ec->cidade->nome,
                        // aliança
                        'membros'      => $ec->qtd_membros,
                        'votaram'      => $ec->votos_registrados,
                        'faltam'       => max(0, $ec->qtd_membros - $ec->votos_registrados),
                        'pct'          => $pct,
                        'status'       => $ec->aberta ? 'aberta' : ($ec->data_encerramento ? 'encerrada' : 'aguardando'),
                        // vida
                        'vida_membros' => $vidaMembros,
                        'vida_votaram' => $vidaVotaram,
                        'vida_faltam'  => max(0, $vidaMembros - $vidaVotaram),
                        'vida_pct'     => $vidaPct,
                        'vida_status'  => $eleicao->aberta_vida ? 'aberta' : ($eleicao->data_encerramento_vida ? 'encerrada' : 'aguardando'),
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
