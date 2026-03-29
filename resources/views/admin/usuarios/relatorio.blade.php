<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Detalhado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print {
            position: sticky; top: 0; z-index: 100;
            background: #1B2A3B; padding: .55rem 1rem;
            display: flex; gap: .5rem; align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,.2);
        }
        .btn-imprimir {
            background: #00BCD4; color: #fff; border: none;
            padding: .38rem .9rem; border-radius: 5px;
            font-family: inherit; font-weight: 600; font-size: .78rem;
            cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-imprimir:hover { background: #00a5bb; }
        .btn-voltar {
            background: transparent; color: #fff;
            border: 1px solid rgba(255,255,255,.3);
            padding: .38rem .9rem; border-radius: 5px;
            font-size: .78rem; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-voltar:hover { border-color: #fff; }

        .doc-page {
            max-width: 21cm;
            margin: 0 auto;
            padding: 1.8cm 1.8cm 2.5cm;
        }

        .doc-head {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 1.4rem;
        }
        .doc-head-logo img { height: 72px; object-fit: contain; }
        .doc-head-title { text-align: center; }
        .doc-head-title .label-oficial {
            font-size: .62rem; font-weight: 700;
            letter-spacing: 3px; text-transform: uppercase;
            color: #6b7280; margin-bottom: .3rem;
        }
        .doc-head-title h1 {
            font-size: 1.35rem; font-weight: 800;
            letter-spacing: 2px; text-transform: uppercase;
            color: #111827; line-height: 1.1;
        }

        .doc-meta {
            display: flex; gap: 2rem; flex-wrap: wrap;
            background: #f9fafb; border: 1px solid #e5e7eb;
            border-radius: 6px; padding: .6rem 1rem;
            margin-bottom: 1.2rem; font-size: .78rem;
        }
        .meta-label { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #9ca3af; margin-bottom: .1rem; }
        .meta-value { font-size: .8rem; font-weight: 600; color: #111827; }

        .ata-table { width: 100%; border-collapse: collapse; font-size: .78rem; margin-bottom: 1rem; }
        .ata-table thead tr { background: #f3f4f6; }
        .ata-table thead th {
            font-size: .65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .6px;
            color: #374151; padding: .45rem .65rem;
            border: 1px solid #e5e7eb; text-align: left;
        }
        .ata-table tbody tr:nth-child(odd)  { background: #fff; }
        .ata-table tbody tr:nth-child(even) { background: #f9fafb; }
        .ata-table tbody tr { page-break-inside: avoid; }
        .ata-table tbody td { padding: .38rem .65rem; border: 1px solid #e5e7eb; color: #374151; vertical-align: middle; }
        .ata-table tfoot td { padding: .38rem .65rem; border: 1px solid #e5e7eb; background: #f3f4f6; font-weight: 700; font-size: .72rem; }

        .doc-footer {
            margin-top: 2rem; padding-top: .5rem;
            border-top: 1px solid #e5e7eb;
            text-align: center; font-size: .65rem; color: #9ca3af;
        }

        @page { size: A4 portrait; margin: 1.5cm 1.5cm 2cm; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .doc-page { padding: 0; margin: 0; max-width: none; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-imprimir">🖨 Imprimir / Salvar PDF</button>
    <a href="{{ route('admin.usuarios.index') }}" class="btn-voltar">← Voltar</a>
</div>

<div class="doc-page">

    <div class="doc-head">
        <div class="doc-head-logo">
            <img src="{{ asset('images/Coag-Vertical.png') }}" alt="Comunidade Recado">
        </div>
        <div class="doc-head-title">
            <div class="label-oficial">Documento Oficial</div>
            <h1>Usuários — Detalhado</h1>
        </div>
        <div></div>
    </div>

    <div class="doc-meta">
        <div>
            <div class="meta-label">Total de usuários</div>
            <div class="meta-value">{{ $usuarios->count() }}</div>
        </div>
        <div>
            <div class="meta-label">Gerado em</div>
            <div class="meta-value">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <table class="ata-table">
        @php
            $escopoLabels = ['ambos' => 'Aliança e Vida', 'alianca' => 'Apenas Aliança', 'vida' => 'Apenas Vida'];
        @endphp
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Missão</th>
                <th>Realidade Liberada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $i => $usuario)
            <tr>
                <td style="color:#9ca3af;font-size:.72rem;">{{ $i + 1 }}</td>
                <td style="font-weight:600;">{{ $usuario->nome }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ ucfirst($usuario->perfil) }}</td>
                <td>{{ $usuario->cidade->nome ?? '—' }}</td>
                <td>
                    @if(in_array($usuario->perfil, ['maquina','mesario']))
                        {{ $escopoLabels[$usuario->escopo_maquina] ?? '—' }}
                    @else
                        —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total</td>
                <td>{{ $usuarios->count() }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="doc-footer">
        Relatório gerado automaticamente pelo sistema Assembleia Digital / Assessoria de Gestão — Comunidade Recado em {{ now()->format('d/m/Y \à\s H:i:s') }}
    </div>

</div>
</body>
</html>
