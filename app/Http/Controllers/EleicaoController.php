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

    public function duplicate(Eleicao $eleicao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel duplicar uma eleicao aberta.');

        $copia = Eleicao::create([
            'titulo'       => 'Cópia de ' . $eleicao->titulo,
            'data_eleicao' => $eleicao->data_eleicao,
            'status'       => 'rascunho',
        ]);

        // Missões
        foreach ($eleicao->cidades as $ec) {
            $copia->cidades()->create(['cidade_id' => $ec->cidade_id]);
        }

        // Perguntas e candidatos
        foreach ($eleicao->perguntas as $pergunta) {
            $novaPergunta = $copia->perguntas()->create([
                'pergunta'      => $pergunta->pergunta,
                'qtd_respostas' => $pergunta->qtd_respostas,
                'escopo'        => $pergunta->escopo,
                'ordem'         => $pergunta->ordem,
            ]);

            foreach ($pergunta->opcoes as $opcao) {
                $novaPergunta->opcoes()->create([
                    'cidade_id' => $opcao->cidade_id,
                    'nome'      => $opcao->nome,
                    'foto'      => $opcao->foto,
                    'ordem'     => $opcao->ordem,
                ]);
            }
        }

        LogEleicao::registrar($copia->id, 'eleicao_criada', "Eleição criada como cópia de \"{$eleicao->titulo}\".");

        return redirect()->route('admin.eleicoes.show', $copia)->with('sucesso', 'Eleição duplicada. Edite o título antes de usar.');
    }
}
