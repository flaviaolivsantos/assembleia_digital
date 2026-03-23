<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votação — Assembleia Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .password-field { position: relative; }
        .password-field .form-control { padding-right: 2.6rem; border-radius: .375rem !important; }
        .password-toggle {
            position: absolute; right: 0; top: 0; height: 100%; width: 2.5rem;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; padding: 0; cursor: pointer;
            color: #CED4DA; font-size: .9rem; transition: color .2s; outline: none; z-index: 5;
        }
        .password-toggle:hover { color: #00BCD4; }
        .password-toggle .bi-eye-slash { color: #00BCD4; }
        .escopo-card {
            border: 2px solid #dee2e6; border-radius: .5rem; padding: .75rem 1rem;
            cursor: pointer; transition: border-color .15s, background .15s;
        }
        .escopo-card:has(input:checked) { border-color: #0d6efd; background: #f0f6ff; }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="text-center" style="width: 100%; max-width: 480px; padding: 1rem;">

        <h2 class="mb-1">Assembleia Digital</h2>
        <p class="text-muted mb-4">Máquina de Votação</p>

        @if(!$eleicaoCidade || (!$aliancaAberta && !$vidaAberta))
            <div class="alert alert-warning">Nenhuma votação aberta para esta cidade no momento.</div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <p class="mb-3 text-muted small">
                        <strong>{{ $eleicaoCidade->eleicao->titulo }}</strong>
                    </p>

                    @if($errors->any())
                        <div class="alert alert-danger text-start">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('maquina.presencial') }}">
                        @csrf

                        {{-- Escopo selector --}}
                        @if($aliancaAberta && $vidaAberta)
                            <div class="mb-4 text-start">
                                <p class="fw-semibold small mb-2">Tipo de votante:</p>
                                <div class="d-flex gap-2">
                                    <label class="escopo-card flex-fill text-start">
                                        <input type="radio" name="escopo" value="alianca" class="form-check-input me-2"
                                               {{ old('escopo', 'alianca') === 'alianca' ? 'checked' : '' }} required>
                                        <span class="badge bg-secondary me-1">Aliança</span>
                                        Realidade de Aliança
                                    </label>
                                    <label class="escopo-card flex-fill text-start">
                                        <input type="radio" name="escopo" value="vida" class="form-check-input me-2"
                                               {{ old('escopo') === 'vida' ? 'checked' : '' }}>
                                        <span class="badge bg-primary me-1">Vida</span>
                                        Realidade de Vida
                                    </label>
                                </div>
                            </div>
                        @elseif($aliancaAberta)
                            <input type="hidden" name="escopo" value="alianca">
                            <p class="text-muted small text-start mb-3">
                                <span class="badge bg-secondary me-1">Aliança</span>Realidade de Aliança
                            </p>
                        @else
                            <input type="hidden" name="escopo" value="vida">
                            <p class="text-muted small text-start mb-3">
                                <span class="badge bg-primary me-1">Vida</span>Realidade de Vida
                            </p>
                        @endif

                        @if($aliancaAberta && $eleicaoCidade->qtd_presencial > 0)
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <span class="badge text-bg-secondary">
                                {{ $eleicaoCidade->votos_presenciais }} / {{ $eleicaoCidade->qtd_presencial }} presenciais aliança
                            </span>
                        </div>
                        @endif
                        @if($vidaAberta && $eleicaoCidade->qtd_presencial_vida > 0)
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <span class="badge text-bg-primary">
                                {{ $eleicaoCidade->votos_presenciais_vida }} / {{ $eleicaoCidade->qtd_presencial_vida }} presenciais vida
                            </span>
                        </div>
                        @endif

                        <p class="text-muted small text-start mb-3">
                            Digite sua senha para liberar a votação para um eleitor presencial.
                        </p>

                        <div class="mb-3">
                            <div class="password-field">
                                <input type="password" id="senha-maquina" name="senha"
                                    class="form-control form-control-lg text-center"
                                    placeholder="Sua senha"
                                    autocomplete="current-password"
                                    autofocus required>
                                <button type="button" class="password-toggle" tabindex="-1"
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
