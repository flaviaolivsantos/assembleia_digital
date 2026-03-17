@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Controle de Presenca</h2>
        <p class="text-muted mb-0">
            {{ $eleicaoCidade->eleicao->titulo }} &mdash; {{ $eleicaoCidade->cidade->nome }}
        </p>
    </div>
    <div class="text-end">
        <div class="fs-4 fw-bold">{{ $presencas->where('votou', false)->count() }}</div>
        <div class="text-muted small">aguardando votar</div>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@if(!$eleicaoCidade->aberta)
    <div class="alert alert-warning">A votacao nao esta aberta. Aguarde o responsavel local abrir a votacao.</div>
@else
    <div class="card mb-4" style="max-width: 480px;">
        <div class="card-header"><strong>Registrar Presenca</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('mesario.presencas.store', $eleicaoCidade) }}">
                @csrf
                <div class="d-flex gap-2">
                    <input type="text" name="nome"
                        class="form-control @error('nome') is-invalid @enderror"
                        placeholder="Nome do membro" value="{{ old('nome') }}" required autofocus>
                    <button type="submit" class="btn btn-primary flex-shrink-0">Registrar</button>
                </div>
                @error('nome')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </form>
        </div>
        <div class="card-footer">
            <a class="text-muted small text-decoration-none" data-bs-toggle="collapse" href="#form-importar">
                Importar lista via CSV
            </a>
            <div class="collapse mt-3" id="form-importar">
                @error('csv')
                    <div class="alert alert-danger py-2 small">{{ $message }}</div>
                @enderror
                <form method="POST" action="{{ route('mesario.presencas.importar', $eleicaoCidade) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <input type="file" name="csv" class="form-control form-control-sm" accept=".csv,.txt" required>
                        <div class="form-text">Arquivo .csv com um nome por linha. Cabecalho "nome" e opcional.</div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm">Importar e Gerar Tokens</button>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- Contadores de membros configurados --}}
@if($eleicaoCidade->qtd_membros > 0)
<div class="row mb-3 g-2 text-center">
    <div class="col-6">
        <div class="card border-secondary">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold">{{ $eleicaoCidade->qtd_presencial }}</div>
                <div class="text-muted small">presencial (limite)</div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card border-primary">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold text-primary">{{ $eleicaoCidade->qtd_remoto }}</div>
                <div class="text-muted small">remoto (limite) &bull; {{ $totalTokens }} tokens gerados</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Contadores de votação --}}
<div class="row mb-3 g-2 text-center">
    <div class="col-4">
        <div class="card">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold">{{ $presencas->count() }}</div>
                <div class="text-muted small">tokens gerados</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold text-success">{{ $presencas->where('votou', true)->count() }}</div>
                <div class="text-muted small">remotos votaram</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold text-warning">{{ $presencas->where('votou', false)->count() }}</div>
                <div class="text-muted small">tokens pendentes</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presencas as $presenca)
                    <tr>
                        <td>{{ $presenca->nome }}</td>
                        <td class="text-center">
                            @if($presenca->votou)
                                <span class="badge text-bg-success">Votou</span>
                            @else
                                <span class="badge text-bg-warning text-dark">Aguardando</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">Nenhum membro registrado ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
