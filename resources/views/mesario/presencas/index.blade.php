@extends('layouts.admin')
@section('page-title', 'Mesário')

@section('content')

<style>
    /* ── Cabeçalho ── */
    .mp-title     { font-family:'Montserrat',sans-serif; font-size:1.45rem; font-weight:700; color:#1B2A3B; margin:0; }
    .mp-subtitle  { font-size:.85rem; color:#6b7280; margin-top:.2rem; }

    /* ── Cards de indicadores ── */
    .mp-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: .75rem;
        margin-bottom: 1.5rem;
    }
    .mp-stat {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
        padding: 1rem 1.1rem .85rem;
        text-align: center;
    }
    .mp-stat-num  { font-family:'Montserrat',sans-serif; font-size:1.6rem; font-weight:700; color:#1B2A3B; line-height:1; }
    .mp-stat-num.turquesa { color:#00BCD4; }
    .mp-stat-label{ font-size:.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:.5px; margin-top:.35rem; }
    .mp-stat-sub  { font-size:.68rem; color:#b0b8c1; margin-top:.15rem; }

    /* ── Card registrar presença ── */
    .mp-form-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
        padding: 1.25rem 1.5rem 1.4rem;
        max-width: 540px;
        margin-bottom: 1.5rem;
    }
    .mp-form-title { font-family:'Montserrat',sans-serif; font-size:.9rem; font-weight:700; color:#1B2A3B; margin-bottom:1rem; }

    .mp-escopo-opts  { display:flex; gap:.75rem; flex-wrap:wrap; margin-bottom:1rem; }
    .mp-escopo-label {
        display:flex; align-items:center; gap:.4rem;
        font-size:.85rem; color:#374151; cursor:pointer;
        border:1.5px solid #e5e7eb; border-radius:7px; padding:.45rem .85rem;
        transition:border-color .15s, background .15s;
    }
    .mp-escopo-label input[type=radio] { accent-color:#00BCD4; }
    .mp-escopo-label:has(input:checked) { border-color:#00BCD4; background:#f0fdff; color:#0e7490; }

    .mp-input-row { display:flex; gap:.5rem; }
    .mp-input {
        flex:1; padding:.65rem .9rem;
        border:1.5px solid #d1d5db; border-radius:8px;
        font-size:.9rem; font-family:inherit; color:#111827;
        outline:none; transition:border-color .15s, box-shadow .15s;
    }
    .mp-input:focus { border-color:#00BCD4; box-shadow:0 0 0 3px rgba(0,188,212,.12); }
    .mp-input::placeholder { color:#9ca3af; }

    .mp-btn-registrar {
        padding:.65rem 1.3rem;
        background:#00BCD4; color:#fff;
        border:none; border-radius:8px;
        font-family:'Montserrat',sans-serif; font-size:.85rem; font-weight:700;
        cursor:pointer; white-space:nowrap; flex-shrink:0;
        transition:filter .15s;
    }
    .mp-btn-registrar:hover { filter:brightness(.9); }

    /* ── Tabela ── */
    .mp-table-card {
        background:#fff; border:1px solid #f0f0f0;
        border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.05);
        overflow:hidden;
    }
    .mp-table { width:100%; border-collapse:collapse; font-size:.87rem; }
    .mp-table thead tr { background:#f8f9fa; }
    .mp-table thead th {
        font-family:'Montserrat',sans-serif; font-size:.68rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.5px; color:#6b7280;
        padding:.65rem .9rem; border-bottom:1px solid #f0f0f0;
    }
    .mp-table tbody tr:hover { background:#fafafa; }
    .mp-table tbody td { padding:.6rem .9rem; border-bottom:1px solid #f5f5f5; color:#374151; vertical-align:middle; }
    .mp-table tbody tr:last-child td { border-bottom:none; }

    .badge-alianca { background:rgba(73,80,87,.1); color:#495057; font-size:.7rem; font-weight:600; padding:.2em .6em; border-radius:4px; font-family:'Montserrat',sans-serif; }
    .badge-vida    { background:rgba(0,188,212,.12); color:#0e7490; font-size:.7rem; font-weight:600; padding:.2em .6em; border-radius:4px; font-family:'Montserrat',sans-serif; }
    .badge-votou   { background:rgba(16,185,129,.12); color:#065f46; font-size:.7rem; font-weight:600; padding:.2em .6em; border-radius:4px; font-family:'Montserrat',sans-serif; }
    .badge-aguard  { background:rgba(245,158,11,.12); color:#92400e; font-size:.7rem; font-weight:600; padding:.2em .6em; border-radius:4px; font-family:'Montserrat',sans-serif; }
</style>

{{-- ── Cabeçalho ── --}}
<div class="mb-4">
    <h1 class="mp-title">Controle de Presença</h1>
    <p class="mp-subtitle">{{ $eleicaoCidade->eleicao->titulo }} — {{ $eleicaoCidade->cidade->nome }}</p>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@php
    $escopoMesario = auth()->user()->escopo_maquina ?? 'ambos';
    $aliancaAberta = $eleicaoCidade->aberta       && in_array($escopoMesario, ['ambos','alianca']);
    $vidaAberta    = $eleicaoCidade->eleicao->aberta_vida && in_array($escopoMesario, ['ambos','vida']);
    $algumAberto   = $aliancaAberta || $vidaAberta;

    $faltamGerarAlianca = $eleicaoCidade->qtd_remoto > 0
        ? max(0, $eleicaoCidade->qtd_remoto - $totalTokensAlianca) : 0;
    $faltamGerarVida = $eleicaoCidade->qtd_vida > 0
        ? max(0, $eleicaoCidade->qtd_vida - $totalTokensVida) : 0;
@endphp

{{-- ── Indicadores ── --}}
<div class="mp-stats">
    @if($eleicaoCidade->qtd_vida > 0 || $totalTokensVida > 0)
    <div class="mp-stat">
        <div class="mp-stat-num turquesa">{{ $totalTokensVida }}</div>
        <div class="mp-stat-label">Tokens Vida</div>
        @if($eleicaoCidade->qtd_vida > 0)<div class="mp-stat-sub">limite {{ $eleicaoCidade->qtd_vida }}</div>@endif
    </div>
    @endif

    @if($eleicaoCidade->qtd_membros > 0 || $totalTokensAlianca > 0)
    <div class="mp-stat">
        <div class="mp-stat-num">{{ $totalTokensAlianca }}</div>
        <div class="mp-stat-label">Tokens Aliança</div>
        @if($eleicaoCidade->qtd_remoto > 0)<div class="mp-stat-sub">limite {{ $eleicaoCidade->qtd_remoto }}</div>@endif
    </div>
    @endif

    @if($faltamGerarVida > 0 || $faltamGerarAlianca > 0)
    <div class="mp-stat">
        <div class="mp-stat-num">{{ $faltamGerarVida + $faltamGerarAlianca }}</div>
        <div class="mp-stat-label">A Gerar</div>
        @if($faltamGerarAlianca > 0 && $faltamGerarVida > 0)
            <div class="mp-stat-sub">{{ $faltamGerarAlianca }} aliança · {{ $faltamGerarVida }} vida</div>
        @endif
    </div>
    @endif

    <div class="mp-stat">
        <div class="mp-stat-num turquesa">{{ $presencas->where('votou', true)->count() }}</div>
        <div class="mp-stat-label">Votaram</div>
    </div>

    <div class="mp-stat">
        <div class="mp-stat-num">{{ $presencas->where('votou', false)->count() }}</div>
        <div class="mp-stat-label">Pendentes</div>
    </div>
</div>

{{-- ── Formulário de registro ── --}}
@if(!$algumAberto)
    <div class="alert alert-warning">Nenhuma votação aberta. Aguarde o responsável abrir a votação aliança ou vida.</div>
@else
    <div class="mp-form-card">
        <div class="mp-form-title">Registrar Presença</div>
        <form method="POST" action="{{ route('mesario.presencas.store', $eleicaoCidade) }}">
            @csrf

            @if($aliancaAberta || $vidaAberta)
            <div class="mp-escopo-opts">
                @if($aliancaAberta)
                <label class="mp-escopo-label">
                    <input type="radio" name="escopo" value="alianca"
                           {{ old('escopo', 'alianca') === 'alianca' ? 'checked' : '' }} required>
                    Realidade de Aliança
                </label>
                @endif
                @if($vidaAberta)
                <label class="mp-escopo-label">
                    <input type="radio" name="escopo" value="vida"
                           {{ old('escopo') === 'vida' ? 'checked' : '' }} {{ !$aliancaAberta ? 'required' : '' }}>
                    Realidade de Vida
                </label>
                @endif
            </div>
            @if(!$aliancaAberta && $vidaAberta)
                <input type="hidden" name="escopo" value="vida">
            @endif
            @endif

            <div class="mp-input-row">
                <input type="text" name="nome"
                    class="mp-input @error('nome') is-invalid @enderror"
                    placeholder="Nome do membro"
                    value="{{ old('nome') }}" required autofocus>
                <button type="submit" class="mp-btn-registrar">Registrar</button>
            </div>
            @error('nome')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </form>
    </div>
@endif

{{-- ── Tabela de presenças ── --}}
<div class="mp-table-card">
    <table class="mp-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th style="text-align:center;">Tipo</th>
                <th style="text-align:center;">Token</th>
                <th style="text-align:center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presencas as $presenca)
                <tr>
                    <td>{{ $presenca->nome }}</td>
                    <td style="text-align:center;">
                        @if($presenca->escopo === 'vida')
                            <span class="badge-vida">Vida</span>
                        @else
                            <span class="badge-alianca">Aliança</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if(!$presenca->votou && $presenca->token)
                            <span class="font-monospace" id="token-{{ $presenca->id }}" style="font-size:.78rem;color:#6b7280;letter-spacing:.5px;">••••••</span>
                            <button class="btn btn-link btn-sm p-0 ms-1 text-muted"
                                    onclick="toggleToken({{ $presenca->id }}, '{{ $presenca->token }}')"
                                    id="btn-{{ $presenca->id }}" title="Ver token">
                                <i class="bi bi-eye" id="ico-{{ $presenca->id }}"></i>
                            </button>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($presenca->votou)
                            <span class="badge-votou">Votou</span>
                        @else
                            <span class="badge-aguard">Aguardando</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:#9ca3af;padding:2rem;">Nenhum membro registrado ainda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
