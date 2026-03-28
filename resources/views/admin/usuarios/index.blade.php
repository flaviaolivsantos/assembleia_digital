@extends('layouts.admin')
@section('page-title', 'Usuários')

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

{{-- ── Filtros ── --}}
@php
    $queryBase = array_filter(['perfil' => $perfil, 'cidade_id' => $cidadeId, 'ordenar' => $ordenar, 'direcao' => $direcao]);
    $sortDir = fn($col) => ($ordenar === $col && $direcao === 'asc') ? 'desc' : 'asc';
    $sortUrl = fn($col) => route('admin.usuarios.index', array_merge($queryBase, ['ordenar' => $col, 'direcao' => $sortDir($col)]));
    $sortIcon = fn($col) => $ordenar === $col ? ($direcao === 'asc' ? '↑' : '↓') : '↕';
@endphp

<form method="GET" action="{{ route('admin.usuarios.index') }}" class="d-flex gap-2 flex-wrap mb-3 align-items-end">
    <input type="hidden" name="ordenar"  value="{{ $ordenar }}">
    <input type="hidden" name="direcao"  value="{{ $direcao }}">

    <div>
        <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">Perfil</label>
        <select name="perfil" class="form-select form-select-sm" style="min-width:140px;">
            <option value="">Todos</option>
            @foreach(['admin' => 'Admin', 'responsavel' => 'Responsável', 'mesario' => 'Mesário', 'maquina' => 'Máquina'] as $val => $label)
                <option value="{{ $val }}" {{ $perfil === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">Missão</label>
        <select name="cidade_id" class="form-select form-select-sm" style="min-width:160px;">
            <option value="">Todas</option>
            @foreach($cidades as $cidade)
                <option value="{{ $cidade->id }}" {{ $cidadeId == $cidade->id ? 'selected' : '' }}>{{ $cidade->nome }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>

    @if($perfil || $cidadeId)
        <a href="{{ route('admin.usuarios.index', array_filter(['ordenar' => $ordenar, 'direcao' => $direcao])) }}"
           class="btn btn-sm btn-outline-secondary">Limpar</a>
    @endif
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>
                        <a href="{{ $sortUrl('nome') }}" class="text-decoration-none text-dark">
                            Nome <span class="text-muted">{{ $sortIcon('nome') }}</span>
                        </a>
                    </th>
                    <th>E-mail</th>
                    <th>
                        <a href="{{ $sortUrl('perfil') }}" class="text-decoration-none text-dark">
                            Perfil <span class="text-muted">{{ $sortIcon('perfil') }}</span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ $sortUrl('cidade') }}" class="text-decoration-none text-dark">
                            Missão <span class="text-muted">{{ $sortIcon('cidade') }}</span>
                        </a>
                    </th>
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
