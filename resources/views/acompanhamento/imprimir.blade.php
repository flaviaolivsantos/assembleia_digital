<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Votos</title>
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

        /* ── Barra de ações (web only) ──────────────────────────── */
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
            min-height: 29.7cm;
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
            font-size: 1.55rem; font-weight: 800;
            letter-spacing: 2px; text-transform: uppercase;
            color: #111827; line-height: 1;
        }
        .doc-head-badge { text-align: right; }

        /* ── Metadados ──────────────────────────────────────────── */
        .doc-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: .5rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: .7rem 1rem;
            margin-bottom: 1.4rem;
        }
        .doc-meta-item .meta-label {
            font-size: .62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px;
            color: #9ca3af; margin-bottom: .15rem;
        }
        .doc-meta-item .meta-value {
            font-size: .82rem; font-weight: 600; color: #111827;
        }

        /* ── Título de eleição ──────────────────────────────────── */
        .eleicao-title {
            font-size: .85rem;
            font-weight: 700;
            color: #111827;
            margin: 1.4rem 0 .7rem;
            padding-bottom: .4rem;
            border-bottom: 2px solid #e5e7eb;
            page-break-after: avoid;
            break-after: avoid;
        }

        /* ── Badge de realidade ─────────────────────────────────── */
        .realidade-badge {
            display: inline-block;
            font-size: .62rem; font-weight: 700;
            letter-spacing: .8px; text-transform: uppercase;
            color: #374151;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: .2em .6em;
            margin-bottom: .45rem;
        }

        /* ── Tabela ─────────────────────────────────────────────── */
        .ata-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: .82rem;
            page-break-inside: auto;
            break-inside: auto;
        }
        .ata-table thead tr { background: #f3f4f6; }
        .ata-table thead th {
            font-size: .68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .6px;
            color: #374151; padding: .5rem .75rem;
            border: 1px solid #e5e7eb; text-align: left;
        }
        .ata-table thead th.center { text-align: center; }
        .ata-table tbody tr:nth-child(odd)  { background: #fff; }
        .ata-table tbody tr:nth-child(even) { background: #f9fafb; }
        .ata-table tbody tr { page-break-inside: avoid; break-inside: avoid; }
        .ata-table tbody td {
            padding: .45rem .75rem;
            border: 1px solid #e5e7eb;
            color: #374151; vertical-align: middle;
        }
        .ata-table tbody td.center { text-align: center; }
        .ata-table tfoot td {
            padding: .45rem .75rem;
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            font-weight: 700; font-size: .75rem; color: #111827;
        }
        .ata-table tfoot td.center { text-align: center; }

        /* Status */
        .s-aberta     { color: #15803d; font-weight: 600; }
        .s-encerrada  { color: #374151; font-weight: 600; }
        .s-aguardando { color: #9ca3af; font-weight: 500; }

        /* Barra de progresso (print-friendly) */
        .progresso-wrap { display: flex; align-items: center; gap: .4rem; }
        .progresso-bg {
            flex: 1; height: 6px; background: #e5e7eb;
            border-radius: 99px; overflow: hidden;
        }
        .progresso-fill { height: 100%; background: #00BCD4; border-radius: 99px; }
        .progresso-pct  { font-size: .78rem; color: #374151; min-width: 2.2rem; text-align: right; }

        /* ── Rodapé ─────────────────────────────────────────────── */
        .doc-footer {
            margin-top: 2.5rem;
            padding-top: .5rem;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: .65rem;
            color: #9ca3af;
        }

        /* ── Print ──────────────────────────────────────────────── */
        @page { size: A4 portrait; margin: 1.5cm 1.5cm 2cm; }

        @media print {
            body { margin: 0; background: #fff; }
            .no-print { display: none !important; }
            .doc-page { padding: 0; margin: 0; max-width: none; min-height: auto; }
            .ata-table               { page-break-inside: auto;  break-inside: auto; }
            .ata-table thead         { display: table-header-group; }
            .ata-table tbody         { page-break-inside: auto;  break-inside: auto; }
            .ata-table tbody tr      { page-break-inside: avoid; break-inside: avoid; }
            .eleicao-title { page-break-after: avoid; break-after: avoid; }
            .doc-footer { position: static; margin-top: 1.5rem; }
        }
    </style>
</head>
<body>

{{-- ── Barra de ações ────────────────────────────────────── --}}
<div class="no-print">
    <button onclick="window.print()" class="btn-imprimir">🖨 Imprimir / Salvar PDF</button>
    <a href="{{ route('acompanhamento.index') }}" class="btn-voltar">← Voltar ao Painel</a>
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
        <div class="doc-head-badge"></div>
    </div>

    {{-- ── Metadados ────────────────────────────────────────────── --}}
    <div class="doc-meta-grid">
        <div class="doc-meta-item">
            <div class="meta-label">Gerado em</div>
            <div class="meta-value">{{ $geradoEm }}</div>
        </div>
        <div class="doc-meta-item">
            <div class="meta-label">Eleições exibidas</div>
            <div class="meta-value">{{ count($eleicoes) }} em andamento</div>
        </div>
    </div>

    {{-- ── Eleições ─────────────────────────────────────────────── --}}
    @forelse($eleicoes as $eleicao)
    @php
        $temAlianca = $eleicao['tem_alianca'];
        $temVida    = $eleicao['tem_vida'];
    @endphp

    <div class="eleicao-title">{{ $eleicao['titulo'] }}</div>

    {{-- Aliança --}}
    @if($temAlianca)
    @php
        $totalC   = collect($eleicao['missoes'])->sum('consagrados');
        $totalM   = collect($eleicao['missoes'])->sum('membros');
        $totalV   = collect($eleicao['missoes'])->sum('votaram');
        $totalF   = collect($eleicao['missoes'])->sum('faltam');
        $totalPct = $totalM > 0 ? round($totalV / $totalM * 100) : 0;
    @endphp
    @if($temVida)
        <div class="realidade-badge">Realidade de Aliança</div>
    @endif
    <table class="ata-table">
        <thead>
            <tr>
                <th>Missão</th>
                <th>Status</th>
                <th class="center">Consagrados</th>
                <th class="center">Membros</th>
                <th class="center">Votaram</th>
                <th class="center">Faltam</th>
                <th class="center" style="min-width:120px;">Participação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eleicao['missoes'] as $m)
            <tr>
                <td>{{ $m['nome'] }}</td>
                <td>
                    @if($m['status'] === 'aberta')
                        <span class="s-aberta">Aberta</span>
                    @elseif($m['status'] === 'encerrada')
                        <span class="s-encerrada">Encerrada</span>
                    @else
                        <span class="s-aguardando">Aguardando</span>
                    @endif
                </td>
                <td class="center">{{ $m['consagrados'] ?: '—' }}</td>
                <td class="center"><strong>{{ $m['membros'] }}</strong></td>
                <td class="center"><strong>{{ $m['votaram'] }}</strong></td>
                <td class="center">{{ $m['faltam'] }}</td>
                <td class="center">
                    <div class="progresso-wrap">
                        <div class="progresso-bg"><div class="progresso-fill" style="width:{{ $m['pct'] }}%"></div></div>
                        <span class="progresso-pct">{{ $m['pct'] }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        @if(count($eleicao['missoes']) > 1)
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td class="center">{{ $totalC ?: '—' }}</td>
                <td class="center">{{ $totalM }}</td>
                <td class="center">{{ $totalV }}</td>
                <td class="center">{{ $totalF }}</td>
                <td class="center">
                    <div class="progresso-wrap">
                        <div class="progresso-bg"><div class="progresso-fill" style="width:{{ $totalPct }}%"></div></div>
                        <span class="progresso-pct">{{ $totalPct }}%</span>
                    </div>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
    @endif

    {{-- Vida --}}
    @if($temVida)
    @php
        $totalVC  = collect($eleicao['missoes'])->sum('vida_consagrados');
        $totalVM  = collect($eleicao['missoes'])->sum('vida_membros');
        $totalVV  = collect($eleicao['missoes'])->sum('vida_votaram');
        $totalVF  = collect($eleicao['missoes'])->sum('vida_faltam');
        $totalVPct = $totalVM > 0 ? round($totalVV / $totalVM * 100) : 0;
    @endphp
    <div class="realidade-badge">Realidade de Vida</div>
    <table class="ata-table">
        <thead>
            <tr>
                <th>Missão</th>
                <th>Status</th>
                <th class="center">Consagrados</th>
                <th class="center">Membros</th>
                <th class="center">Votaram</th>
                <th class="center">Faltam</th>
                <th class="center" style="min-width:120px;">Participação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eleicao['missoes'] as $m)
            <tr>
                <td>{{ $m['nome'] }}</td>
                <td>
                    @if($m['vida_status'] === 'aberta')
                        <span class="s-aberta">Aberta</span>
                    @elseif($m['vida_status'] === 'encerrada')
                        <span class="s-encerrada">Encerrada</span>
                    @else
                        <span class="s-aguardando">Aguardando</span>
                    @endif
                </td>
                <td class="center">{{ $m['vida_consagrados'] ?: '—' }}</td>
                <td class="center"><strong>{{ $m['vida_membros'] }}</strong></td>
                <td class="center"><strong>{{ $m['vida_votaram'] }}</strong></td>
                <td class="center">{{ $m['vida_faltam'] }}</td>
                <td class="center">
                    <div class="progresso-wrap">
                        <div class="progresso-bg"><div class="progresso-fill" style="width:{{ $m['vida_pct'] }}%"></div></div>
                        <span class="progresso-pct">{{ $m['vida_pct'] }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        @if(count($eleicao['missoes']) > 1)
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td class="center">{{ $totalVC ?: '—' }}</td>
                <td class="center">{{ $totalVM }}</td>
                <td class="center">{{ $totalVV }}</td>
                <td class="center">{{ $totalVF }}</td>
                <td class="center">
                    <div class="progresso-wrap">
                        <div class="progresso-bg"><div class="progresso-fill" style="width:{{ $totalVPct }}%"></div></div>
                        <span class="progresso-pct">{{ $totalVPct }}%</span>
                    </div>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
    @endif

    @empty
    <p style="color:#6b7280;font-size:.85rem;">Nenhuma eleição aberta no momento.</p>
    @endforelse

    {{-- ── Rodapé ──────────────────────────────────────────────── --}}
    <div class="doc-footer">
        Relatório gerado automaticamente pelo sistema Assembleia Digital / Assessoria de Gestão — Comunidade Recado em {{ $geradoEm }}
    </div>

</div>
</body>
</html>
