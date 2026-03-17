<?php

namespace App\Http\Controllers;

use App\Models\Eleicao;

class LogController extends Controller
{
    public function index(Eleicao $eleicao)
    {
        $logs = $eleicao->logs()->with('usuario')->orderByDesc('created_at')->get();
        return view('admin.eleicoes.logs', compact('eleicao', 'logs'));
    }
}
