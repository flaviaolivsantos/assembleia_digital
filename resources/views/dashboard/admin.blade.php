@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="mb-0">Painel do Administrador</h2>
    <p class="text-muted mb-0">Bem-vindo, {{ auth()->user()->nome }}.</p>
</div>

{{-- Cards de métricas --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card h-100 metric-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="metric-label">Eleições</span>
                    <i class="bi bi-check2-square metric-icon"></i>
                </div>
                <div class="metric-value">{{ $stats['eleicoes_total'] }}</div>
                <div class="mt-2 d-flex gap-1 flex-wrap">
                    @if($stats['eleicoes_abertas'] > 0)
                        <span class="badge text-bg-success">{{ $stats['eleicoes_abertas'] }} abertas</span>
                    @endif
                    @if($stats['eleicoes_rascunho'] > 0)
                        <span class="badge text-bg-secondary">{{ $stats['eleicoes_rascunho'] }} rascunho</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card h-100 metric-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="metric-label">Missões</span>
                    <i class="bi bi-building metric-icon"></i>
                </div>
                <div class="metric-value">{{ $stats['cidades'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card h-100 metric-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="metric-label">Usuários</span>
                    <i class="bi bi-people metric-icon"></i>
                </div>
                <div class="metric-value">{{ $stats['usuarios'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Acesso rápido --}}
<h6 class="text-muted text-uppercase small mb-2 fw-semibold" style="letter-spacing:.6px">Acesso rápido</h6>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.eleicoes.index') }}" class="card text-decoration-none h-100 quick-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-list-ul quick-icon"></i>
                <div class="quick-title">Eleições</div>
                <div class="quick-sub">Listar todas</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.eleicoes.create') }}" class="card text-decoration-none h-100 quick-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-plus-circle quick-icon"></i>
                <div class="quick-title">Nova Eleição</div>
                <div class="quick-sub">Criar eleição</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.cidades.index') }}" class="card text-decoration-none h-100 quick-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-geo-alt quick-icon"></i>
                <div class="quick-title">Missões</div>
                <div class="quick-sub">Gerenciar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.usuarios.index') }}" class="card text-decoration-none h-100 quick-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-people-fill quick-icon"></i>
                <div class="quick-title">Usuários</div>
                <div class="quick-sub">Gerenciar</div>
            </div>
        </a>
    </div>
</div>

{{-- Eleições --}}
@if(session('sucesso'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('sucesso') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="text-muted text-uppercase small fw-semibold mb-0" style="letter-spacing:.6px">Eleições</h6>
    <a href="{{ route('admin.eleicoes.create') }}" class="btn btn-sm btn-primary">+ Nova Eleição</a>
</div>

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
                    @php $badges = ['rascunho' => 'secondary', 'aberta' => 'success', 'encerrada' => 'dark']; @endphp
                    <tr>
                        <td>{{ $eleicao->titulo }}</td>
                        <td class="text-muted small">{{ $eleicao->data_eleicao->format('d/m/Y') }}</td>
                        <td>
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
                        <td colspan="5" class="text-center text-muted py-4">
                            Nenhuma eleição cadastrada.
                            <a href="{{ route('admin.eleicoes.create') }}">Criar a primeira</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
