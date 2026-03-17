@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Usuários</h2>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">+ Novo Usuário</a>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Missão</th>
                    <th>Acesso até</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            {{ $usuario->nome }}
                            @if($usuario->id === auth()->id())
                                <span class="badge text-bg-secondary ms-1">você</span>
                            @endif
                        </td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @php
                                $cores = ['admin' => 'danger', 'responsavel' => 'primary', 'mesario' => 'warning', 'maquina' => 'secondary'];
                            @endphp
                            <span class="badge text-bg-{{ $cores[$usuario->perfil] ?? 'secondary' }}">
                                {{ ucfirst($usuario->perfil) }}
                            </span>
                        </td>
                        <td>{{ $usuario->cidade->nome ?? '—' }}</td>
                        <td>
                            @if($usuario->acesso_ate)
                                @if(now()->isAfter($usuario->acesso_ate))
                                    <span class="badge text-bg-danger" title="{{ $usuario->acesso_ate->format('d/m/Y H:i') }}">Expirado</span>
                                @else
                                    <span class="text-muted small">{{ $usuario->acesso_ate->format('d/m/Y H:i') }}</span>
                                @endif
                            @else
                                <span class="text-muted small">Ilimitado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            @if($usuario->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
                                      class="d-inline" onsubmit="return confirm('Remover este usuário?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remover</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Nenhum usuário cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
