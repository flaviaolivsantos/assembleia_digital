<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Eleicao;
use App\Models\EleicaoCidade;
use App\Models\LogEleicao;
use Illuminate\Http\Request;

class EleicaoController extends Controller
{
    public function index()
    {
        $eleicoes = Eleicao::withCount('cidades')->orderByDesc('data_eleicao')->get();
        return view('admin.eleicoes.index', compact('eleicoes'));
    }

    public function create()
    {
        $cidades = Cidade::orderBy('nome')->get();
        return view('admin.eleicoes.create', compact('cidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'       => ['required', 'string', 'max:255'],
            'data_eleicao' => ['required', 'date'],
            'cidades'      => ['required', 'array', 'min:1'],
            'cidades.*'    => ['exists:cidades,id'],
        ]);

        $eleicao = Eleicao::create([
            'titulo'       => $request->titulo,
            'data_eleicao' => $request->data_eleicao,
            'status'       => 'rascunho',
        ]);

        foreach ($request->cidades as $cidadeId) {
            EleicaoCidade::create([
                'eleicao_id' => $eleicao->id,
                'cidade_id'  => $cidadeId,
            ]);
        }

        LogEleicao::registrar($eleicao->id, 'eleicao_criada', "Eleição \"{$eleicao->titulo}\" criada.");

        return redirect()->route('admin.eleicoes.index')->with('sucesso', 'Eleicao criada com sucesso.');
    }

    public function show(Eleicao $eleicao)
    {
        $eleicao->load('cidades.cidade', 'perguntas');
        return view('admin.eleicoes.show', compact('eleicao'));
    }

    public function edit(Eleicao $eleicao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        $cidades = Cidade::orderBy('nome')->get();
        $cidadesSelecionadas = $eleicao->cidades->pluck('cidade_id')->toArray();
        return view('admin.eleicoes.edit', compact('eleicao', 'cidades', 'cidadesSelecionadas'));
    }

    public function update(Request $request, Eleicao $eleicao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        $request->validate([
            'titulo'       => ['required', 'string', 'max:255'],
            'data_eleicao' => ['required', 'date'],
            'cidades'      => ['required', 'array', 'min:1'],
            'cidades.*'    => ['exists:cidades,id'],
        ]);

        $eleicao->update([
            'titulo'       => $request->titulo,
            'data_eleicao' => $request->data_eleicao,
        ]);

        // Sincroniza cidades: remove as antigas e recria
        $eleicao->cidades()->delete();
        foreach ($request->cidades as $cidadeId) {
            EleicaoCidade::create([
                'eleicao_id' => $eleicao->id,
                'cidade_id'  => $cidadeId,
            ]);
        }

        LogEleicao::registrar($eleicao->id, 'eleicao_atualizada', "Título ou cidades alterados.");

        return redirect()->route('admin.eleicoes.show', $eleicao)->with('sucesso', 'Eleicao atualizada com sucesso.');
    }

    public function destroy(Eleicao $eleicao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel remover uma eleicao aberta.');

        $eleicao->delete();

        return redirect()->route('admin.eleicoes.index')->with('sucesso', 'Eleicao removida com sucesso.');
    }
}
