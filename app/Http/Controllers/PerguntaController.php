<?php

namespace App\Http\Controllers;

use App\Models\Eleicao;
use App\Models\LogEleicao;
use App\Models\Pergunta;
use Illuminate\Http\Request;

class PerguntaController extends Controller
{
    public function create(Eleicao $eleicao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');
        $cidades = $eleicao->cidades()->with('cidade')->get();
        return view('admin.perguntas.create', compact('eleicao', 'cidades'));
    }

    public function store(Request $request, Eleicao $eleicao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        $request->validate([
            'pergunta'      => ['required', 'string'],
            'qtd_respostas' => ['required', 'integer', 'min:1'],
            'escopo'        => ['required', 'in:alianca,vida'],
        ]);

        $ordem = $eleicao->perguntas()->max('ordem') + 1;

        $pergunta = $eleicao->perguntas()->create([
            'pergunta'      => $request->pergunta,
            'qtd_respostas' => $request->qtd_respostas,
            'escopo'        => $request->escopo,
            'ordem'         => $ordem,
        ]);

        LogEleicao::registrar($eleicao->id, 'pergunta_adicionada', "Pergunta adicionada: \"{$request->pergunta}\".");

        // Aliança com missão selecionada: vai direto para adicionar candidatos
        if ($request->escopo === 'alianca' && $request->cidade_id) {
            return redirect()->route('admin.eleicoes.perguntas.opcoes.create', [$eleicao, $pergunta])
                ->with('cidade_id', $request->cidade_id)
                ->with('sucesso', 'Pergunta criada. Adicione os candidatos da missão selecionada.');
        }

        return redirect()->route('admin.eleicoes.show', $eleicao)->with('sucesso', 'Pergunta adicionada.');
    }

    public function edit(Eleicao $eleicao, Pergunta $pergunta)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');
        $cidades = $eleicao->cidades()->with('cidade')->get();
        return view('admin.perguntas.edit', compact('eleicao', 'pergunta', 'cidades'));
    }

    public function update(Request $request, Eleicao $eleicao, Pergunta $pergunta)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        $request->validate([
            'pergunta'      => ['required', 'string'],
            'qtd_respostas' => ['required', 'integer', 'min:1'],
            'escopo'        => ['required', 'in:alianca,vida'],
        ]);

        $pergunta->update($request->only('pergunta', 'qtd_respostas', 'escopo'));

        LogEleicao::registrar($eleicao->id, 'pergunta_atualizada', "Pergunta atualizada: \"{$request->pergunta}\".");

        return redirect()->route('admin.eleicoes.show', $eleicao)->with('sucesso', 'Pergunta atualizada.');
    }

    public function destroy(Eleicao $eleicao, Pergunta $pergunta)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        LogEleicao::registrar($eleicao->id, 'pergunta_removida', "Pergunta removida: \"{$pergunta->pergunta}\".");

        $pergunta->delete();
        return redirect()->route('admin.eleicoes.show', $eleicao)->with('sucesso', 'Pergunta removida.');
    }
}
