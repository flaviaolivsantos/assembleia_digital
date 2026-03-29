@extends('layouts.admin')
@section('page-title', 'Responsável')

@section('content')
<style>
    /* ── Reset de variáveis ────────────────────────────────── */
    :root {
        --azul:   #1B2A3B;
        --ciano:  #00BCD4;
        --verde:  #16a34a;
        --verm:   #dc2626;
        --amber:  #d97706;
    }

    /* ── Card principal ─────────────────────────────────────── */
    .el-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        margin-bottom: 1.25rem;
    }
    .el-card-head {
        border-radius: 12px 12px 0 0;
    }

    /* ── Cabeçalho do card ─────────────────────────────────── */
    .el-card-head {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
        padding: .9rem 1.25rem;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem;
    }
    .el-card-titulo {
        font-family: 'Montserrat', sans-serif;
        font-size: .95rem; font-weight: 700;
        color: #111827; margin: 0;
    }
    .el-card-data {
        font-size: .78rem; color: #6B7280;
        margin-top: .2rem;
        display: flex; align-items: center; gap: 4px;
    }

    /* ── Status pill (único elemento colorido por linha) ──── */
    .s-pill {
        font-family: 'Montserrat', sans-serif;
        font-size: .7rem; font-weight: 700;
        letter-spacing: .4px; text-transform: uppercase;
        padding: .25em .7em; border-radius: 2rem;
        display: inline-flex; align-items: center; gap: 4px;
        white-space: nowrap;
    }
    .s-aberta    { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .s-encerrada { background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; }
    .s-aguardando{ background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

    /* ── Seção (vida / aliança) ─────────────────────────────── */
    .el-section {
        padding: 1.1rem 1.25rem;
        border-top: 1px solid #F3F4F6;
    }
    .el-section:first-child { border-top: none; }

    /* ── Tipo + Cidade (texto limpo, sem pill colorida) ──── */
    .meta-tag {
        font-size: .75rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .5px;
        color: #6B7280;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .meta-tag i { font-size: .78rem; }

    /* ── Timestamps ─────────────────────────────────────────── */
    .el-timestamp {
        font-size: .78rem; color: #6B7280;
        display: inline-flex; align-items: center; gap: 4px;
        margin-top: .35rem;
    }

    /* ── Grid de métricas ──────────────────────────────────── */
    .metrics-row {
        display: inline-flex; align-items: stretch;
        gap: 0; margin-top: .85rem;
        border: 1px solid #E5E7EB; border-radius: 8px;
        overflow: hidden; background: #fff;
    }
    .metric-cell {
        padding: .55rem 1rem;
        display: flex; flex-direction: column; justify-content: center;
        border-right: 1px solid #E5E7EB;
        min-width: 90px;
    }
    .metric-cell:last-child { border-right: none; }
    .metric-label {
        font-size: .65rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .5px;
        color: #9CA3AF; line-height: 1;
        margin-bottom: .25rem;
    }
    .metric-value {
        font-family: 'Montserrat', sans-serif;
        font-size: 1.15rem; font-weight: 700;
        color: #111827; line-height: 1;
    }

    /* ── Botões ─────────────────────────────────────────────── */
    .btn-ac {
        font-family: 'Montserrat', sans-serif;
        font-size: .75rem; font-weight: 600;
        letter-spacing: .2px;
        padding: .4rem .9rem; border-radius: 6px;
        cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
        transition: filter .15s, transform .1s;
        white-space: nowrap; border: none;
    }
    .btn-ac:hover { filter: brightness(.93); transform: translateY(-1px); text-decoration: none; }
    .btn-ac:active { transform: translateY(0); }

    /* primário sólido */
    .btn-open   { background: #16a34a; color: #fff; }
    .btn-encerrar { background: #dc2626; color: #fff; }
    .btn-result { background: #1B2A3B; color: #fff; }

    /* outline (secundário) */
    .btn-outline {
        background: transparent; color: #374151;
        border: 1px solid #D1D5DB !important;
    }
    .btn-outline:hover { background: #F9FAFB; color: #111827; }

    /* ghost relatório no header */
    .btn-ghost {
        background: transparent; color: #6B7280;
        border: 1px solid #E5E7EB !important;
        font-size: .72rem;
    }
    .btn-ghost:hover { background: #F3F4F6; color: #374151; }

    /* ── Filter bar ─────────────────────────────────────────── */
    .filter-bar { display: flex; gap: .4rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .filter-btn {
        font-family: 'Montserrat', sans-serif;
        font-size: .72rem; font-weight: 600;
        letter-spacing: .3px; text-transform: uppercase;
        padding: .35rem .85rem; border-radius: 2rem;
        border: 1.5px solid #D1D5DB;
        background: #fff; color: #6B7280;
        cursor: pointer; transition: all .15s;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .filter-btn:hover { border-color: var(--ciano); color: var(--ciano); }
    .filter-btn.active { background: #1B2A3B; border-color: #1B2A3B; color: #fff; }

    @media (max-width: 576px) {
        .el-section { padding: .9rem 1rem; }
        .el-card-head { padding: .75rem 1rem; }
        .metrics-row { flex-wrap: wrap; }
    }
</style>

{{-- ── Cabeçalho ─────────────────────────────────────────── --}}
<div class="mb-4 d-flex justify-content-between align-items-end flex-wrap gap-2">
    <div>
        <h2 style="font-family:'Montserrat',sans-serif;font-size:1.35rem;font-weight:700;color:#111827;margin:0;">Painel Eleição</h2>
        <p style="font-size:.85rem;color:#6B7280;margin:.1rem 0 0;">Gerencie a eleição.</p>
    </div>
</div>

{{-- ── Filtro ─────────────────────────────────────────────── --}}
<div class="filter-bar">
    <button class="filter-btn active" onclick="filtrar('todos', this)">
        <i class="bi bi-grid-3x3-gap"></i>Todos
    </button>
    <button class="filter-btn" onclick="filtrar('rascunho', this)">
        <i class="bi bi-clock"></i>Aguardando
    </button>
    <button class="filter-btn" onclick="filtrar('aberta', this)">
        <i class="bi bi-play-circle-fill"></i>Em andamento
    </button>
    <button class="filter-btn" onclick="filtrar('encerrada', this)">
        <i class="bi bi-check-circle-fill"></i>Encerrada
    </button>
</div>

@if(session('sucesso'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('sucesso') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@forelse($eleicoes as $eleicao)
    @php
        $temVida    = $eleicao->perguntas->where('escopo', 'vida')->count() > 0;
        $temAlianca = $eleicao->perguntas->where('escopo', 'alianca')->count() > 0;
    @endphp

    <div class="el-card" data-status="{{ $eleicao->status }}">

        {{-- ── Cabeçalho do card ── --}}
        <div class="el-card-head">
            <div>
                <h5 class="el-card-titulo">{{ $eleicao->titulo }}</h5>
                <div class="el-card-data">
                    <i class="bi bi-calendar3"></i>{{ $eleicao->data_eleicao->format('d/m/Y') }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($eleicao->status === 'aberta')
                    <span class="s-pill s-aberta"><i class="bi bi-circle-fill" style="font-size:.5rem;"></i>Em andamento</span>
                @elseif($eleicao->status === 'encerrada')
                    <span class="s-pill s-encerrada"><i class="bi bi-check-circle-fill"></i>Encerrada</span>
                @else
                    <span class="s-pill s-aguardando"><i class="bi bi-clock"></i>Aguardando</span>
                @endif
                @if($eleicao->status !== 'rascunho')
                    @php $ecFirst = $eleicao->cidades->first(); @endphp
                    @if($ecFirst)
                    <a href="{{ route('responsavel.relatorios', $ecFirst) }}" class="btn-ac btn-ghost" target="_blank">
                        <i class="bi bi-journal-text"></i>Relatório
                    </a>
                    @endif
                @endif
            </div>
        </div>

        {{-- ── Realidade de Vida ── --}}
        @if($temVida)
        <div class="el-section">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="flex-grow-1">

                    {{-- Tipo + Status --}}
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="meta-tag"><i class="bi bi-globe2"></i>Realidade de Vida</span>
                        @if($eleicao->aberta_vida)
                            <span class="s-pill s-aberta"><i class="bi bi-circle-fill" style="font-size:.5rem;"></i>Aberta</span>
                        @elseif($eleicao->data_encerramento_vida)
                            <span class="s-pill s-encerrada"><i class="bi bi-check-circle-fill"></i>Encerrada</span>
                        @else
                            <span class="s-pill s-aguardando"><i class="bi bi-clock"></i>Aguardando</span>
                        @endif
                    </div>

                    {{-- Métricas vida --}}
                    @php
                        $vidaMembrosTot = $eleicao->cidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0));
                        $vidaVotosTot   = $eleicao->cidades->sum(fn($ec) => $ec->votos_registrados_vida ?? 0);
                        $vidaPart       = $vidaMembrosTot > 0 ? round($vidaVotosTot / $vidaMembrosTot * 100) : 0;
                    @endphp
                    <div class="metrics-row">
                        <div class="metric-cell">
                            <div class="metric-label">Membros</div>
                            <div class="metric-value">{{ $vidaMembrosTot ?: '—' }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Votaram</div>
                            <div class="metric-value">{{ $vidaVotosTot }}</div>
                        </div>
                        @if($vidaMembrosTot > 0)
                        <div class="metric-cell">
                            <div class="metric-label">Participação</div>
                            <div class="metric-value">{{ $vidaPart }}%</div>
                        </div>
                        @endif
                    </div>

                    {{-- Timestamps --}}
                    <div class="d-flex flex-wrap gap-3">
                        @if($eleicao->data_abertura_vida)
                            <span class="el-timestamp">
                                <i class="bi bi-play-circle text-success"></i>
                                Aberta em {{ $eleicao->data_abertura_vida->format('d/m/Y H:i') }}
                            </span>
                        @endif
                        @if($eleicao->data_encerramento_vida)
                            <span class="el-timestamp">
                                <i class="bi bi-stop-circle" style="color:#dc2626;"></i>
                                Encerrada em {{ $eleicao->data_encerramento_vida->format('d/m/Y H:i') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Botões --}}
                <div class="d-flex flex-column gap-2 align-items-stretch justify-content-center" style="width:180px;flex-shrink:0;">
                    @if(!$temAlianca)
                        @foreach($eleicao->cidades as $ec)
                            @if(!($ec->data_encerramento_vida ?? false))
                            <a href="{{ route('responsavel.membros', $ec) }}" class="btn-ac btn-outline">
                                <i class="bi bi-people"></i>Configurar Membros
                                @if(auth()->user()->perfil === 'admin') <small style="opacity:.6;">— {{ $ec->cidade->nome }}</small> @endif
                            </a>
                            @endif
                        @endforeach
                    @endif

                    @if(!$eleicao->aberta_vida && !$eleicao->data_encerramento_vida)
                        <a href="{{ route('responsavel.vida.abrir', $eleicao) }}" class="btn-ac btn-open">
                            <i class="bi bi-play-circle-fill"></i>Abrir Votação Vida
                        </a>
                    @endif
                    @if($eleicao->aberta_vida)
                        <a href="{{ route('responsavel.vida.encerrar', $eleicao) }}" class="btn-ac btn-encerrar">
                            <i class="bi bi-stop-circle-fill"></i>Encerrar Vida
                        </a>
                    @endif
                    @if($eleicao->aberta_vida || $eleicao->data_encerramento_vida)
                        @php $ecVida = $eleicao->cidades->first(); @endphp
                        @if($ecVida)
                        <a href="{{ route('responsavel.resultados', $ecVida) }}?filtro=vida" class="btn-ac btn-result">
                            <i class="bi bi-bar-chart-fill"></i>Ver Resultados
                        </a>
                        <a href="{{ route('responsavel.zeresima.vida', $eleicao) }}" class="btn-ac btn-outline" target="_blank">
                            <i class="bi bi-file-earmark-check"></i>Zerésima
                        </a>
                        @endif
                    @endif
                    {{-- @if(auth()->user()->perfil === 'admin' && !$eleicao->aberta_vida && $eleicao->data_encerramento_vida)
                        <a href="{{ route('responsavel.vida.reabrir', $eleicao) }}" class="btn-ac" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
                            <i class="bi bi-arrow-counterclockwise"></i>Reabrir Vida
                        </a>
                    @endif --}}
                </div>
            </div>
        </div>
        @endif

        {{-- ── Realidade de Aliança (por cidade) ── --}}
        @if($temAlianca)
            @foreach($eleicao->cidades as $ec)
            <div class="el-section">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="flex-grow-1">

                        {{-- Tipo + Cidade + Status --}}
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <span class="meta-tag"><i class="bi bi-building"></i>Realidade de Aliança</span>
                            @if(auth()->user()->perfil === 'admin')
                                <span class="meta-tag"><i class="bi bi-geo-alt"></i>{{ $ec->cidade->nome }}</span>
                            @endif
                            @if($ec->aberta)
                                <span class="s-pill s-aberta"><i class="bi bi-circle-fill" style="font-size:.5rem;"></i>Aberta</span>
                            @elseif($ec->data_encerramento)
                                <span class="s-pill s-encerrada"><i class="bi bi-check-circle-fill"></i>Finalizada</span>
                            @else
                                <span class="s-pill s-aguardando"><i class="bi bi-clock"></i>Aguardando</span>
                            @endif
                        </div>

                        {{-- Métricas aliança --}}
                        <div class="metrics-row">
                            @if($temVida)
                            <div class="metric-cell">
                                <div class="metric-label">Membros Vida</div>
                                <div class="metric-value">{{ ($ec->qtd_presencial_vida + $ec->qtd_vida) ?: '—' }}</div>
                            </div>
                            @endif
                            <div class="metric-cell">
                                <div class="metric-label">Membros Aliança</div>
                                <div class="metric-value">{{ $ec->qtd_membros }}</div>
                            </div>
                            <div class="metric-cell">
                                <div class="metric-label">Votaram (Aliança)</div>
                                <div class="metric-value">{{ $ec->votos_registrados }}</div>
                            </div>
                            @if($ec->qtd_membros > 0)
                            <div class="metric-cell">
                                <div class="metric-label">Participação (Aliança)</div>
                                <div class="metric-value">{{ number_format(($ec->votos_registrados / $ec->qtd_membros) * 100, 0) }}%</div>
                            </div>
                            @endif
                        </div>

                        {{-- Timestamps aliança --}}
                        <div class="d-flex flex-wrap gap-3">
                            @if($ec->data_abertura)
                                <span class="el-timestamp">
                                    <i class="bi bi-play-circle text-success"></i>
                                    Aberta em {{ $ec->data_abertura->format('d/m/Y H:i') }}
                                </span>
                            @endif
                            @if($ec->data_encerramento)
                                <span class="el-timestamp">
                                    <i class="bi bi-stop-circle" style="color:#dc2626;"></i>
                                    Encerrada em {{ $ec->data_encerramento->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- Botões --}}
                    <div class="d-flex flex-column gap-2 align-items-stretch justify-content-center" style="width:180px;flex-shrink:0;">
                        @if(!$ec->data_encerramento)
                            <a href="{{ route('responsavel.membros', $ec) }}" class="btn-ac btn-outline">
                                <i class="bi bi-people"></i>Alterar Membros
                            </a>
                        @endif
                        @if(!$ec->aberta && !$ec->data_encerramento)
                            <a href="{{ route('responsavel.abrir', $ec) }}" class="btn-ac btn-open">
                                <i class="bi bi-play-circle-fill"></i>Abrir Aliança
                            </a>
                        @endif
                        @if($ec->aberta)
                            <a href="{{ route('responsavel.encerrar', $ec) }}" class="btn-ac btn-encerrar">
                                <i class="bi bi-stop-circle-fill"></i>Encerrar Aliança
                            </a>
                        @endif
                        @if($ec->aberta || $ec->data_encerramento)
                            <a href="{{ route('responsavel.resultados', $ec) }}?filtro=alianca" class="btn-ac btn-result">
                                <i class="bi bi-bar-chart-fill"></i>Ver Resultados
                            </a>
                            <a href="{{ route('responsavel.zeresima.alianca', $ec) }}" class="btn-ac btn-outline" target="_blank">
                                <i class="bi bi-file-earmark-check"></i>Zerésima
                            </a>
                        @endif
                        {{-- @if(auth()->user()->perfil === 'admin' && !$ec->aberta && $ec->data_encerramento)
                            <a href="{{ route('responsavel.alianca.reabrir', $ec) }}" class="btn-ac" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
                                <i class="bi bi-arrow-counterclockwise"></i>Reabrir Aliança
                            </a>
                        @endif --}}
                    </div>
                </div>
            </div>
            @endforeach
        @endif

    </div>
@empty
    <div class="el-card" style="padding:2.5rem;text-align:center;">
        <i class="bi bi-inbox" style="font-size:2rem;color:#D1D5DB;"></i>
        <p style="margin:.5rem 0 0;color:#6B7280;">Nenhuma eleição disponível.</p>
    </div>
@endforelse

@push('scripts')
<script>
function filtrar(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.el-card[data-status]').forEach(card => {
        card.style.display = (status === 'todos' || card.dataset.status === status) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
