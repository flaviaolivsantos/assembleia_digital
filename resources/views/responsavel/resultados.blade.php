@extends('layouts.app')

@section('content')

@php
    $mostrarAlianca  = $temAlianca && ($filtro === 'geral' || $filtro === 'alianca');
    $mostrarVida     = $temVida    && ($filtro === 'geral' || $filtro === 'vida');

    // Métricas de aliança para os gráficos: geral → soma todas cidades; aliança → cidade selecionada
    $compareceram = $filtro === 'geral'
        ? $todasCidades->sum('qtd_membros')
        : $aliancaCidade->qtd_membros;
    $votaram      = $filtro === 'geral'
        ? $todasCidades->sum('votos_registrados')
        : $aliancaCidade->votos_registrados;
    $pctAderencia = $compareceram > 0 ? round($votaram / $compareceram * 100, 1) : 0;
    $pctAproveit  = $compareceram > 0 ? round($votaram / $compareceram * 100, 1) : 0;

    // Vida é nacional — soma todas as cidades
    $vidaEleitores   = $todasCidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0));
    $vidaVotaram     = array_sum($vidaVotaramPorCidade);
    $pctVidaAproveit = $vidaEleitores > 0 ? round($vidaVotaram / $vidaEleitores * 100, 1) : 0;

    // Auditoria por máquina: vida → todas; aliança → filtra pela cidade aliança selecionada
    $maquinasDaMissao = match($filtro) {
        'alianca' => $votosPorMaquina->filter(fn($r) => $r->maquina?->cidade_id === $aliancaCidade->cidade_id),
        'vida'    => $votosPorMaquina,
        default   => $temVida
            ? $votosPorMaquina
            : $votosPorMaquina->filter(fn($r) => $r->maquina?->cidade_id === $aliancaCidade->cidade_id),
    };

    // Total de votos remotos a exibir no grid
    $remotosTotal = ($mostrarAlianca ? $votosRemotosAlianca : 0)
                  + ($mostrarVida    ? $votosRemotosVida    : 0);

    // Indica resultado parcial (seção visível ainda não encerrada)
    $parcialAlianca   = $mostrarAlianca && !$aliancaCidade->data_encerramento;
    $parcialVida      = $mostrarVida    && !$eleicao->data_encerramento_vida;
    $resultadoParcial = $parcialAlianca || $parcialVida;
@endphp

