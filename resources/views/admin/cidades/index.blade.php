@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Missões</h2>
    <a href="{{ route('admin.cidades.create') }}" class="btn btn-primary">+ Nova Missão</a>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th class="text-end">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cidades as $cidade)
                    <tr>
                        <td>{{ $cidade->id }}</td>
                        <td>{{ $cidade->nome }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.cidades.edit', $cidade) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form method="POST" action="{{ route('admin.cidades.destroy', $cidade) }}" class="d-inline"
                                  onsubmit="return confirm('Remover {{ $cidade->nome }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Remover</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Nenhuma missão cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
