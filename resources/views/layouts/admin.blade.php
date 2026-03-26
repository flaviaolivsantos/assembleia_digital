<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assembleia Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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

        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--body-bg); color: var(--text-primary); margin: 0; padding: 0; }

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
            text-decoration: none; flex-shrink: 0;
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
        .sidebar-footer { padding: 1rem; border-top: 1px solid rgba(255,255,255,.08); flex-shrink: 0; }
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
        .main-wrapper { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }

        /* ── Topnav ──────────────────────────────── */
        .topnav {
            height: var(--topnav-h); background: #fff;
            border-bottom: 1px solid #E9ECEF;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; gap: .75rem;
            position: sticky; top: 0; z-index: 100;
        }
        .topnav-title { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: .9rem; color: var(--text-primary); }
        .topnav-nav { display: flex; gap: .25rem; }
        .topnav-nav .nav-pill {
            font-size: .78rem; font-weight: 500;
            padding: .35rem .8rem; border-radius: 2rem;
            color: var(--text-muted); text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px; transition: all .15s;
        }
        .topnav-nav .nav-pill:hover { background: #F0F2F5; color: var(--text-primary); }
        .topnav-user {
            display: flex; align-items: center; gap: 8px;
            padding: .35rem .65rem; border-radius: 2rem;
            cursor: pointer; transition: background .15s;
            border: 1px solid #E9ECEF; text-decoration: none;
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

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>

@php
    $perfil = auth()->user()->perfil;
@endphp

{{-- ── Sidebar ─────────────────────────────────────────────── --}}
<aside class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-check2-square"></i></div>
        <div class="sidebar-brand-text">Assembleia Digital <span>Sistema de Votação</span></div>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Principal</div>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Visão Geral
        </a>
        <a href="{{ route('admin.eleicoes.index') }}" class="sidebar-link {{ request()->routeIs('admin.eleicoes.*') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i> Eleições
        </a>
        <a href="{{ route('admin.cidades.index') }}" class="sidebar-link {{ request()->routeIs('admin.cidades.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Missões
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="sidebar-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Usuários
        </a>

        <div class="sidebar-section" style="margin-top:.5rem;">Gestão</div>

        <a href="{{ route('responsavel.index') }}" class="sidebar-link {{ request()->routeIs('responsavel.*') ? 'active' : '' }}">
            <i class="bi bi-person-check-fill"></i> Responsável
        </a>
        <a href="{{ route('acompanhamento.index') }}" class="sidebar-link {{ request()->routeIs('acompanhamento.*') ? 'active' : '' }}">
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

    <header class="topnav">
        <span class="topnav-title d-none d-md-block">@yield('page-title', 'Administrador')</span>

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

    <div class="page-content">
        @yield('content')
    </div>

    <footer style="text-align:center;padding:.75rem 1.5rem;font-size:.72rem;color:var(--text-muted);border-top:1px solid #E9ECEF;margin-left:0;">
        Assembleia Digital &mdash; Desenvolvido por Assessoria de Gestão
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
