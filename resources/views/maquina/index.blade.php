<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votação — Assembleia Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="text-center" style="width: 100%; max-width: 440px; padding: 1rem;">

        <h2 class="mb-1">Assembleia Digital</h2>
        <p class="text-muted mb-4">Máquina de Votação</p>

        @if(!$eleicaoCidade)
            <div class="alert alert-warning">Nenhuma votação aberta para esta missão no momento.</div>
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
                        Digite sua senha para liberar a votação para um eleitor presencial.
                    </p>
                    <form method="POST" action="{{ route('maquina.presencial') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" id="senha-maquina" name="senha"
                                    class="form-control form-control-lg text-center"
                                    placeholder="Sua senha"
                                    autocomplete="current-password"
                                    autofocus required>
                                <button type="button" class="btn btn-outline-secondary" tabindex="-1"
                                        onclick="toggleSenha('senha-maquina','ico-senha-maquina')">
                                    <i class="bi bi-eye" id="ico-senha-maquina"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">Liberar Votação</button>
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
<script>
function toggleSenha(inputId, icoId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(icoId);
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
