<?php

namespace App\Http\Controllers;

use App\Models\Eleicao;
use App\Models\LogEleicao;
use App\Models\Opcao;
use App\Models\Pergunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OpcaoController extends Controller
{
    public function create(Eleicao $eleicao, Pergunta $pergunta)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');
        $cidades = $eleicao->cidades()->with('cidade')->get();
        return view('admin.opcoes.create', compact('eleicao', 'pergunta', 'cidades'));
    }

    public function store(Request $request, Eleicao $eleicao, Pergunta $pergunta)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        $isVida = $pergunta->escopo === 'vida';

        $request->validate([
            'cidade_id' => $isVida ? ['nullable'] : ['required', 'exists:cidades,id'],
            'nome'      => ['required', 'string', 'max:255'],
            'foto'      => ['nullable', 'image', 'max:2048'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('candidatos', 'public');
        }

        $cidadeId = $isVida ? null : $request->cidade_id;
        $ordem    = $pergunta->opcoes()->where('cidade_id', $cidadeId)->max('ordem') + 1;

        $pergunta->opcoes()->create([
            'cidade_id' => $cidadeId,
            'nome'      => $request->nome,
            'foto'      => $fotoPath,
            'ordem'     => $ordem,
        ]);

        LogEleicao::registrar($eleicao->id, 'candidato_adicionado', "Candidato \"{$request->nome}\" adicionado à pergunta \"{$pergunta->pergunta}\".");

        // Se veio com contexto de missão, volta para criar mais candidatos da mesma missão
        if ($cidadeId && $pergunta->escopo === 'alianca') {
            return redirect()->route('admin.eleicoes.perguntas.opcoes.create', [$eleicao, $pergunta])
                ->with('cidade_id', $cidadeId)
                ->with('sucesso', 'Candidato adicionado.');
        }

        return redirect()->route('admin.eleicoes.perguntas.opcoes.index', [$eleicao, $pergunta])
            ->with('sucesso', 'Candidato adicionado.');
    }

    public function index(Eleicao $eleicao, Pergunta $pergunta)
    {
        if ($pergunta->escopo === 'vida') {
            $opcoes = $pergunta->opcoes()->get();
            return view('admin.opcoes.index', compact('eleicao', 'pergunta', 'opcoes'));
        }

        $cidades = $eleicao->cidades()->with('cidade')->get();
        $opcoesPorCidade = [];
        foreach ($cidades as $ec) {
            $opcoesPorCidade[$ec->cidade_id] = $pergunta->opcoesPorCidade($ec->cidade_id)->get();
        }
        return view('admin.opcoes.index', compact('eleicao', 'pergunta', 'cidades', 'opcoesPorCidade'));
    }

    public function edit(Eleicao $eleicao, Pergunta $pergunta, Opcao $opcao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');
        $cidades = $eleicao->cidades()->with('cidade')->get();
        return view('admin.opcoes.edit', compact('eleicao', 'pergunta', 'opcao', 'cidades'));
    }

    public function update(Request $request, Eleicao $eleicao, Pergunta $pergunta, Opcao $opcao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        $isVida = $pergunta->escopo === 'vida';

        $request->validate([
            'cidade_id' => $isVida ? ['nullable'] : ['required', 'exists:cidades,id'],
            'nome'      => ['required', 'string', 'max:255'],
            'foto'      => ['nullable', 'image', 'max:2048'],
        ]);

        $fotoPath = $opcao->foto;
        if ($request->hasFile('foto')) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('candidatos', 'public');
        }

        $opcao->update([
            'cidade_id' => $isVida ? null : $request->cidade_id,
            'nome'      => $request->nome,
            'foto'      => $fotoPath,
        ]);

        LogEleicao::registrar($eleicao->id, 'candidato_atualizado', "Candidato \"{$request->nome}\" atualizado.");

        return redirect()->route('admin.eleicoes.perguntas.opcoes.index', [$eleicao, $pergunta])
            ->with('sucesso', 'Candidato atualizado.');
    }

    public function destroy(Eleicao $eleicao, Pergunta $pergunta, Opcao $opcao)
    {
        abort_if($eleicao->estaAberta(), 403, 'Nao e possivel editar uma eleicao aberta.');

        LogEleicao::registrar($eleicao->id, 'candidato_removido', "Candidato \"{$opcao->nome}\" removido da pergunta \"{$pergunta->pergunta}\".");

        if ($opcao->foto) {
            Storage::disk('public')->delete($opcao->foto);
        }
        $opcao->delete();

        return redirect()->route('admin.eleicoes.perguntas.opcoes.index', [$eleicao, $pergunta])
            ->with('sucesso', 'Candidato removido.');
    }
}
