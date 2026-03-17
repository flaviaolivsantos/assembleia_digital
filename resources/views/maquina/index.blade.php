<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votacao — Assembleia Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="text-center" style="width: 100%; max-width: 440px; padding: 1rem;">

        <h2 class="mb-1">Assembleia Digital</h2>
        <p class="text-muted mb-4">Maquina de Votacao</p>

        @if(!$eleicaoCidade)
            <div class="alert alert-warning">Nenhuma votacao aberta para esta missão no momento.</div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <p class="mb-2 text-muted small">
                        <strong>{{ $eleicaoCidade->eleicao->titulo }}</strong>
                    </p>

                    @if($eleicaoCidade->qtd_presencial > 0)
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <span class="badge text-bg-secondary fs-6">
                            {{ $eleicaoCidade->votos_presenciais }} / {{ $eleicaoCidade->qtd_presencial }} presenciais
                        </span>
                        <span class="badge text-bg-primary fs-6">
                            {{ $eleicaoCidade->votos_registrados - $eleicaoCidade->votos_presenciais }} / {{ $eleicaoCidade->qtd_remoto }} remotos
                        </span>
                    </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger text-start">{{ $errors->first() }}</div>
                    @endif

                    <p class="text-muted small text-start mb-3">
                        Digite sua senha para liberar a votacao para um eleitor presencial.
                    </p>
                    <form method="POST" action="{{ route('maquina.presencial') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="password" name="senha"
                                class="form-control form-control-lg text-center"
                                placeholder="Sua senha"
                                autocomplete="current-password"
                                autofocus required>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">Liberar Votacao</button>
                    </form>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="btn btn-sm btn-outline-secondary">Sair</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
