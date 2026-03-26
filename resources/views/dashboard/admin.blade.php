<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel — Assembleia Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-w: 240px;
            --topnav-h: 60px;
            --sidebar-bg: #1B2A3B;
            --sidebar-hover: rgba(255,255,255,.07);
            --sidebar-active: rgba(0,188,212,.15);
            --sidebar-active-border: #00BCD4;
            --body-bg: #F0F2F5;
            --card-bg: #FFFFFF;
            --text-primary: #1B2A3B;
            --text-muted: #6C757D;
            --ciano: #00BCD4;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--body-bg); color: var(--text-primary); }

        /* ── Sidebar ─────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 200; overflow-y: auto;
        }
        .sidebar-brand {
            height: var(--topnav-h);
            display: flex; align-items: center; gap: 10px;
            padding: 0 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            text-decoration: none;
        }
        .sidebar-brand-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--ciano);
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem; color: #fff; flex-shrink: 0;
        }
        .sidebar-brand-text {
            font-family: 'Montserrat', sans-serif; font-weight: 700;
            font-size: .85rem; color: #fff; line-height: 1.2;
        }
        .sidebar-brand-text span { display: block; font-weight: 400; font-size: .7rem; color: rgba(255,255,255,.45); }

        .sidebar-nav { flex: 1; padding: 1rem 0; }
        .sidebar-section {
            font-size: .65rem; font-weight: 600; letter-spacing: 1.2px;
            text-transform: uppercase; color: rgba(255,255,255,.3);
            padding: .75rem 1.25rem .4rem;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: .6rem 1.25rem; margin: 1px .5rem;
            border-radius: 8px;
            font-size: .875rem; color: rgba(255,255,255,.65);
            text-decoration: none; transition: all .15s;
            position: relative;
        }
        .sidebar-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar-link.active {
            background: var(--sidebar-active); color: #fff;
            border-left: 3px solid var(--sidebar-active-border);
            padding-left: calc(1.25rem - 3px);
        }
        .sidebar-link i { font-size: 1rem; flex-shrink: 0; width: 20px; text-align: center; }
        .sidebar-link .badge-soon {
            margin-left: auto; font-size: .6rem; background: rgba(255,255,255,.12);
            color: rgba(255,255,255,.4); padding: .15em .5em; border-radius: 3px;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: .5rem .75rem; border-radius: 8px;
            background: rgba(255,255,255,.06);
        }
        .sidebar-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--ciano);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .sidebar-user-name { font-size: .82rem; color: #fff; font-weight: 500; line-height: 1.2; }
        .sidebar-user-role { font-size: .7rem; color: rgba(255,255,255,.4); }

        /* ── Main wrapper ────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Topnav ──────────────────────────────── */
        .topnav {
            height: var(--topnav-h);
            background: #fff;
            border-bottom: 1px solid #E9ECEF;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem;
            gap: .75rem;
            position: sticky; top: 0; z-index: 100;
        }
        .topnav-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600; font-size: .9rem; color: var(--text-primary);
        }
        .topnav-nav { display: flex; gap: .25rem; margin-left: auto; }
        .topnav-nav .nav-pill {
            font-size: .78rem; font-weight: 500;
            padding: .35rem .8rem; border-radius: 2rem;
            color: var(--text-muted); text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
            transition: all .15s;
        }
        .topnav-nav .nav-pill:hover { background: #F0F2F5; color: var(--text-primary); }
        .topnav-nav .nav-pill.dropdown-toggle::after { display: none; }

        .topnav-user {
            display: flex; align-items: center; gap: 8px;
            padding: .35rem .65rem; border-radius: 2rem;
            cursor: pointer; transition: background .15s;
            border: 1px solid #E9ECEF;
            text-decoration: none;
        }
        .topnav-user:hover { background: #F8F9FA; }
        .topnav-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--ciano);
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700; color: #fff;
        }
        .topnav-user-name { font-size: .82rem; font-weight: 500; color: var(--text-primary); }
        .topnav-user i { font-size: .7rem; color: var(--text-muted); }

        /* ── Page content ────────────────────────── */
        .page-content { padding: 1.5rem; flex: 1; max-width: 1280px; }

        /* ── KPI Cards ───────────────────────────── */
        .kpi-card {
            background: var(--card-bg); border-radius: 12px;
            border: none; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
            padding: .9rem 1.1rem;
            transition: transform .15s, box-shadow .15s;
        }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,.09); }
        .kpi-icon-box {
            width: 38px; height: 38px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .kpi-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.6rem; font-weight: 700; color: var(--text-primary);
            line-height: 1; margin-top: .5rem;
        }
        .kpi-label { font-size: .75rem; color: var(--text-muted); margin-top: .2rem; font-weight: 500; }
        .kpi-pill {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: .68rem; font-weight: 600; padding: .15em .55em;
            border-radius: 2rem; margin-top: .35rem;
        }

        /* ── Quick access cards ──────────────────── */
        .quick-card {
            background: #F8F9FA; border: 1.5px solid #E9ECEF;
            border-radius: 10px; padding: .65rem .75rem;
            text-decoration: none; color: var(--text-primary);
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; gap: .3rem;
            transition: all .15s; text-align: center; min-height: 72px;
        }
        .quick-card:hover { background: #fff; border-color: var(--ciano); color: var(--text-primary); box-shadow: 0 4px 12px rgba(0,188,212,.12); }
        .quick-card i { font-size: 1.2rem; color: var(--ciano); }
        .quick-card-title { font-size: .78rem; font-weight: 600; line-height: 1.2; }
        .quick-card-sub { font-size: .68rem; color: var(--text-muted); }

        /* ── Table card ──────────────────────────── */
        .table-card {
            background: var(--card-bg); border-radius: 14px;
            border: none; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
            overflow: hidden;
        }
        .table-card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #F0F2F5;
            display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
        }
        .table-card-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600; font-size: .9rem; color: var(--text-primary);
            margin-right: auto;
        }
        .search-input {
            border: 1.5px solid #E9ECEF; border-radius: 8px;
            padding: .35rem .75rem .35rem 2rem; font-size: .82rem;
            background: #F8F9FA; color: var(--text-primary);
            outline: none; width: 200px; transition: all .15s;
        }
        .search-input:focus { border-color: var(--ciano); background: #fff; width: 220px; }
        .search-wrap { position: relative; }
        .search-wrap i { position: absolute; left: .6rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: .8rem; pointer-events: none; }
        .filter-select {
            border: 1.5px solid #E9ECEF; border-radius: 8px;
            padding: .35rem .75rem; font-size: .82rem;
            background: #F8F9FA; color: var(--text-primary);
            outline: none; cursor: pointer; transition: border-color .15s;
        }
        .filter-select:focus { border-color: var(--ciano); }

        .dash-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .dash-table thead tr { background: #FAFBFC; }
        .dash-table thead th {
            padding: .75rem 1.25rem; font-size: .72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .5px;
            color: var(--text-muted); border-bottom: 1px solid #F0F2F5;
            white-space: nowrap;
        }
        .dash-table tbody tr { border-bottom: 1px solid #F8F9FA; transition: background .1s; }
        .dash-table tbody tr:hover { background: #FAFBFC; }
        .dash-table tbody tr:last-child { border-bottom: none; }
        .dash-table tbody td { padding: .85rem 1.25rem; vertical-align: middle; }

        .status-pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: .72rem; font-weight: 600; padding: .3em .8em; border-radius: 2rem;
        }
        .status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        .pill-aberta    { background: rgba(25,135,84,.1);  color: #146c43; } .pill-aberta::before    { background: #198754; }
        .pill-encerrada { background: rgba(33,37,41,.1);   color: #343a40; } .pill-encerrada::before { background: #343a40; }
        .pill-rascunho  { background: rgba(255,193,7,.15); color: #856404; } .pill-rascunho::before  { background: #ffc107; }

        .action-btn {
            width: 30px; height: 30px; border-radius: 7px;
            border: 1.5px solid #E9ECEF; background: transparent;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .85rem; color: var(--text-muted); cursor: pointer;
            text-decoration: none; transition: all .15s;
        }
        .action-btn:hover { border-color: var(--ciano); color: var(--ciano); background: rgba(0,188,212,.05); }
        .action-btn.danger:hover { border-color: #dc3545; color: #dc3545; background: rgba(220,53,69,.05); }

        .table-footer {
            padding: .75rem 1.25rem;
            border-top: 1px solid #F0F2F5;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .5rem;
        }
        .table-info-text { font-size: .78rem; color: var(--text-muted); }
        .pagination-btns { display: flex; gap: 4px; }
        .pg-btn {
            width: 30px; height: 30px; border-radius: 7px;
            border: 1.5px solid #E9ECEF; background: #fff;
            font-size: .78rem; font-weight: 500; color: var(--text-muted);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all .15s;
        }
        .pg-btn:hover:not(:disabled) { border-color: var(--ciano); color: var(--ciano); }
        .pg-btn.active { background: var(--text-primary); border-color: var(--text-primary); color: #fff; }
        .pg-btn:disabled { opacity: .35; cursor: not-allowed; }

        /* ── Section heading ─────────────────────── */
        .section-heading {
            font-family: 'Montserrat', sans-serif;
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .9px;
            color: var(--text-muted); margin-bottom: .75rem;
        }

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>

{{-- ── Sidebar ─────────────────────────────────────────────── --}}
<aside class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-check2-square"></i></div>
        <div class="sidebar-brand-text">Assembleia Digital <span>Sistema de Votação</span></div>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Principal</div>

        <a href="{{ route('dashboard') }}" class="sidebar-link active">
            <i class="bi bi-grid-1x2-fill"></i> Visão Geral
        </a>
        <a href="{{ route('admin.eleicoes.index') }}" class="sidebar-link">
            <i class="bi bi-check2-square"></i> Eleições
        </a>
        <a href="{{ route('admin.cidades.index') }}" class="sidebar-link">
            <i class="bi bi-building"></i> Missões
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="sidebar-link">
            <i class="bi bi-people-fill"></i> Usuários
        </a>

        <div class="sidebar-section" style="margin-top:.5rem;">Gestão</div>

        <a href="{{ route('responsavel.index') }}" class="sidebar-link">
            <i class="bi bi-person-check-fill"></i> Responsável
        </a>
        <a href="{{ route('acompanhamento.index') }}" class="sidebar-link">
            <i class="bi bi-bar-chart-line-fill"></i> Acompanhamento
        </a>
        <a href="#" class="sidebar-link" style="opacity:.5;pointer-events:none;">
            <i class="bi bi-gear-fill"></i> Configurações
            <span class="badge-soon">Em breve</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->nome, 0, 1)) }}</div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->nome }}</div>
                <div class="sidebar-user-role">Administrador</div>
            </div>
        </div>
    </div>
</aside>

{{-- ── Main wrapper ─────────────────────────────────────────── --}}
<div class="main-wrapper">

    {{-- Topnav --}}
    <header class="topnav">
        <span class="topnav-title d-none d-md-block">Painel do Administrador</span>

        {{-- Role nav pills --}}
        <nav class="topnav-nav">
            <a href="{{ route('responsavel.index') }}" class="nav-pill">
                <i class="bi bi-person-check"></i><span class="d-none d-lg-inline">Responsável</span>
            </a>
            <a href="{{ route('mesario.index') }}" class="nav-pill">
                <i class="bi bi-people"></i><span class="d-none d-lg-inline">Mesário</span>
            </a>
            <a href="{{ route('acompanhamento.index') }}" class="nav-pill">
                <i class="bi bi-bar-chart-line"></i><span class="d-none d-lg-inline">Painel</span>
            </a>
        </nav>

        {{-- User dropdown --}}
        <div class="dropdown ms-2">
            <a href="#" class="topnav-user dropdown-toggle" data-bs-toggle="dropdown">
                <div class="topnav-avatar">{{ strtoupper(substr(auth()->user()->nome, 0, 1)) }}</div>
                <span class="topnav-user-name d-none d-md-inline">{{ auth()->user()->nome }}</span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:10px;min-width:180px;">
                <li><h6 class="dropdown-header" style="font-size:.72rem;">{{ auth()->user()->email }}</h6></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2" style="font-size:.85rem;">
                            <i class="bi bi-box-arrow-right text-danger"></i> Sair
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    {{-- Page content --}}
    <div class="page-content">

        {{-- Page header --}}
        <div class="mb-4">
            <h4 style="font-family:'Montserrat',sans-serif;font-weight:700;color:var(--text-primary);margin-bottom:.2rem;">
                Olá, {{ explode(' ', auth()->user()->nome)[0] }}! 👋
            </h4>
            <p style="color:var(--text-muted);font-size:.875rem;margin:0;">
                Aqui está o resumo das atividades do sistema.
            </p>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:10px;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('sucesso') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="section-heading">Indicadores</div>
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-4">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Eleições</div>
                        </div>
                        <div class="kpi-icon-box" style="background:rgba(123,97,255,.12);color:#7B61FF;">
                            <i class="bi bi-check2-square"></i>
                        </div>
                    </div>
                    <div class="kpi-value">{{ $stats['eleicoes_total'] }}</div>
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.5rem;">
                        @if($stats['eleicoes_abertas'] > 0)
                            <span class="kpi-pill" style="background:rgba(25,135,84,.1);color:#146c43;">
                                <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>{{ $stats['eleicoes_abertas'] }} abertas
                            </span>
                        @endif
                        @if($stats['eleicoes_rascunho'] > 0)
                            <span class="kpi-pill" style="background:#F0F2F5;color:var(--text-muted);">
                                {{ $stats['eleicoes_rascunho'] }} rascunho
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Missões</div>
                        </div>
                        <div class="kpi-icon-box" style="background:rgba(13,110,253,.1);color:#0D6EFD;">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                    <div class="kpi-value">{{ $stats['cidades'] }}</div>
                    <div class="kpi-label">cadastradas no sistema</div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:.78rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Usuários</div>
                        </div>
                        <div class="kpi-icon-box" style="background:rgba(25,135,84,.1);color:#198754;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="kpi-value">{{ $stats['usuarios'] }}</div>
                    <div class="kpi-label">com acesso ao sistema</div>
                </div>
            </div>
        </div>

        {{-- Elections table --}}
        <div class="section-heading">Eleições</div>
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">Todas as Eleições</span>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Pesquisar por título...">
                </div>
                <select id="filterStatus" class="filter-select">
                    <option value="">Todos os status</option>
                    <option value="rascunho">Rascunho</option>
                    <option value="aberta">Aberta</option>
                    <option value="encerrada">Encerrada</option>
                </select>
                <a href="{{ route('admin.eleicoes.create') }}" class="btn btn-sm btn-primary" style="border-radius:8px;font-size:.8rem;">
                    <i class="bi bi-plus me-1"></i>Nova
                </a>
            </div>

            <div style="overflow-x:auto;">
                <table class="dash-table" id="electionsTable">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Missões</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($eleicoes as $eleicao)
                        <tr data-title="{{ strtolower($eleicao->titulo) }}" data-status="{{ $eleicao->status }}">
                            <td style="font-weight:500;">{{ $eleicao->titulo }}</td>
                            <td style="color:var(--text-muted);font-size:.82rem;">{{ $eleicao->data_eleicao->format('d/m/Y') }}</td>
                            <td>
                                <span class="status-pill pill-{{ $eleicao->status }}">
                                    {{ ucfirst($eleicao->status) }}
                                </span>
                            </td>
                            <td style="color:var(--text-muted);">{{ $eleicao->cidades_count }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="action-btn" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!$eleicao->estaAberta())
                                    <div class="dropdown">
                                        <button class="action-btn dropdown-toggle" data-bs-toggle="dropdown" title="Mais ações" style="border:none!important;">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:10px;font-size:.82rem;min-width:160px;">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.eleicoes.edit', $eleicao) }}">
                                                    <i class="bi bi-pencil text-secondary"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.eleicoes.duplicate', $eleicao) }}"
                                                      onsubmit="return confirm('Duplicar {{ addslashes($eleicao->titulo) }}?')">
                                                    @csrf
                                                    <button class="dropdown-item d-flex align-items-center gap-2">
                                                        <i class="bi bi-copy text-info"></i> Duplicar
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.eleicoes.destroy', $eleicao) }}"
                                                      onsubmit="return confirm('Remover {{ addslashes($eleicao->titulo) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <i class="bi bi-trash"></i> Remover
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="5" style="text-align:center;padding:2.5rem;color:var(--text-muted);">
                                Nenhuma eleição cadastrada.
                                <a href="{{ route('admin.eleicoes.create') }}" style="color:var(--ciano);">Criar a primeira</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span class="table-info-text" id="tableInfo"></span>
                <div class="pagination-btns" id="pagination"></div>
            </div>
        </div>

    </div>{{-- /page-content --}}

    <footer style="text-align:center;padding:.75rem 1.5rem;font-size:.72rem;color:var(--text-muted);border-top:1px solid #E9ECEF;">
        Assembleia Digital &mdash; Desenvolvido por Assessoria de Gestão
    </footer>
</div>{{-- /main-wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    const PER_PAGE = 10;
    let currentPage = 1;
    let filtered = [];

    const tbody     = document.getElementById('tableBody');
    const searchEl  = document.getElementById('searchInput');
    const filterEl  = document.getElementById('filterStatus');
    const infoEl    = document.getElementById('tableInfo');
    const pgEl      = document.getElementById('pagination');

    const allRows = Array.from(tbody.querySelectorAll('tr[data-title]'));

    function applyFilters() {
        const q      = searchEl.value.toLowerCase().trim();
        const status = filterEl.value;
        filtered = allRows.filter(row => {
            const matchTitle  = !q      || row.dataset.title.includes(q);
            const matchStatus = !status || row.dataset.status === status;
            return matchTitle && matchStatus;
        });
        currentPage = 1;
        render();
    }

    function render() {
        const total = filtered.length;
        const pages = Math.max(1, Math.ceil(total / PER_PAGE));
        currentPage = Math.min(currentPage, pages);
        const start = (currentPage - 1) * PER_PAGE;
        const end   = start + PER_PAGE;

        allRows.forEach(r => r.style.display = 'none');
        filtered.forEach((r, i) => { r.style.display = (i >= start && i < end) ? '' : 'none'; });

        // Empty state
        let emptyRow = tbody.querySelector('#noResults');
        if (total === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.id = 'noResults';
                emptyRow.innerHTML = `<td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted);">Nenhuma eleição encontrada.</td>`;
                tbody.appendChild(emptyRow);
            }
            emptyRow.style.display = '';
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }

        // Info text
        const from = total === 0 ? 0 : start + 1;
        const to   = Math.min(end, total);
        infoEl.textContent = total === 0 ? 'Nenhum resultado' : `Mostrando ${from}–${to} de ${total}`;

        // Pagination
        pgEl.innerHTML = '';
        const prev = btn('<i class="bi bi-chevron-left"></i>', currentPage <= 1);
        prev.addEventListener('click', () => { currentPage--; render(); });
        pgEl.appendChild(prev);

        const maxPages = Math.min(pages, 7);
        for (let p = 1; p <= maxPages; p++) {
            const b = btn(p, false, p === currentPage);
            b.addEventListener('click', (pg => () => { currentPage = pg; render(); })(p));
            pgEl.appendChild(b);
        }

        const next = btn('<i class="bi bi-chevron-right"></i>', currentPage >= pages);
        next.addEventListener('click', () => { currentPage++; render(); });
        pgEl.appendChild(next);
    }

    function btn(html, disabled, active) {
        const b = document.createElement('button');
        b.className = 'pg-btn' + (active ? ' active' : '');
        b.innerHTML = html;
        b.disabled = disabled;
        return b;
    }

    searchEl.addEventListener('input', applyFilters);
    filterEl.addEventListener('change', applyFilters);

    applyFilters();
})();
</script>
</body>
</html>
