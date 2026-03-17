<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Illuminate\Http\Request;

class CidadeController extends Controller
{
    public function index()
    {
        $cidades = Cidade::orderBy('nome')->get();
        return view('admin.cidades.index', compact('cidades'));
    }

    public function create()
    {
        return view('admin.cidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:cidades,nome'],
        ]);

        Cidade::create($request->only('nome'));

        return redirect()->route('admin.cidades.index')->with('sucesso', 'Cidade cadastrada com sucesso.');
    }

    public function edit(Cidade $cidade)
    {
        return view('admin.cidades.edit', compact('cidade'));
    }

    public function update(Request $request, Cidade $cidade)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:cidades,nome,' . $cidade->id],
        ]);

        $cidade->update($request->only('nome'));

        return redirect()->route('admin.cidades.index')->with('sucesso', 'Cidade atualizada com sucesso.');
    }

    public function destroy(Cidade $cidade)
    {
        $cidade->delete();

        return redirect()->route('admin.cidades.index')->with('sucesso', 'Cidade removida com sucesso.');
    }
}