<style>
    /* --- Dashboard de Resultados -------------------------------- */
    .dash-header-title { font-family: 'Montserrat', sans-serif; font-weight: 700; color: #2C3E50; margin-bottom: .1rem; }
    .dash-header-sub   { font-size: .9rem; color: #6c757d; }

    /* Badge parcial */
    .badge-parcial {
        font-family: 'Montserrat', sans-serif; font-size: .68rem; font-weight: 700;
        letter-spacing: .4px; text-transform: uppercase;
        padding: .25em .65em; border-radius: 2rem; vertical-align: middle;
        background: rgba(255,193,7,.2); color: #856404;
        border: 1px solid rgba(255,193,7,.4);
        display: inline-flex; align-items: center; gap: 4px;
    }

    /* Barra de filtro */
    .res-filter-bar { display: flex; gap: .5rem; flex-wrap: wrap; }
    .res-filter-btn {
        font-family: 'Montserrat', sans-serif; font-size: .75rem; font-weight: 600;
        letter-spacing: .3px; text-transform: uppercase;
        padding: .4rem .9rem; border-radius: 2rem;
        border: 1.5px solid #CED4DA; background: #fff; color: #495057;
        text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        transition: all .15s;
    }
    .res-filter-btn:hover { border-color: #00BCD4; color: #00BCD4; }
    .res-filter-btn.active { background: #2C3E50; border-color: #2C3E50; color: #fff; }

    /* Cards de métrica */
    .res-metric-card {
        background: linear-gradient(135deg, #F8F9FA 0%, #FFFFFF 100%);
        border: none !important; border-radius: .75rem !important;
        box-shadow: 0 4px 12px rgba(0,0,0,.08) !important;
        transition: transform .15s, box-shadow .15s;
    }
    .res-metric-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.12) !important; }
    .res-metric-icon {
        width: 52px; height: 52px; border-radius: 50%;
        background: rgba(0, 188, 212, .1);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; color: #00BCD4; flex-shrink: 0;
    }
    .res-metric-value {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.4rem; font-weight: 700; color: #2C3E50; line-height: 1;
    }
    .res-metric-label {
        font-family: 'Montserrat', sans-serif;
        font-size: .82rem; font-weight: 600; color: #495057;
        text-transform: uppercase; letter-spacing: .4px; margin-top: .25rem;
    }

    /* Cards de gráfico */
    .chart-card { border: none !important; border-radius: .75rem !important; box-shadow: 0 4px 12px rgba(0,0,0,.08) !important; }
    .chart-card .card-header {
        background: transparent; border-bottom: 1px solid #F0F2F5;
        font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: .9rem;
        color: #2C3E50; padding: 1rem 1.25rem .75rem;
    }
    .chart-wrap { position: relative; width: 200px; height: 200px; margin: 0 auto; }
    .chart-center-label {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center; pointer-events: none;
    }
    .chart-center-pct { font-family: 'Montserrat', sans-serif; font-size: 2rem; font-weight: 700; color: #2C3E50; line-height: 1; }
    .chart-center-sub { font-size: .75rem; color: #495057; margin-top: .2rem; }
    .chart-legend { font-size: .82rem; color: #495057; text-align: center; margin-top: .75rem; }
    .chart-legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; }

    /* Tabelas estilizadas */
    .res-table { border-collapse: collapse; width: 100%; }
    .res-table thead tr { background-color: #2C3E50 !important; }
    .res-table thead th {
        color: #fff !important; font-family: 'Montserrat', sans-serif;
        font-size: .78rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: .5px; padding: .65rem .85rem; border: none !important;
    }
    .res-table tbody tr:nth-child(odd)  { background-color: #fff; }
    .res-table tbody tr:nth-child(even) { background-color: #F8F9FA; }
    .res-table tbody tr:hover { background-color: rgba(0,188,212,.05) !important; }
    .res-table tbody td { padding: .6rem .85rem; border: 1px solid #dee2e6; font-size: .9rem; color: #495057; vertical-align: middle; }
    .res-table tfoot td { padding: .6rem .85rem; border: 1px solid #dee2e6; background: #F8F9FA; font-size: .88rem; }

    /* Linha vencedora */
    .res-table tbody tr.winner td { background-color: rgba(0,188,212,.08) !important; }
    .res-table tbody tr.winner td:first-child { border-left: 3px solid #00BCD4; }
    .winner-name { color: #00BCD4; font-weight: 600; }

    /* Barra de progresso inline */
    .mini-bar { display: flex; align-items: center; gap: 6px; }
    .mini-bar .progress { height: 6px; flex: 1; background: #e9ecef; border-radius: 4px; }
    .mini-bar .progress-bar { background: #00BCD4; border-radius: 4px; transition: width .6s ease; }
    .mini-bar small { min-width: 38px; text-align: right; font-size: .78rem; color: #6c757d; }

    /* Seção título */
    .section-heading { font-family: 'Montserrat', sans-serif; font-size: .78rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .8px; margin-bottom: .75rem; }

    /* Badge de tipo */
    .badge-vida     { background: rgba(0,188,212,.15); color: #00899e; }
    .badge-alianca  { background: rgba(73,80,87,.12);  color: #495057; }
    .badge-tipo { font-family: 'Montserrat', sans-serif; font-size: .72rem; font-weight: 600; padding: .3em .7em; border-radius: 4px; text-transform: uppercase; letter-spacing: .3px; }
</style>

{{-- ── Header ────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h2 class="dash-header-title mb-1">
            Resultados — {{ $eleicao->titulo }}
            @if($resultadoParcial)
                <span class="badge-parcial ms-2"><i class="bi bi-hourglass-split"></i>Resultados Parciais</span>
            @endif
        </h2>
        <span class="dash-header-sub">
            @if($filtro === 'alianca')
                <i class="bi bi-geo-alt me-1"></i>{{ $aliancaCidade->cidade->nome }}
                &nbsp;&middot;&nbsp;
            @endif
            <i class="bi bi-calendar3 me-1"></i>{{ $eleicao->data_eleicao->format('d/m/Y') }}
        </span>
    </div>
    <div class="d-flex gap-2 flex-shrink-0">
        <a href="{{ route('responsavel.ata', $eleicaoCidade) }}?filtro={{ $filtro }}{{ $filtro === 'alianca' ? '&alianca_cidade_id='.$aliancaCidade->cidade_id : '' }}"
           class="btn btn-outline-dark btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i>Imprimir Ata
        </a>
        <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

{{-- ── Barra de Filtro ──────────────────────────────────────── --}}
<div class="res-filter-bar mb-4">
    @if($temAlianca && $temVida)
    <a href="{{ route('responsavel.resultados', $eleicaoCidade) }}?filtro=geral"
       class="res-filter-btn {{ $filtro === 'geral' ? 'active' : '' }}">
        <i class="bi bi-grid-3x3-gap"></i>Tudo
    </a>
    @endif
    @if($temAlianca)
        @foreach($todasCidades as $ec)
        <a href="{{ route('responsavel.resultados', $eleicaoCidade) }}?filtro=alianca&alianca_cidade_id={{ $ec->cidade_id }}"
           class="res-filter-btn {{ $filtro === 'alianca' && $aliancaCidade->cidade_id === $ec->cidade_id ? 'active' : '' }}">
            <i class="bi bi-building"></i>Aliança — {{ $ec->cidade->nome }}
        </a>
        @endforeach
    @endif
    @if($temVida)
    <a href="{{ route('responsavel.resultados', $eleicaoCidade) }}?filtro=vida"
       class="res-filter-btn {{ $filtro === 'vida' ? 'active' : '' }}">
        <i class="bi bi-globe2"></i>Realidade de Vida
    </a>
    @endif
</div>

{{-- ── Cards de Métricas ──────────────────────────────────────── --}}
@if($mostrarAlianca)
@php $cidadesAlianca = $filtro === 'geral' ? $todasCidades : collect([$aliancaCidade]); @endphp
@foreach($cidadesAlianca as $cAlianca)
@php
    $aAptos       = $cAlianca->qtd_membros;
    $aVotaram     = $cAlianca->votos_registrados;
    $aPctAproveit = $aAptos > 0 ? round($aVotaram / $aAptos * 100, 1) : 0;
@endphp
<p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.5px">
    <span class="badge text-bg-secondary me-1">Aliança</span>{{ $cAlianca->cidade->nome }}
</p>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="res-metric-value">{{ $aAptos }}</div>
                    <div class="res-metric-label">Eleitores Aptos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon" style="background:rgba(220,53,69,.1);color:#dc3545;"><i class="bi bi-person-x-fill"></i></div>
                <div>
                    <div class="res-metric-value" style="color:#dc3545;">{{ max(0, $aAptos - $aVotaram) }}</div>
                    <div class="res-metric-label">Faltaram</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="res-metric-value">{{ $aVotaram }}</div>
                    <div class="res-metric-label">Votaram</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon"><i class="bi bi-percent"></i></div>
                <div>
                    <div class="res-metric-value">{{ $aPctAproveit }}%</div>
                    <div class="res-metric-label">Aproveitamento</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

@if($mostrarVida)
<p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.5px">
    <span class="badge text-bg-primary me-1">Vida</span> — todas as missões
</p>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="res-metric-value">{{ $vidaEleitores }}</div>
                    <div class="res-metric-label">Eleitores Aptos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon" style="background:rgba(220,53,69,.1);color:#dc3545;"><i class="bi bi-person-x-fill"></i></div>
                <div>
                    <div class="res-metric-value" style="color:#dc3545;">{{ max(0, $vidaEleitores - $vidaVotaram) }}</div>
                    <div class="res-metric-label">Faltaram</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="res-metric-value">{{ $vidaVotaram }}</div>
                    <div class="res-metric-label">Votaram</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card res-metric-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="res-metric-icon"><i class="bi bi-percent"></i></div>
                <div>
                    <div class="res-metric-value">{{ $pctVidaAproveit }}%</div>
                    <div class="res-metric-label">Aproveitamento</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Gráficos de Rosca ──────────────────────────────────────── --}}
@if($mostrarAlianca)
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Aderência <span class="badge text-bg-secondary ms-1" style="font-size:.65rem;">Aliança{{ $filtro === 'geral' ? ' — Todas' : ' — '.$aliancaCidade->cidade->nome }}</span></div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                <div class="chart-wrap">
                    <canvas id="chartAderencia"></canvas>
                    <div class="chart-center-label">
                        <div class="chart-center-pct">{{ $pctAderencia }}%</div>
                        <div class="chart-center-sub">Aderência</div>
                    </div>
                </div>
                <div class="chart-legend mt-3">
                    <span><span class="chart-legend-dot" style="background:#00BCD4"></span>Votaram ({{ $votaram }})</span>
                    &nbsp;&nbsp;
                    <span><span class="chart-legend-dot" style="background:#CED4DA"></span>Não votaram ({{ $compareceram - $votaram }})</span>
                </div>
                <p class="text-muted small mt-2 mb-0 text-center">Dos membros de aliança, quantos votaram.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Aproveitamento <span class="badge text-bg-secondary ms-1" style="font-size:.65rem;">Aliança{{ $filtro === 'geral' ? ' — Todas' : ' — '.$aliancaCidade->cidade->nome }}</span></div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                <div class="chart-wrap">
                    <canvas id="chartAproveitamento"></canvas>
                    <div class="chart-center-label">
                        <div class="chart-center-pct">{{ $pctAproveit }}%</div>
                        <div class="chart-center-sub">Aproveitamento</div>
                    </div>
                </div>
                <div class="chart-legend mt-3">
                    <span><span class="chart-legend-dot" style="background:#00BCD4"></span>Votaram ({{ $votaram }})</span>
                    &nbsp;&nbsp;
                    <span><span class="chart-legend-dot" style="background:#CED4DA"></span>Não votaram ({{ $compareceram - $votaram }})</span>
                </div>
                <p class="text-muted small mt-2 mb-0 text-center">Dos que compareceram, quantos efetivamente votaram.</p>
            </div>
        </div>
    </div>
</div>
@endif

@if($mostrarVida && $vidaEleitores > 0)
<div class="row g-3 mb-4">
    <div class="col-md-6 offset-md-3">
        <div class="card chart-card h-100">
            <div class="card-header"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Aproveitamento <span class="badge text-bg-primary ms-1" style="font-size:.65rem;">Vida</span></div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                <div class="chart-wrap">
                    <canvas id="chartVidaAproveitamento"></canvas>
                    <div class="chart-center-label">
                        <div class="chart-center-pct">{{ $pctVidaAproveit }}%</div>
                        <div class="chart-center-sub">Aproveitamento</div>
                    </div>
                </div>
                <div class="chart-legend mt-3">
                    <span><span class="chart-legend-dot" style="background:#00BCD4"></span>Votaram ({{ $vidaVotaram }})</span>
                    &nbsp;&nbsp;
                    <span><span class="chart-legend-dot" style="background:#CED4DA"></span>Não votaram ({{ $vidaEleitores - $vidaVotaram }})</span>
                </div>
                <p class="text-muted small mt-2 mb-0 text-center">Dos membros vida, quantos efetivamente votaram.</p>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Auditoria por Máquina ──────────────────────────────────── --}}
@if($maquinasDaMissao->isNotEmpty() || $remotosTotal > 0)
<div class="card mb-4" style="border-radius:.75rem!important;border:none!important;box-shadow:0 4px 12px rgba(0,0,0,.08)!important;">
    <div class="card-header d-flex justify-content-between align-items-center"
         style="background:transparent;border-bottom:1px solid #F0F2F5;">
        <span style="font-family:'Montserrat',sans-serif;font-weight:600;font-size:.9rem;color:#2C3E50;">
            <i class="bi bi-display me-2 text-primary"></i>Votos por Máquina
        </span>
        <span class="badge text-bg-secondary">{{ $maquinasDaMissao->count() }} máquina(s)</span>
    </div>
    <div class="card-body p-0">
        <table class="res-table">
            <thead><tr><th>Máquina / Canal</th><th class="text-end">Votos</th></tr></thead>
            <tbody>
                @foreach($maquinasDaMissao as $row)
                    <tr>
                        <td>{{ $row->maquina?->nome ?? 'Desconhecida' }}</td>
                        <td class="text-end fw-semibold">{{ $row->total_votos }}</td>
                    </tr>
                @endforeach
                @if($mostrarAlianca && $votosRemotosAlianca > 0)
                <tr>
                    <td><i class="bi bi-wifi me-1 text-muted"></i>Votação Remota <span class="badge-tipo badge-alianca ms-1">Aliança</span></td>
                    <td class="text-end fw-semibold">{{ $votosRemotosAlianca }}</td>
                </tr>
                @endif
                @if($mostrarVida && $votosRemotosVida > 0)
                <tr>
                    <td><i class="bi bi-wifi me-1 text-muted"></i>Votação Remota <span class="badge-tipo badge-vida ms-1">Vida</span></td>
                    <td class="text-end fw-semibold">{{ $votosRemotosVida }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td class="fw-semibold">Total</td>
                    <td class="text-end fw-bold">{{ $maquinasDaMissao->sum('total_votos') + $remotosTotal }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- ── Resultados por Pergunta ────────────────────────────────── --}}
@foreach($eleicao->perguntas->sortBy('ordem') as $pergunta)
    @php $isVida = $pergunta->escopo === 'vida'; @endphp
    @if($filtro === 'alianca' && $isVida) @continue @endif
    @if($filtro === 'vida'    && !$isVida) @continue @endif

    @php
        // Para aliança em geral: iterar por cada cidade; caso contrário, cidade única
        $cidadesLoop = (!$isVida && $filtro === 'geral')
            ? $todasCidades
            : collect([null]); // null = não precisa de cidade (vida ou aliança específica)
    @endphp

    @foreach($cidadesLoop as $ecLoop)
    @php
        if ($isVida) {
            $opcoesCidade = $pergunta->opcoes
                ->map(function($opcao) use ($votosRaw, $pergunta) {
                    $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                    return $opcao;
                })->sortByDesc('total_votos');
        } else {
            $cidFiltro    = $ecLoop ? $ecLoop->cidade_id : $aliancaCidade->cidade_id;
            $opcoesCidade = $pergunta->opcoes->where('cidade_id', $cidFiltro)
                ->map(function($opcao) use ($votosRaw, $pergunta) {
                    $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                    return $opcao;
                })->sortByDesc('total_votos');
        }
        $totalVotosPergunta = $opcoesCidade->sum('total_votos');
        $maxVotos           = $opcoesCidade->max('total_votos');
    @endphp

    <div class="card mb-4" style="border-radius:.75rem!important;border:none!important;box-shadow:0 4px 12px rgba(0,0,0,.08)!important;">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:transparent;border-bottom:1px solid #F0F2F5;">
            <span style="font-family:'Montserrat',sans-serif;font-weight:600;font-size:.9rem;color:#2C3E50;">
                {{ $pergunta->pergunta }}
            </span>
            <div class="d-flex gap-2 align-items-center">
                @if(!$isVida && $ecLoop)
                    <span class="badge text-bg-secondary" style="font-size:.65rem;">{{ $ecLoop->cidade->nome }}</span>
                @endif
                <span class="badge-tipo {{ $isVida ? 'badge-vida' : 'badge-alianca' }}">
                    {{ $isVida ? 'Realidade de Vida' : 'Realidade de Aliança' }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="res-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Candidato</th>
                        <th class="text-center">Votos</th>
                        <th style="min-width:160px">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opcoesCidade as $i => $opcao)
                        @php
                            $pctOpcao = $totalVotosPergunta > 0 ? round($opcao->total_votos / $totalVotosPergunta * 100, 1) : 0;
                            $isWinner = $i === 0 && $totalVotosPergunta > 0 && $opcao->total_votos === $maxVotos;
                        @endphp
                        <tr class="{{ $isWinner ? 'winner' : '' }}">
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="{{ $isWinner ? 'winner-name' : '' }}">
                                @if($isWinner)<i class="bi bi-trophy-fill me-1" style="color:#00BCD4;font-size:.75rem;"></i>@endif
                                {{ $opcao->nome }}
                            </td>
                            <td class="text-center fw-semibold">{{ $opcao->total_votos }}</td>
                            <td>
                                <div class="mini-bar">
                                    <div class="progress"><div class="progress-bar" style="width:{{ $pctOpcao }}%"></div></div>
                                    <small>{{ $pctOpcao }}%</small>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Nenhum candidato encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endforeach

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const CIANO = '#00BCD4';
const CINZA = '#CED4DA';

function criarDonut(id, valor, total, label) {
    const restante = Math.max(total - valor, 0);
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{ data: [valor, restante], backgroundColor: [CIANO, CINZA], borderWidth: 0, hoverOffset: 6 }]
        },
        options: {
            cutout: '72%',
            animation: { animateRotate: true, duration: 900, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(ctx) {
                    const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                    return ` ${ctx.parsed} (${pct}%)`;
                }}}
            }
        }
    });
}

@if($mostrarAlianca)
criarDonut('chartAderencia',      {{ $votaram }}, {{ $compareceram }}, 'Aderência');
criarDonut('chartAproveitamento', {{ $votaram }},      {{ $compareceram }}, 'Aproveitamento');
@endif
@if($mostrarVida && $vidaEleitores > 0)
criarDonut('chartVidaAproveitamento', {{ $vidaVotaram }}, {{ $vidaEleitores }}, 'Aproveitamento Vida');
@endif
</script>
@endpush

@endsection
