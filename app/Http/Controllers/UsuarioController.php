<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\LogSistema;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('cidade')->orderBy('nome')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $cidades = Cidade::orderBy('nome')->get();
        return view('admin.usuarios.create', compact('cidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'       => ['required', 'string', 'max:191'],
            'email'      => ['required', 'email', 'max:191', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:4', 'confirmed'],
            'perfil'     => ['required', 'in:admin,responsavel,mesario,maquina'],
            'cidade_id'  => ['nullable', 'exists:cidades,id'],
            'acesso_ate' => ['nullable', 'date', 'after:now'],
        ], [
            'acesso_ate.after' => 'A data/hora de expiração deve ser no futuro.',
        ]);

        User::create([
            'nome'       => $request->nome,
            'email'      => $request->email,
            'password'   => $request->password,
            'perfil'     => $request->perfil,
            'cidade_id'  => $request->cidade_id ?: null,
            'acesso_ate' => $request->acesso_ate ?: null,
        ]);

        LogSistema::registrar('usuario_criado', "Usuário \"{$request->nome}\" ({$request->perfil}) criado.");

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário criado com sucesso.');
    }

    public function edit(User $usuario)
    {
        $cidades = Cidade::orderBy('nome')->get();
        return view('admin.usuarios.edit', compact('usuario', 'cidades'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nome'       => ['required', 'string', 'max:191'],
            'email'      => ['required', 'email', 'max:191', "unique:users,email,{$usuario->id}"],
            'password'   => ['nullable', 'string', 'min:4', 'confirmed'],
            'perfil'     => ['required', 'in:admin,responsavel,mesario,maquina'],
            'cidade_id'  => ['nullable', 'exists:cidades,id'],
            'acesso_ate' => ['nullable', 'date'],
        ]);

        $dados = [
            'nome'       => $request->nome,
            'email'      => $request->email,
            'perfil'     => $request->perfil,
            'cidade_id'  => $request->cidade_id ?: null,
            'acesso_ate' => $request->acesso_ate ?: null,
        ];

        if ($request->filled('password')) {
            $dados['password'] = $request->password;
        }

        $usuario->update($dados);

        LogSistema::registrar('usuario_atualizado', "Usuário \"{$usuario->nome}\" atualizado.");

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->withErrors(['geral' => 'Você não pode excluir sua própria conta.']);
        }

        LogSistema::registrar('usuario_removido', "Usuário \"{$usuario->nome}\" removido.");
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário removido.');
    }
}
