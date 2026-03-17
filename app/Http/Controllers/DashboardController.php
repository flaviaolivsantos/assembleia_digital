<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Eleicao;
use App\Models\EleicaoCidade;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $perfil = auth()->user()->perfil;

        if ($perfil === 'admin') {
            $stats = [
                'eleicoes_total'    => Eleicao::count(),
                'eleicoes_abertas'  => Eleicao::where('status', 'aberta')->count(),
                'eleicoes_rascunho' => Eleicao::where('status', 'rascunho')->count(),
                'cidades'           => Cidade::count(),
                'usuarios'          => User::count(),
            ];

            $eleicoes_recentes = Eleicao::orderByDesc('created_at')->limit(5)->get();

            return view('dashboard.admin', compact('stats', 'eleicoes_recentes'));
        }

        if ($perfil === 'mesario') {
            $eleicoesCidade = EleicaoCidade::with('eleicao')
                ->where('cidade_id', auth()->user()->cidade_id)
                ->where('aberta', true)
                ->get();

            if ($eleicoesCidade->count() === 1) {
                return redirect()->route('mesario.presencas.index', $eleicoesCidade->first());
            }

            return view('dashboard.mesario', compact('eleicoesCidade'));
        }

        return match ($perfil) {
            'responsavel' => view('dashboard.responsavel'),
            'maquina'     => view('dashboard.maquina'),
            default       => abort(403, 'Perfil inválido.'),
        };
    }
}
