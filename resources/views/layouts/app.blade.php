<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assembleia Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

@auth
@php
    $perfil = auth()->user()->perfil;
    $isAdmin      = $perfil === 'admin';
    $isResponsavel = in_array($perfil, ['responsavel', 'admin']);
    $isMesario    = in_array($perfil, ['mesario', 'responsavel', 'admin']);
@endphp
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a href="{{ route('dashboard') }}" class="navbar-brand">Assembleia Digital</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto gap-1">
                @if($isAdmin)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-shield-lock"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.eleicoes.index') }}">Eleições</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.cidades.index') }}">Missões</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.usuarios.index') }}">Usuários</a></li>
                        </ul>
                    </li>
                @endif

                @if($isResponsavel)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('responsavel.index') }}">
                            <i class="bi bi-person-check"></i> Responsável
                        </a>
                    </li>
                @endif

                @if($isMesario)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('mesario.index') }}">
                            <i class="bi bi-people"></i> Mesário
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('acompanhamento.index') }}">
                            <i class="bi bi-bar-chart-line"></i> Painel
                        </a>
                    </li>
                @endif
            </ul>

            <div class="d-flex align-items-center gap-3">
                @if(strtolower(auth()->user()->nome) !== $perfil)
                    <span class="text-white-50 small d-none d-lg-inline">{{ auth()->user()->nome }}</span>
                @endif
                <span class="badge-perfil perfil-{{ $perfil }}">{{ ucfirst($perfil) }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light">Sair</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endauth

<main class="container py-4 flex-grow-1">
    @yield('content')
</main>

<footer class="text-center text-muted small py-3 mt-auto border-top">
    Desenvolvido por Assessoria de Gestão
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
