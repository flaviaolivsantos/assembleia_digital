<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zerésima — {{ $eleicao->titulo }}</title>
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
        .doc-head-logo img {
            height: 72px;
            object-fit: contain;
        }
        .doc-head-title {
            text-align: center;
        }
        .doc-head-title .label-oficial {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: .3rem;
        }
        .doc-head-title h1 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #111827;
            line-height: 1;
        }
        .doc-head-badge {
            text-align: right;
        }
        .doc-head-badge .badge-oficial {
            display: inline-block;
            border: 1.5px solid #d1d5db;
            border-radius: 5px;
            padding: .3rem .7rem;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #374151;
            background: #f9fafb;
        }

        /* ── Bloco de metadados ─────────────────────────────────── */
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
        .doc-meta-item .meta-label {
            font-size: .62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9ca3af;
            margin-bottom: .15rem;
        }
        .doc-meta-item .meta-value {
            font-size: .82rem;
            font-weight: 600;
            color: #111827;
        }

        /* ── Aviso ──────────────────────────────────────────────── */
        .doc-aviso {
            border-left: 3px solid #d1d5db;
            background: #f9fafb;
            padding: .55rem .85rem;
            font-size: .78rem;
            color: #4b5563;
            border-radius: 0 4px 4px 0;
            margin-bottom: 1.3rem;
        }

        /* ── Títulos de seção ───────────────────────────────────── */
        .section-title {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: .35rem;
            margin: 1.3rem 0 .65rem;
            page-break-after: avoid;
            break-after: avoid;
        }
        .sub-title {
            font-size: .78rem;
            font-weight: 700;
            color: #374151;
            margin: .9rem 0 .35rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            page-break-after: avoid;
            break-after: avoid;
        }

        /* ── Tabela de auditoria (2 colunas) ────────────────────── */
        .audit-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: .82rem;
        }
        .audit-table td {
            padding: .45rem .75rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .audit-table tr:last-child td { border-bottom: none; }
        .audit-table td:first-child {
            font-weight: 600;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6b7280;
            width: 38%;
            background: #f9fafb;
            border-right: 1px solid #e5e7eb;
        }
        .audit-table td:last-child { color: #111827; }

        /* ── Tabela de candidatos ───────────────────────────────── */
        .ata-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: .82rem;
            page-break-inside: auto;
            break-inside: auto;
        }
        .ata-table thead tr {
            background: #f3f4f6;
        }
        .ata-table thead th {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #374151;
            padding: .5rem .75rem;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        .ata-table thead th.center { text-align: center; }
        .ata-table tbody tr:nth-child(odd)  { background: #fff; }
        .ata-table tbody tr:nth-child(even) { background: #f9fafb; }
        .ata-table tbody tr { page-break-inside: avoid; break-inside: avoid; }
        .ata-table tbody td {
            padding: .45rem .75rem;
            border: 1px solid #e5e7eb;
            color: #374151;
            vertical-align: middle;
        }
        .ata-table tbody td.center { text-align: center; }
        .zero-badge {
            display: inline-block;
            background: #f3f4f6;
            color: #6b7280;
            font-weight: 700;
            font-size: .75rem;
            padding: .1em .5em;
            border-radius: 3px;
            border: 1px solid #d1d5db;
        }

        /* ── Assinatura ─────────────────────────────────────────── */
        .assinatura-block {
            margin-top: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .4rem;
        }
        .assinatura-linha {
            width: 52%;
            border-top: 1px solid #6b7280;
        }
        .assinatura-label {
            font-size: .78rem;
            color: #6b7280;
            text-align: center;
        }

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
        /* @page com margem real — garante espaço em TODAS as páginas */
        @page { size: A4 portrait; margin: 1.5cm 1.5cm 2cm; }

        @media print {
            body { margin: 0; background: #fff; }
            /* Fix 4: ocultar todos os elementos de interface */
            .no-print { display: none !important; }
            .doc-page { padding: 0; margin: 0; max-width: none; min-height: auto; }
            /* Fix 2: tabela flui livremente; apenas as linhas não são cortadas */
            .ata-table               { page-break-inside: auto;  break-inside: auto; }
            .ata-table thead         { display: table-header-group; }
            .ata-table tbody         { page-break-inside: auto;  break-inside: auto; }
            .ata-table tbody tr      { page-break-inside: avoid; break-inside: avoid; }
            /* Fix 3: título nunca fica separado da tabela nem é cortado no meio */
            .section-title { page-break-after: avoid; break-after: avoid; page-break-inside: avoid; break-inside: avoid; }
            .sub-title     { page-break-after: avoid; break-after: avoid; page-break-inside: avoid; break-inside: avoid; }
            /* Fix 3: assinatura nunca fica órfã — título + bloco viajam juntos */
            .assinatura-wrapper { page-break-inside: avoid; break-inside: avoid; }
            /* Rodapé estático no final do conteúdo */
            .doc-footer { position: static; margin-top: 1.5rem; }
        }
    </style>
</head>
<body>

{{-- ── Barra de ações ────────────────────────────────────── --}}
<div class="no-print">
    <button onclick="window.print()" class="btn-imprimir">🖨 Imprimir / Salvar PDF</button>
    <a href="{{ route('responsavel.index') }}" class="btn-voltar">← Voltar ao Painel</a>
</div>

<div class="doc-page">

    {{-- ── Cabeçalho 3 colunas ──────────────────────────────── --}}
    <div class="doc-head">
        <div class="doc-head-logo">
            <img src="{{ asset('images/Coag-Vertical.png') }}" alt="Comunidade Recado">
        </div>
        <div class="doc-head-title">
            <div class="label-oficial">Documento Oficial</div>
            <h1>Zerésima</h1>
        </div>
        <div class="doc-head-badge"></div>
    </div>

    {{-- ── Metadados ─────────────────────────────────────────── --}}
    <div class="doc-meta-grid">
        <div class="doc-meta-item">
            <div class="meta-label">Eleição</div>
            <div class="meta-value">{{ $eleicao->titulo }}</div>
        </div>
        <div class="doc-meta-item">
            <div class="meta-label">Realidade</div>
            <div class="meta-value">
                @if($escopo === 'alianca')
                    Aliança — {{ $eleicaoCidade->cidade->nome }}
                @else
                    Vida — Todas as Missões
                @endif
            </div>
        </div>
        <div class="doc-meta-item">
            <div class="meta-label">Data / Hora de Abertura</div>
            <div class="meta-value">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ── Aviso ─────────────────────────────────────────────── --}}
    <div class="doc-aviso">
        Este documento certifica que, no momento da abertura da votação, todos os candidatos registrados possuem <strong>zero votos</strong>, atestando a integridade do processo eleitoral.
    </div>

    {{-- ── 1. Dados da Abertura ─────────────────────────────── --}}
    <div class="section-title">1. Dados da Abertura</div>
    <table class="audit-table">
        <tbody>
            <tr>
                <td>Eleição</td>
                <td>{{ $eleicao->titulo }}</td>
            </tr>
            <tr>
                <td>Realidade</td>
                <td>
                    @if($escopo === 'alianca')
                        Realidade de Aliança — {{ $eleicaoCidade->cidade->nome }}
                    @else
                        Realidade de Vida
                    @endif
                </td>
            </tr>
            <tr>
                <td>Data da Eleição</td>
                <td>{{ $eleicao->data_eleicao->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Horário de Abertura</td>
                <td><strong>{{ now()->format('d/m/Y H:i:s') }}</strong></td>
            </tr>
            <tr>
                <td>Aberta por</td>
                <td>
                    @if($escopo === 'alianca')
                        {{ $eleicaoCidade->abertaPor?->nome ?? auth()->user()->nome }}
                    @else
                        {{ $eleicao->abertaPorVida?->nome ?? auth()->user()->nome }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ── 2. Relação de Candidatos ─────────────────────────── --}}
    <div class="section-title">2. Relação de Candidatos</div>

    @foreach($eleicao->perguntas->where('escopo', $escopo)->sortBy('ordem') as $pergunta)
        @php
            $opcoes = $escopo === 'alianca'
                ? $pergunta->opcoes->where('cidade_id', $eleicaoCidade->cidade_id)->sortBy('nome')
                : $pergunta->opcoes->sortBy('nome');
        @endphp

        @if($opcoes->isEmpty()) @continue @endif

        <div class="sub-title">{{ $loop->iteration }}. {{ $pergunta->pergunta }}</div>

        <table class="ata-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Candidato</th>
                    <th class="center" style="width:80px;">Votos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opcoes as $opcao)
                <tr>
                    <td style="color:#9ca3af;">{{ $loop->iteration }}</td>
                    <td>{{ $opcao->nome }}</td>
                    <td class="center"><span class="zero-badge">0</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- ── 3. Assinatura ────────────────────────────────────── --}}
    <div class="assinatura-wrapper">
        <div class="section-title" style="margin-top:2rem;">3. Assinatura</div>
        <div class="assinatura-block">
            <div class="assinatura-linha"></div>
            <div class="assinatura-label">Responsável</div>
        </div>
    </div>

    {{-- ── Rodapé ────────────────────────────────────────────── --}}
    <div class="doc-footer">
        Zerésima gerada automaticamente pelo sistema Assembleia Digital — Assessoria de Gestão — Comunidade Recado em {{ now()->format('d/m/Y \à\s H:i:s') }}
    </div>

</div>
</body>
</html>
