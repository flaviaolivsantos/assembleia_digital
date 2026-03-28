@extends('layouts.admin')
@section('page-title', 'Mesário')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Controle de Presença</h2>
        <p class="text-muted mb-0">
            {{ $eleicaoCidade->eleicao->titulo }} — {{ $eleicaoCidade->cidade->nome }}
        </p>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@php
    $aliancaAberta = $eleicaoCidade->aberta;
    $vidaAberta    = $eleicaoCidade->eleicao->aberta_vida;
    $algumAberto   = $aliancaAberta || $vidaAberta;
@endphp

@if(!$algumAberto)
    <div class="alert alert-warning">Nenhuma votação aberta. Aguarde o responsável abrir a votação aliança ou vida.</div>
@else
    <div class="card mb-4" style="max-width: 520px;">
        <div class="card-header"><strong>Registrar Presença</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('mesario.presencas.store', $eleicaoCidade) }}">
                @csrf

                {{-- Escopo selector --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de votante</label>
                    <div class="d-flex gap-3">
                        @if($aliancaAberta)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="escopo" id="escopo_alianca"
                                   value="alianca" {{ old('escopo', 'alianca') === 'alianca' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="escopo_alianca">
                                <span class="badge bg-secondary me-1">Aliança</span>
                                Realidade de Aliança
                            </label>
                        </div>
                        @endif
                        @if($vidaAberta)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="escopo" id="escopo_vida"
                                   value="vida" {{ old('escopo') === 'vida' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="escopo_vida">
                                <span class="badge bg-primary me-1">Vida</span>
                                Realidade de Vida
                            </label>
                        </div>
                        @endif
                    </div>
                    @if(!$aliancaAberta && $vidaAberta)
                        {{-- Only vida open: pre-select vida --}}
                        <input type="hidden" name="escopo" value="vida">
                    @endif
                </div>

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
                        <label class="form-label small fw-semibold">Tipo de votante</label>
                        <select name="escopo" class="form-select form-select-sm" required>
                            @if($aliancaAberta)
                            <option value="alianca">Realidade de Aliança</option>
                            @endif
                            @if($vidaAberta)
                            <option value="vida">Realidade de Vida</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-2">
                        <input type="file" name="csv" class="form-control form-control-sm" accept=".csv,.txt" required>
                        <div class="form-text">Arquivo .csv com um nome por linha. Cabeçalho "nome" é opcional.</div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm">Importar e Gerar Tokens</button>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- Contadores --}}
@php
    $faltamGerarAlianca = $eleicaoCidade->qtd_remoto > 0
        ? max(0, $eleicaoCidade->qtd_remoto - $totalTokensAlianca)
        : 0;
    $faltamGerarVida = $eleicaoCidade->qtd_vida > 0
        ? max(0, $eleicaoCidade->qtd_vida - $totalTokensVida)
        : 0;
@endphp
<div class="row mb-3 g-2 text-center">
    @if($eleicaoCidade->qtd_vida > 0 || $totalTokensVida > 0)
    <div class="col-6 col-md-3">
        <div class="card border-primary">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold text-primary">{{ $totalTokensVida }}</div>
                <div class="text-muted small">tokens vida
                    @if($eleicaoCidade->qtd_vida > 0)(limite {{ $eleicaoCidade->qtd_vida }})@endif
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($eleicaoCidade->qtd_membros > 0 || $totalTokensAlianca > 0)
    <div class="col-6 col-md-3">
        <div class="card border-secondary">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold">{{ $totalTokensAlianca }}</div>
                <div class="text-muted small">tokens aliança
                    @if($eleicaoCidade->qtd_remoto > 0)(limite {{ $eleicaoCidade->qtd_remoto }})@endif
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($faltamGerarVida > 0 || $faltamGerarAlianca > 0)
    <div class="col-6 col-md-3">
        <div class="card border-warning">
            <div class="card-body py-2">
                @if($faltamGerarVida > 0 && $faltamGerarAlianca > 0)
                    <div class="fs-5 fw-bold text-warning">{{ $faltamGerarVida + $faltamGerarAlianca }}</div>
                    <div class="text-muted small">tokens a gerar
                        <span class="d-block" style="font-size:.75rem">{{ $faltamGerarAlianca }} aliança · {{ $faltamGerarVida }} vida</span>
                    </div>
                @elseif($faltamGerarAlianca > 0)
                    <div class="fs-5 fw-bold text-warning">{{ $faltamGerarAlianca }}</div>
                    <div class="text-muted small">tokens aliança a gerar</div>
                @else
                    <div class="fs-5 fw-bold text-warning">{{ $faltamGerarVida }}</div>
                    <div class="text-muted small">tokens vida a gerar</div>
                @endif
            </div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold text-success">{{ $presencas->where('votou', true)->count() }}</div>
                <div class="text-muted small">votaram</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2">
                <div class="fs-5 fw-bold text-warning">{{ $presencas->where('votou', false)->count() }}</div>
                <div class="text-muted small">pendentes</div>
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
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Token</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presencas as $presenca)
                    <tr>
                        <td>{{ $presenca->nome }}</td>
                        <td class="text-center">
                            @if($presenca->escopo === 'vida')
                                <span class="badge bg-primary">Vida</span>
                            @else
                                <span class="badge bg-secondary">Aliança</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!$presenca->votou && $presenca->token)
                                <span class="font-monospace token-oculto" id="token-{{ $presenca->id }}">••••••</span>
                                <button class="btn btn-link btn-sm p-0 ms-1 text-muted"
                                        onclick="toggleToken({{ $presenca->id }}, '{{ $presenca->token }}')"
                                        id="btn-{{ $presenca->id }}" title="Ver token">
                                    <i class="bi bi-eye" id="ico-{{ $presenca->id }}"></i>
                                </button>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
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
                        <td colspan="4" class="text-center text-muted py-4">Nenhum membro registrado ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function toggleToken(id, token) {
    const span = document.getElementById('token-' + id);
    const ico  = document.getElementById('ico-' + id);
    if (span.textContent === '••••••') {
        span.textContent = token;
        ico.className = 'bi bi-eye-slash';
    } else {
        span.textContent = '••••••';
        ico.className = 'bi bi-eye';
    }
}
</script>
@endpush
@endsection
