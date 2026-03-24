@extends('layouts.app')

@section('content')
<style>
    /* ── Variáveis ─────────────────────────────────────────── */
    :root {
        --azul:        #2C3E50;
        --ciano:       #00BCD4;
        --branco:      #FFFFFF;
        --cinza-claro: #F8F9FA;
        --cinza-medio: #CED4DA;
        --cinza-esc:   #495057;
        --amarelo:     #FFC107;
        --verde:       #28A745;
        --vermelho:    #DC3545;
    }

    /* ── Cabeçalho da página ───────────────────────────────── */
    .page-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 1.5rem; font-weight: 700;
        color: var(--azul); margin-bottom: .15rem;
    }
    .page-sub {
        font-family: 'Roboto', sans-serif;
        font-size: .9rem; color: var(--cinza-esc);
    }

    /* ── Card de eleição ───────────────────────────────────── */
    .el-card {
        background: var(--branco);
        border: none !important;
        border-radius: .75rem !important;
        box-shadow: 0 4px 12px rgba(0,0,0,.05) !important;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    /* ── Cabeçalho do card ─────────────────────────────────── */
    .el-card-header {
        background: var(--azul);
        padding: 1rem 1.5rem;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem;
    }
    .el-card-titulo {
        font-family: 'Montserrat', sans-serif;
        font-size: 1rem; font-weight: 700;
        color: var(--branco); margin: 0;
    }
    .el-card-data {
        font-family: 'Roboto', sans-serif;
        font-size: .8rem; color: rgba(255,255,255,.65);
        margin-top: .15rem;
    }

    /* ── Tags de status ────────────────────────────────────── */
    .status-badge {
        font-family: 'Montserrat', sans-serif;
        font-size: .72rem; font-weight: 700;
        letter-spacing: .4px; text-transform: uppercase;
        padding: .3em .75em; border-radius: 2rem;
        display: inline-flex; align-items: center; gap: 5px;
        white-space: nowrap;
    }
    .status-aberta    { background: rgba(40,167,69,.15);  color: #1a6e2e; border: 1px solid rgba(40,167,69,.25); }
    .status-encerrada { background: rgba(44,62,80,.12);   color: var(--azul); border: 1px solid rgba(44,62,80,.2); }
    .status-aguardando{ background: rgba(255,193,7,.2);   color: #856404; border: 1px solid rgba(255,193,7,.35); }
    .status-badge-header { background: rgba(255,255,255,.15); color: var(--branco); border: 1px solid rgba(255,255,255,.25); }

    /* ── Seção (vida / aliança) ────────────────────────────── */
    .el-section {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #F0F2F5;
    }
    .el-section:first-child { border-top: none; }

    /* ── Badge de tipo ─────────────────────────────────────── */
    .tipo-badge {
        font-family: 'Montserrat', sans-serif;
        font-size: .72rem; font-weight: 700;
        letter-spacing: .4px; text-transform: uppercase;
        padding: .3em .75em; border-radius: 4px;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .tipo-vida    { background: var(--ciano); color: var(--branco); }
    .tipo-alianca { background: var(--cinza-esc); color: var(--branco); }
    .tipo-cidade  { background: var(--azul); color: var(--branco); font-size: .68rem; }

    /* ── Descrição ─────────────────────────────────────────── */
    .el-desc {
        font-family: 'Roboto', sans-serif;
        font-size: .85rem; color: var(--cinza-esc);
        margin: .4rem 0 0;
    }
    .el-timestamp {
        font-size: .78rem; color: #868e96;
        display: inline-flex; align-items: center; gap: 4px;
        margin-top: .3rem;
    }

    /* ── Métricas ──────────────────────────────────────────── */
    .metrics-grid {
        display: flex; gap: 1.5rem; flex-wrap: wrap;
        margin-top: .85rem;
    }
    .metric-item {
        display: flex; align-items: center; gap: .6rem;
    }
    .metric-icon {
        width: 36px; height: 36px; border-radius: 8px;
        background: var(--cinza-claro);
        display: flex; align-items: center; justify-content: center;
        color: var(--cinza-medio); font-size: .95rem;
        flex-shrink: 0;
    }
    .metric-label {
        font-family: 'Montserrat', sans-serif;
        font-size: .68rem; font-weight: 600;
        color: var(--cinza-esc); text-transform: uppercase;
        letter-spacing: .4px; line-height: 1.2;
    }
    .metric-value {
        font-family: 'Montserrat', sans-serif;
        font-size: 1.2rem; font-weight: 700;
        color: var(--azul); line-height: 1;
        margin-top: .1rem;
    }

    /* ── Botões de ação ────────────────────────────────────── */
    .btn-el {
        font-family: 'Montserrat', sans-serif;
        font-size: .78rem; font-weight: 600;
        letter-spacing: .3px;
        padding: .45rem 1rem; border-radius: .4rem;
        border: none; cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px;
        transition: filter .15s, transform .1s;
        white-space: nowrap;
    }
    .btn-el:hover { filter: brightness(.92); transform: translateY(-1px); }
    .btn-el:active { transform: translateY(0); }

    .btn-primario  { background: var(--verde);   color: var(--branco); }
    .btn-encerrar  { background: var(--vermelho); color: var(--branco); }
    .btn-secundario{ background: var(--cinza-claro); color: var(--cinza-esc); border: 1px solid var(--cinza-medio) !important; }
    .btn-resultado { background: var(--azul);    color: var(--branco); }
    .btn-reabrir   { background: var(--amarelo); color: #5a4100; }

    /* ── Divider ───────────────────────────────────────────── */
    .el-section-divider {
        height: 1px; background: #F0F2F5; margin: 0 1.5rem;
    }

    /* ── Filtro de status ──────────────────────────────────── */
    .filter-bar {
        display: flex; gap: .5rem; flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .filter-btn {
        font-family: 'Montserrat', sans-serif;
        font-size: .75rem; font-weight: 600;
        letter-spacing: .3px; text-transform: uppercase;
        padding: .4rem .9rem; border-radius: 2rem;
        border: 1.5px solid var(--cinza-medio);
        background: var(--branco); color: var(--cinza-esc);
        cursor: pointer; transition: all .15s;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .filter-btn:hover { border-color: var(--ciano); color: var(--ciano); }
    .filter-btn.active { background: var(--azul); border-color: var(--azul); color: var(--branco); }

    /* ── Responsivo ────────────────────────────────────────── */
    @media (max-width: 576px) {
        .el-section { padding: 1rem; }
        .el-card-header { padding: .85rem 1rem; }
        .metrics-grid { gap: 1rem; }
    }
</style>

{{-- ── Cabeçalho da página ──────────────────────────────── --}}
<div class="mb-4 d-flex justify-content-between align-items-end flex-wrap gap-2">
    <div>
        <h2 class="page-title mb-0">Painel Eleição</h2>
        <p class="page-sub mb-0">Gerencie a eleição.</p>
    </div>
</div>

{{-- ── Filtro por status ────────────────────────────────── --}}
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
        <div class="el-card-header">
            <div>
                <h5 class="el-card-titulo">{{ $eleicao->titulo }}</h5>
                <div class="el-card-data">
                    <i class="bi bi-calendar3 me-1"></i>{{ $eleicao->data_eleicao->format('d/m/Y') }}
                </div>
            </div>
            @if($eleicao->status === 'aberta')
                <span class="status-badge status-badge-header"><i class="bi bi-play-circle-fill"></i>Em andamento</span>
            @elseif($eleicao->status === 'encerrada')
                <span class="status-badge status-badge-header"><i class="bi bi-check-circle-fill"></i>Encerrada</span>
            @else
                <span class="status-badge status-badge-header"><i class="bi bi-clock"></i>Aguardando</span>
            @endif
        </div>

        {{-- ── Realidade de Vida ── --}}
        @if($temVida)
        <div class="el-section">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="flex-grow-1">

                    {{-- Tipo + Status --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="tipo-badge tipo-vida"><i class="bi bi-globe2"></i>Realidade de Vida</span>
                        @if($eleicao->aberta_vida)
                            <span class="status-badge status-aberta"><i class="bi bi-play-circle-fill"></i>Aberta</span>
                        @elseif($eleicao->data_encerramento_vida)
                            <span class="status-badge status-encerrada"><i class="bi bi-check-circle-fill"></i>Encerrada</span>
                        @else
                            <span class="status-badge status-aguardando"><i class="bi bi-clock"></i>Aguardando</span>
                        @endif
                    </div>

                    <p class="el-desc">Votação única — válida para todas as cidades simultaneamente.</p>

                    {{-- Timestamps --}}
                    @if($eleicao->data_abertura_vida)
                        <span class="el-timestamp me-3">
                            <i class="bi bi-play-circle text-success"></i>
                            Aberta em {{ $eleicao->data_abertura_vida->format('d/m/Y H:i') }}
                        </span>
                    @endif
                    @if($eleicao->data_encerramento_vida)
                        <span class="el-timestamp">
                            <i class="bi bi-stop-circle text-danger"></i>
                            Encerrada em {{ $eleicao->data_encerramento_vida->format('d/m/Y H:i') }}
                        </span>
                    @endif

                    {{-- Métricas vida (só quando não há aliança) --}}
                    @if(!$temAlianca)
                    <div class="metrics-grid">
                        @foreach($eleicao->cidades as $ec)
                        <div class="metric-item">
                            <div class="metric-icon"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="metric-label">
                                    @if(auth()->user()->perfil === 'admin'){{ $ec->cidade->nome }}@else Membros Vida @endif
                                </div>
                                <div class="metric-value">{{ ($ec->qtd_presencial_vida + $ec->qtd_vida) ?: '—' }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>

                {{-- Botões --}}
                <div class="d-flex flex-column gap-2 align-items-end">
                    @if(!$temAlianca)
                        @foreach($eleicao->cidades as $ec)
                            @if(!$ec->data_encerramento_vida ?? true)
                            <a href="{{ route('responsavel.membros', $ec) }}" class="btn-el btn-secundario">
                                <i class="bi bi-people"></i>
                                Configurar Membros Vida @if(auth()->user()->perfil === 'admin') — {{ $ec->cidade->nome }} @endif
                            </a>
                            @endif
                        @endforeach
                    @endif

                    @if(!$eleicao->aberta_vida && !$eleicao->data_encerramento_vida)
                        <a href="{{ route('responsavel.vida.abrir', $eleicao) }}" class="btn-el btn-primario">
                            <i class="bi bi-play-circle-fill"></i>Abrir Votação Vida
                        </a>
                    @endif
                    @if($eleicao->aberta_vida)
                        <a href="{{ route('responsavel.vida.encerrar', $eleicao) }}" class="btn-el btn-encerrar">
                            <i class="bi bi-stop-circle-fill"></i>Encerrar Votação Vida
                        </a>
                    @endif
                    @if(!$eleicao->aberta_vida && $eleicao->data_encerramento_vida)
                        @php $ecVida = $eleicao->cidades->first(); @endphp
                        @if($ecVida)
                        <a href="{{ route('responsavel.resultados', $ecVida) }}" class="btn-el btn-resultado">
                            <i class="bi bi-bar-chart-fill"></i>Ver Resultados
                        </a>
                        @endif
                    @endif
                    @if(auth()->user()->perfil === 'admin' && !$eleicao->aberta_vida && $eleicao->data_encerramento_vida)
                        <a href="{{ route('responsavel.vida.reabrir', $eleicao) }}" class="btn-el btn-reabrir">
                            <i class="bi bi-arrow-counterclockwise"></i>Reabrir Vida
                        </a>
                    @endif
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
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="tipo-badge tipo-alianca"><i class="bi bi-building"></i>Realidade de Aliança</span>
                            @if(auth()->user()->perfil === 'admin')
                                <span class="tipo-badge tipo-cidade"><i class="bi bi-geo-alt"></i>{{ $ec->cidade->nome }}</span>
                            @endif
                            @if($ec->aberta)
                                <span class="status-badge status-aberta"><i class="bi bi-play-circle-fill"></i>Aberta</span>
                            @elseif($ec->data_encerramento)
                                <span class="status-badge status-encerrada"><i class="bi bi-check-circle-fill"></i>Finalizada</span>
                            @else
                                <span class="status-badge status-aguardando"><i class="bi bi-clock"></i>Aguardando</span>
                            @endif
                        </div>

                        {{-- Métricas --}}
                        <div class="metrics-grid">
                            @if($temVida)
                            <div class="metric-item">
                                <div class="metric-icon" style="background:rgba(0,188,212,.08);color:var(--ciano);">
                                    <i class="bi bi-globe2"></i>
                                </div>
                                <div>
                                    <div class="metric-label">Vida (membros)</div>
                                    <div class="metric-value">{{ ($ec->qtd_presencial_vida + $ec->qtd_vida) ?: '—' }}</div>
                                </div>
                            </div>
                            @endif
                            <div class="metric-item">
                                <div class="metric-icon"><i class="bi bi-people-fill"></i></div>
                                <div>
                                    <div class="metric-label">Aliança (membros)</div>
                                    <div class="metric-value">{{ $ec->qtd_membros }}</div>
                                </div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-icon" style="background:rgba(40,167,69,.08);color:#28A745;">
                                    <i class="bi bi-check2-square"></i>
                                </div>
                                <div>
                                    <div class="metric-label">Votos Aliança</div>
                                    <div class="metric-value">{{ $ec->votos_registrados }}</div>
                                </div>
                            </div>
                            @if($ec->qtd_membros > 0)
                            <div class="metric-item">
                                <div class="metric-icon" style="background:rgba(44,62,80,.06);color:var(--azul);">
                                    <i class="bi bi-percent"></i>
                                </div>
                                <div>
                                    <div class="metric-label">Participação</div>
                                    <div class="metric-value">{{ number_format(($ec->votos_registrados / $ec->qtd_membros) * 100, 0) }}%</div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>

                    {{-- Botões --}}
                    <div class="d-flex flex-column gap-2 align-items-end">
                        @if(!$ec->data_encerramento)
                            <a href="{{ route('responsavel.membros', $ec) }}" class="btn-el btn-secundario">
                                <i class="bi bi-people"></i>Alterar Membros
                            </a>
                        @endif
                        @if(!$ec->aberta && !$ec->data_encerramento)
                            <a href="{{ route('responsavel.abrir', $ec) }}" class="btn-el btn-primario">
                                <i class="bi bi-play-circle-fill"></i>Abrir Aliança
                            </a>
                        @endif
                        @if($ec->aberta)
                            <a href="{{ route('responsavel.encerrar', $ec) }}" class="btn-el btn-encerrar">
                                <i class="bi bi-stop-circle-fill"></i>Encerrar Aliança
                            </a>
                        @endif
                        @if($ec->data_encerramento)
                            <a href="{{ route('responsavel.resultados', $ec) }}" class="btn-el btn-resultado">
                                <i class="bi bi-bar-chart-fill"></i>Ver Resultados
                            </a>
                        @endif
                        @if(auth()->user()->perfil === 'admin' && !$ec->aberta && $ec->data_encerramento)
                            <a href="{{ route('responsavel.alianca.reabrir', $ec) }}" class="btn-el btn-reabrir">
                                <i class="bi bi-arrow-counterclockwise"></i>Reabrir Aliança
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @endif

    </div>
@empty
    <div class="el-card p-4 text-center">
        <i class="bi bi-inbox" style="font-size:2rem;color:var(--cinza-medio);"></i>
        <p class="mt-2 mb-0" style="color:var(--cinza-esc);">Nenhuma eleição disponível.</p>
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
