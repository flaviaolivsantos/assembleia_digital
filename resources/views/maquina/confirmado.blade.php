<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voto Registrado — Assembleia Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="text-center px-3" style="max-width: 480px;">

        <div class="display-1 text-success mb-3">&#10003;</div>
        <h2 class="mb-2">Voto Registrado!</h2>
        <p class="text-muted mb-5">Seu voto foi computado com sucesso de forma anônima.<br>Obrigado pela sua participação.</p>

        <a href="{{ route('maquina.index') }}" class="btn btn-primary btn-lg">
            Próxima Votação
        </a>

        <p class="text-muted small mt-4">
            Esta página será redirecionada automaticamente em <span id="conta">10</span>s.
        </p>
    </div>
</div>

<script>
let s = 10;
const el = document.getElementById('conta');
const t = setInterval(() => {
    s--;
    el.textContent = s;
    if (s <= 0) {
        clearInterval(t);
        window.location = "{{ route('maquina.index') }}";
    }
}, 1000);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
