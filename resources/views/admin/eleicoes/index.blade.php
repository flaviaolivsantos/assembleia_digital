@extends('layouts.admin')
@section('page-title', 'Eleições')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Eleições</h2>
    <a href="{{ route('admin.eleicoes.create') }}" class="btn btn-primary">+ Nova Eleição</a>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Missões</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eleicoes as $eleicao)
                    <tr>
                        <td>{{ $eleicao->titulo }}</td>
                        <td>{{ $eleicao->data_eleicao->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $badges = ['rascunho' => 'secondary', 'aberta' => 'success', 'encerrada' => 'dark'];
                            @endphp
                            <span class="badge text-bg-{{ $badges[$eleicao->status] ?? 'secondary' }}">
                                {{ ucfirst($eleicao->status) }}
                            </span>
                        </td>
                        <td>{{ $eleicao->cidades_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                            @if(!$eleicao->estaAberta())
                                <a href="{{ route('admin.eleicoes.edit', $eleicao) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <form method="POST" action="{{ route('admin.eleicoes.duplicate', $eleicao) }}" class="d-inline"
                                      onsubmit="return confirm('Duplicar {{ $eleicao->titulo }}?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-info">Duplicar</button>
                                </form>
                                <form method="POST" action="{{ route('admin.eleicoes.destroy', $eleicao) }}" class="d-inline"
                                      onsubmit="return confirm('Remover {{ $eleicao->titulo }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remover</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Nenhuma eleição cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
