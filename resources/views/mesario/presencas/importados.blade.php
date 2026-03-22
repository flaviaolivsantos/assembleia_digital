<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tokens Gerados — {{ $eleicaoCidade->cidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .token-cell { font-family: monospace; font-size: 1.05rem; letter-spacing: 0.05em; }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body class="p-4">

<div class="no-print mb-3 d-flex gap-2 align-items-center">
    <button onclick="window.print()" class="btn btn-dark btn-sm">Imprimir / Salvar PDF</button>
    <a href="{{ route('mesario.presencas.index', $eleicaoCidade) }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
    <span class="text-muted small ms-2">
        Estes tokens não serão exibidos novamente. Imprima antes de sair desta página.
    </span>
</div>

<div class="mb-3">
    <h5 class="mb-0">{{ $eleicaoCidade->eleicao->titulo }}</h5>
    <p class="text-muted mb-0">{{ $eleicaoCidade->cidade->nome }} &mdash; {{ now()->format('d/m/Y H:i') }}</p>
    <p class="text-muted small">{{ count($resultados) }} token(s) gerado(s)</p>
</div>

<table class="table table-bordered table-sm">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Token de Votação</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resultados as $i => $item)
            <tr>
                <td class="text-muted">{{ $i + 1 }}</td>
                <td>{{ $item['nome'] }}</td>
                <td class="token-cell">{{ $item['token'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="text-muted small mt-3 no-print">
    Cada token é de uso único. Entregue individualmente a cada membro antes de iniciar a votação.
</p>

</body>
</html>
