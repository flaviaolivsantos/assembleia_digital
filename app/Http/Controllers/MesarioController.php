<?php

namespace App\Http\Controllers;

use App\Models\EleicaoCidade;

class MesarioController extends Controller
{
    public function index()
    {
        $perfil   = auth()->user()->perfil;
        $cidadeId = auth()->user()->cidade_id;

        // Admin vê todas as missões; demais veem só a sua
        // Mostra cidades onde aliança OU vida estão abertas
        $query = EleicaoCidade::with('eleicao', 'cidade')
            ->where(function ($q) {
                $q->where('aberta', true)
                  ->orWhereHas('eleicao', fn($e) => $e->where('aberta_vida', true));
            })
            ->whereHas('eleicao', fn($e) => $e->where('status', 'aberta'));

        if ($perfil !== 'admin') {
            $query->where('cidade_id', $cidadeId);
        }

        $eleicoesCidade = $query->get();

        if ($perfil !== 'admin' && $eleicoesCidade->count() === 1) {
            return redirect()->route('mesario.presencas.index', $eleicoesCidade->first());
        }

        return view('dashboard.mesario', compact('eleicoesCidade'));
    }
}
