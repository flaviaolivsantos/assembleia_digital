<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Votos — {{ $eleicao->titulo }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ── Reset ──────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Barra de ações ─────────────────────────────────────── */
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

        /* ── Página ─────────────────────────────────────────────── */
        .doc-page {
            max-width: 21cm;
            margin: 0 auto;
            padding: 1.8cm 1.8cm 2.5cm;
        }

        /* ── Cabeçalho 3 colunas ────────────────────────────────── */
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
            font-size: 1.45rem; font-weight: 800;
            letter-spacing: 2px; text-transform: uppercase;
            color: #111827; line-height: 1.1;
        }

        /* ── Metadados ──────────────────────────────────────────── */
        .doc-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .5rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: .7rem 1rem;
            margin-bottom: 1.2rem;
        }
        .meta-label {
            font-size: .62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px;
            color: #9ca3af; margin-bottom: .15rem;
        }
        .meta-value {
            font-size: .82rem; font-weight: 600; color: #111827;
        }

        /* ── Título de seção ────────────────────────────────────── */
        .section-title {
            font-size: .72rem; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: #6b7280; border-bottom: 1px solid #e5e7eb;
            padding-bottom: .35rem; margin: 1.3rem 0 .65rem;
            page-break-after: avoid; break-after: avoid;
        }

        /* ── Tabela ─────────────────────────────────────────────── */
        .ata-table {
            width: 100%; border-collapse: collapse;
            margin-bottom: 1rem; font-size: .78rem;
            page-break-inside: auto; break-inside: auto;
        }
        .ata-table thead tr { background: #f3f4f6; }
        .ata-table thead th {
            font-size: .65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .6px;
            color: #374151; padding: .45rem .65rem;
            border: 1px solid #e5e7eb; text-align: left;
        }
        .ata-table thead th.center { text-align: center; }
        .ata-table tbody tr:nth-child(odd)  { background: #fff; }
        .ata-table tbody tr:nth-child(even) { background: #f9fafb; }
        .ata-table tbody tr { page-break-inside: avoid; break-inside: avoid; }
        .ata-table tbody td {
            padding: .38rem .65rem; border: 1px solid #e5e7eb;
            color: #374151; vertical-align: middle;
        }
        .ata-table tfoot td {
            padding: .38rem .65rem; border: 1px solid #e5e7eb;
            background: #f3f4f6; font-weight: 700;
            font-size: .72rem; color: #111827;
        }

        /* Badges */
        .b-alianca { font-weight: 600; color: #374151; }
        .b-vida    { font-weight: 600; color: #0e7490; }
        .b-presencial { color: #374151; }
        .b-remoto     { color: #0e7490; }
        .token-mono { font-family: 'Courier New', monospace; font-size: .72rem; color: #9ca3af; }

        /* ── Rodapé ─────────────────────────────────────────────── */
        .doc-footer {
            margin-top: 2.5rem; padding-top: .5rem;
            border-top: 1px solid #e5e7eb;
            text-align: center; font-size: .65rem; color: #9ca3af;
        }

        /* ── Print ──────────────────────────────────────────────── */
        @page { size: A4 portrait; margin: 1.5cm 1.5cm 2cm; }

        @media print {
            body { margin: 0; background: #fff; }
            .no-print { display: none !important; }
            .doc-page { padding: 0; margin: 0; max-width: none; }
            .ata-table               { page-break-inside: auto;  break-inside: auto; }
            .ata-table thead         { display: table-header-group; }
            .ata-table tbody         { page-break-inside: auto;  break-inside: auto; }
            .ata-table tbody tr      { page-break-inside: avoid; break-inside: avoid; }
            .section-title           { page-break-after: avoid;  break-after: avoid; }
            .doc-footer              { position: static; margin-top: 1.5rem; }
        }
    </style>
</head>
<body>
@php
    $votosFiltrados = match($filtro) {
        'alianca'    => $votos->filter(fn($v) => $v->pergunta?->escopo === 'alianca'),
        'vida'       => $votos->filter(fn($v) => $v->pergunta?->escopo === 'vida'),
        'presencial' => $votos->filter(fn($v) => $v->origem === 'presencial'),
        'remoto'     => $votos->filter(fn($v) => $v->origem === 'remoto'),
        default      => $votos,
    };
    $totalAlianca    = $votos->filter(fn($v) => $v->pergunta?->escopo === 'alianca')->count();
    $totalVida       = $votos->filter(fn($v) => $v->pergunta?->escopo === 'vida')->count();
    $totalPresencial = $votos->where('origem', 'presencial')->count();
    $totalRemoto     = $votos->where('origem', 'remoto')->count();

    $filtroLabels = [
        'todos' => 'Todos', 'alianca' => 'Aliança',
        'vida' => 'Vida', 'presencial' => 'Presencial', 'remoto' => 'Remoto',
    ];
@endphp

{{-- ── Barra de ações ────────────────────────────────────── --}}
<div class="no-print">
    <button onclick="window.print()" class="btn-imprimir">🖨 Imprimir / Salvar PDF</button>
    <a href="{{ route('responsavel.relatorios', $eleicaoCidade) }}?filtro={{ $filtro }}" class="btn-voltar">← Voltar ao Relatório</a>
</div>

<div class="doc-page">

    {{-- ── Cabeçalho ──────────────────────────────────────────── --}}
    <div class="doc-head">
        <div class="doc-head-logo">
            <img src="{{ asset('images/Coag-Vertical.png') }}" alt="Comunidade Recado">
        </div>
        <div class="doc-head-title">
            <div class="label-oficial">Documento Oficial</div>
            <h1>Relatório de Votos</h1>
        </div>
        <div></div>
    </div>

    {{-- ── Metadados ────────────────────────────────────────────── --}}
    <div class="doc-meta-grid">
        <div>
            <div class="meta-label">Eleição</div>
            <div class="meta-value">{{ $eleicao->titulo }}</div>
        </div>
        <div>
            <div class="meta-label">Data da Eleição</div>
            <div class="meta-value">{{ $eleicao->data_eleicao->format('d/m/Y') }}</div>
        </div>
        <div>
            <div class="meta-label">Gerado em</div>
            <div class="meta-value">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ── Resumo ───────────────────────────────────────────────── --}}
    <div class="section-title">Resumo</div>
    <table class="ata-table" style="max-width:400px;">
        <tbody>
            <tr><td style="background:#f9fafb;color:#6b7280;font-weight:600;font-size:.68rem;text-transform:uppercase;width:50%;">Total de registros</td><td><strong>{{ $votos->count() }}</strong></td></tr>
            <tr><td style="background:#f9fafb;color:#6b7280;font-weight:600;font-size:.68rem;text-transform:uppercase;">Aliança</td><td>{{ $totalAlianca }}</td></tr>
            <tr><td style="background:#f9fafb;color:#6b7280;font-weight:600;font-size:.68rem;text-transform:uppercase;">Vida</td><td>{{ $totalVida }}</td></tr>
            <tr><td style="background:#f9fafb;color:#6b7280;font-weight:600;font-size:.68rem;text-transform:uppercase;">Presencial</td><td>{{ $totalPresencial }}</td></tr>
            <tr><td style="background:#f9fafb;color:#6b7280;font-weight:600;font-size:.68rem;text-transform:uppercase;">Remoto</td><td>{{ $totalRemoto }}</td></tr>
            @if($filtro !== 'todos')
            <tr><td style="background:#f9fafb;color:#6b7280;font-weight:600;font-size:.68rem;text-transform:uppercase;">Filtro aplicado</td><td><strong>{{ $filtroLabels[$filtro] ?? $filtro }}</strong></td></tr>
            @endif
        </tbody>
    </table>

    {{-- ── Votos ────────────────────────────────────────────────── --}}
    <div class="section-title">Registro de Votos ({{ $votosFiltrados->count() }} registros)</div>
    <table class="ata-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Horário</th>
                <th>Realidade</th>
                <th>Origem</th>
                <th>Máquina</th>
                <th>Candidato</th>
                <th>Token</th>
            </tr>
        </thead>
        <tbody>
            @forelse($votosFiltrados->values() as $i => $voto)
            <tr>
                <td style="color:#9ca3af;font-size:.72rem;">{{ $i + 1 }}</td>
                <td style="white-space:nowrap;">{{ $voto->created_at ? \Carbon\Carbon::parse($voto->created_at)->format('d/m/Y H:i:s') : '—' }}</td>
                <td>
                    @if($voto->pergunta?->escopo === 'vida')
                        <span class="b-vida">Vida</span>
                    @else
                        <span class="b-alianca">Aliança</span>
                    @endif
                </td>
                <td>
                    @if($voto->origem === 'presencial')
                        <span class="b-presencial">Presencial</span>
                    @else
                        <span class="b-remoto">Remoto</span>
                    @endif
                </td>
                <td>{{ $voto->maquina?->name ?? '—' }}</td>
                <td style="font-weight:600;">{{ $voto->opcao?->nome ?? '—' }}</td>
                <td><span class="token-mono">{{ substr($voto->token_hash, 0, 4) }}...{{ substr($voto->token_hash, -4) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:1rem;">Nenhum voto encontrado.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">Total</td>
                <td>{{ $votosFiltrados->count() }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ── Rodapé ──────────────────────────────────────────────── --}}
    <div class="doc-footer">
        Relatório gerado automaticamente pelo sistema Assembleia Digital / Assessoria de Gestão — Comunidade Recado em {{ now()->format('d/m/Y \à\s H:i:s') }}
    </div>

</div>
</body>
</html>
