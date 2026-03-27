<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votação Remota — Assembleia Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Coag-Vertical.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .token-input { font-size: 1.4rem; letter-spacing: 0.15em; text-transform: uppercase; text-align: center; }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="text-center" style="width: 100%; max-width: 440px; padding: 1rem;">

        <h2 class="mb-1">Assembleia Digital</h2>
        <p class="text-muted mb-4">Votação Remota</p>

        <div class="card shadow-sm">
            <div class="card-body p-4">

                @if($errors->any())
                    <div class="alert alert-danger text-start">{{ $errors->first() }}</div>
                @endif

                <p class="text-muted small mb-3">
                    Insira o token recebido do mesário para acessar a votação.
                </p>

                <form method="POST" action="{{ route('votacao.token') }}">
                    @csrf
                    <div class="mb-4">
                        <input type="text" name="token"
                            class="form-control token-input"
                            placeholder="XXXXX-XXXXX-XXXXX"
                            maxlength="17"
                            autofocus required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                </form>

            </div>
        </div>

        <p class="text-muted small mt-4">
            Assembleia Digital &mdash; Votação segura e anônima.
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
