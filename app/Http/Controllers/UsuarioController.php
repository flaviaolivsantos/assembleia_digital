<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\LogSistema;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $perfil   = $request->input('perfil');
        $cidadeId = $request->input('cidade_id');
        $ordenar  = in_array($request->input('ordenar'), ['nome', 'perfil', 'cidade']) ? $request->input('ordenar') : 'nome';
        $direcao  = $request->input('direcao', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = User::with('cidade')
            ->leftJoin('cidades', 'users.cidade_id', '=', 'cidades.id')
            ->select('users.*');

        if ($perfil) {
            $query->where('users.perfil', $perfil);
        }
        if ($cidadeId) {
            $query->where('users.cidade_id', $cidadeId);
        }

        if ($ordenar === 'cidade') {
            $query->orderBy('cidades.nome', $direcao);
        } else {
            $query->orderBy('users.' . $ordenar, $direcao);
        }

        $usuarios = $query->get();
        $cidades  = Cidade::orderBy('nome')->get();

        return view('admin.usuarios.index', compact('usuarios', 'cidades', 'perfil', 'cidadeId', 'ordenar', 'direcao'));
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
            'nome'           => $request->nome,
            'email'          => $request->email,
            'password'       => $request->password,
            'perfil'         => $request->perfil,
            'escopo_maquina' => in_array($request->perfil, ['maquina','mesario']) ? $this->resolverEscopo($request) : 'ambos',
            'cidade_id'      => $request->perfil === 'admin' ? null : ($request->cidade_id ?: null),
            'acesso_ate'     => $request->acesso_ate ?: null,
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
            'nome'           => $request->nome,
            'email'          => $request->email,
            'perfil'         => $request->perfil,
            'escopo_maquina' => in_array($request->perfil, ['maquina','mesario']) ? $this->resolverEscopo($request) : 'ambos',
            'cidade_id'      => $request->perfil === 'admin' ? null : ($request->cidade_id ?: null),
            'acesso_ate'     => $request->acesso_ate ?: null,
        ];

        if ($request->filled('password')) {
            $dados['password'] = $request->password;
        }

        $usuario->update($dados);

        LogSistema::registrar('usuario_atualizado', "Usuário \"{$usuario->nome}\" atualizado.");

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    public function relatorio()
    {
        $usuarios = User::with('cidade')->orderBy('perfil')->orderBy('nome')->get();
        return view('admin.usuarios.relatorio', compact('usuarios'));
    }

    private function resolverEscopo(Request $request): string
    {
        $alianca = $request->boolean('escopo_alianca');
        $vida    = $request->boolean('escopo_vida');

        if ($alianca && $vida) return 'ambos';
        if ($alianca)          return 'alianca';
        if ($vida)             return 'vida';
        return 'ambos'; // fallback: nenhum marcado
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
