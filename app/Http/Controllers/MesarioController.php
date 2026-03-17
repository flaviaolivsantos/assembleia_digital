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
        $query = EleicaoCidade::with('eleicao', 'cidade')->where('aberta', true);

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
