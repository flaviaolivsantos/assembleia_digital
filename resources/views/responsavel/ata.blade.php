<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ata — {{ $eleicao->titulo }}</title>
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
        .btn-voltar.active {
            background: rgba(0,188,212,.2);
            border-color: #00BCD4;
            color: #00BCD4;
        }

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
        .sub-title .sub-label {
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            color: #9ca3af;
            font-size: .72rem;
        }

        /* ── Nota descritiva (italic gray) ──────────────────────── */
        .nota {
            font-size: .75rem;
            color: #6b7280;
            font-style: italic;
            margin: .2rem 0 .45rem;
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
            width: 42%;
            background: #f9fafb;
            border-right: 1px solid #e5e7eb;
        }
        .audit-table td:last-child { color: #111827; }

        /* ── Tabela de resultados ───────────────────────────────── */
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
        .ata-table tfoot td {
            padding: .45rem .75rem;
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            font-weight: 700;
            font-size: .75rem;
            color: #111827;
        }
        .ata-table tfoot td.center { text-align: center; }

        /* Valores em destaque */
        .val-strong { font-weight: 700; color: #111827; }
        .val-pct    { font-weight: 600; color: #374151; }

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
        @page { size: A4 portrait; margin: 1.5cm 1.5cm 2cm; }

        @media print {
            body { margin: 0; background: #fff; }
            .no-print { display: none !important; }
            .doc-page { padding: 0; margin: 0; max-width: none; min-height: auto; }
            .ata-table               { page-break-inside: auto;  break-inside: auto; }
            .ata-table thead         { display: table-header-group; }
            .ata-table tbody         { page-break-inside: auto;  break-inside: auto; }
            .ata-table tbody tr      { page-break-inside: avoid; break-inside: avoid; }
            .section-title { page-break-after: avoid; break-after: avoid; page-break-inside: avoid; break-inside: avoid; }
            .sub-title     { page-break-after: avoid; break-after: avoid; page-break-inside: avoid; break-inside: avoid; }
            .assinatura-wrapper { page-break-inside: avoid; break-inside: avoid; }
            .doc-footer { position: static; margin-top: 1.5rem; }
        }
    </style>
</head>
<body>
@php
    $mostrarAlianca = $temAlianca && ($filtro === 'geral' || $filtro === 'alianca');
    $mostrarVida    = $temVida    && ($filtro === 'geral' || $filtro === 'vida');
    $semZeros       = request()->boolean('sem_zeros', false);

    // Monta URL base para o toggle sem_zeros
    $toggleParams = array_merge(request()->query(), ['sem_zeros' => $semZeros ? '0' : '1']);
    $toggleUrl    = route('responsavel.ata', $eleicaoCidade) . '?' . http_build_query($toggleParams);
@endphp

{{-- ── Barra de ações ────────────────────────────────────── --}}
<div class="no-print">
    <button onclick="window.print()" class="btn-imprimir">🖨 Imprimir / Salvar PDF</button>
    <a href="{{ $toggleUrl }}" class="btn-voltar {{ $semZeros ? 'active' : '' }}">
        {{ $semZeros ? '✓ Ocultando zeros' : 'Ocultar zeros' }}
    </a>
    <a href="{{ route('responsavel.resultados', $eleicaoCidade) }}?filtro={{ $filtro }}{{ $filtro === 'alianca' ? '&alianca_cidade_id='.$aliancaCidade->cidade_id : '' }}" class="btn-voltar">← Voltar</a>
</div>

<div class="doc-page">

    {{-- ── Cabeçalho 3 colunas ──────────────────────────────── --}}
    <div class="doc-head">
        <div class="doc-head-logo">
            <img src="{{ asset('images/Coag-Vertical.png') }}" alt="Comunidade Recado">
        </div>
        <div class="doc-head-title">
            <div class="label-oficial">Documento Oficial</div>
            <h1>Ata de Eleição</h1>
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
                @if($mostrarVida && !$mostrarAlianca)
                    Vida — Todas as Missões
                @elseif($mostrarAlianca && !$mostrarVida)
                    Aliança — {{ $aliancaCidade->cidade->nome }}
                @else
                    Aliança + Vida
                @endif
            </div>
        </div>
        <div class="doc-meta-item">
            <div class="meta-label">Data da Eleição</div>
            <div class="meta-value">{{ $eleicao->data_eleicao->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- ── 1. Participação ────────────────────────────────────── --}}
    <div class="section-title">1. Participação</div>

    @if($mostrarAlianca)
    @php
        $faltaram = max(0, $aliancaCidade->qtd_consagrados - $aliancaCidade->votos_registrados);
        $adPct    = $aliancaCidade->qtd_consagrados > 0
            ? floor($aliancaCidade->votos_registrados / $aliancaCidade->qtd_consagrados * 10000) / 100
            : 0;
    @endphp
    <p class="nota">Realidade de Aliança — {{ $aliancaCidade->cidade->nome }}</p>
    <table class="ata-table">
        <thead>
            <tr>
                <th class="center">Total de Membros</th>
                <th class="center">Membros Aptos</th>
                <th class="center">Votaram</th>
                <th class="center">Faltaram</th>
                <th class="center">Aderência</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center val-strong">{{ $aliancaCidade->qtd_consagrados ?: '—' }}</td>
                <td class="center val-strong">{{ $aliancaCidade->qtd_eleitorado }}</td>
                <td class="center val-strong">{{ $aliancaCidade->votos_registrados }}</td>
                <td class="center val-strong">{{ $aliancaCidade->qtd_consagrados ? $faltaram : '—' }}</td>
                <td class="center val-pct">{{ number_format($adPct, 2, ',', '') }}%</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($mostrarVida)
    @php
        $vidaTotalConsagrados = $todasCidades->sum('qtd_consagrados_vida');
        $vidaTotalEleit       = $todasCidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0));
        $vidaTotalVotaram     = array_sum($vidaVotaramPorCidade);
        $vidaFaltaram         = max(0, $vidaTotalConsagrados - $vidaTotalVotaram);
        $vidaAdPct            = $vidaTotalConsagrados > 0 ? floor($vidaTotalVotaram / $vidaTotalConsagrados * 10000) / 100 : 0;
    @endphp
    <p class="nota" style="{{ $mostrarAlianca ? 'margin-top:.8rem;' : '' }}">Realidade de Vida — Todas as Missões</p>
    <table class="ata-table">
        <thead>
            <tr>
                <th class="center">Total de Membros</th>
                <th class="center">Eleitores Aptos</th>
                <th class="center">Votaram</th>
                <th class="center">Faltaram</th>
                <th class="center">Aderência</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center val-strong">{{ $vidaTotalConsagrados ?: '—' }}</td>
                <td class="center val-strong">{{ $vidaTotalEleit }}</td>
                <td class="center val-strong">{{ $vidaTotalVotaram }}</td>
                <td class="center val-strong">{{ $vidaTotalConsagrados ? $vidaFaltaram : '—' }}</td>
                <td class="center val-pct">{{ number_format($vidaAdPct, 2, ',', '') }}%</td>
            </tr>
        </tbody>
    </table>

    @if($todasCidades->count() > 1)
    <p class="nota">Participação Vida por Missão</p>
    <table class="ata-table">
        <thead>
            <tr>
                <th>Missão</th>
                <th class="center">Membros</th>
                <th class="center">Votaram</th>
                <th class="center">Aderência</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todasCidades as $ec)
            @php
                $ecMembros = $ec->qtd_consagrados_vida ?? 0;
                $ecVot     = $vidaVotaramPorCidade[$ec->cidade_id] ?? 0;
                $ecAd      = $ecMembros > 0 ? floor($ecVot / $ecMembros * 10000) / 100 : 0;
            @endphp
            <tr>
                <td>{{ $ec->cidade->nome }}</td>
                <td class="center">{{ $ecMembros ?: '—' }}</td>
                <td class="center val-strong">{{ $ecVot }}</td>
                <td class="center val-pct">{{ number_format($ecAd, 2, ',', '') }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="center">{{ $vidaTotalConsagrados ?: '—' }}</td>
                <td class="center">{{ $vidaTotalVotaram }}</td>
                <td class="center">{{ number_format($vidaAdPct, 2, ',', '') }}%</td>
            </tr>
        </tfoot>
    </table>
    @endif
    @endif

    {{-- ── 2. Resultados ──────────────────────────────────────── --}}
    <div class="section-title">2. Resultados</div>

    @foreach($eleicao->perguntas->sortBy('ordem') as $pergunta)
        @php $isVida = $pergunta->escopo === 'vida'; @endphp
        @if($filtro === 'alianca' && $isVida) @continue @endif
        @if($filtro === 'vida'    && !$isVida) @continue @endif

        @if(!$isVida)
            @php $preCheck = $pergunta->opcoes->where('cidade_id', $aliancaCidade->cidade_id); @endphp
            @if($preCheck->isEmpty()) @continue @endif
        @endif

        <div class="sub-title">
            {{ $loop->iteration }}. {{ $pergunta->pergunta }}
        </div>

        @if($isVida)
            @php
                $opcoesVida = $pergunta->opcoes
                    ->map(function($opcao) use ($votosRaw, $pergunta) {
                        $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                        return $opcao;
                    })->sortByDesc('total_votos');
                if ($semZeros) $opcoesVida = $opcoesVida->filter(fn($o) => $o->total_votos > 0);
                $totalVida = $opcoesVida->sum('total_votos');
            @endphp
            <p class="nota">Placar Total Geral</p>
            <table class="ata-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Candidato</th>
                        <th class="center">Votos</th>
                        <th class="center">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($opcoesVida as $opcao)
                        @php $pctOpcao = $totalVida > 0 ? round($opcao->total_votos / $totalVida * 100, 1) : 0; @endphp
                        <tr>
                            <td style="width:30px;color:#9ca3af;">{{ $loop->iteration }}</td>
                            <td>{{ $opcao->nome }}</td>
                            <td class="center val-strong">{{ $opcao->total_votos }}</td>
                            <td class="center val-pct">{{ $pctOpcao }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($todasCidades->count() > 1)
                <p class="nota">Votos por missão</p>
                <table class="ata-table">
                    <thead>
                        <tr>
                            <th>Missão</th>
                            @foreach($opcoesVida as $opcao)
                                <th class="center" style="font-size:.68rem;">{{ $opcao->nome }}</th>
                            @endforeach
                            <th class="center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todasCidades as $ec)
                        @php $totalPorCidade = 0; @endphp
                        <tr>
                            <td>{{ $ec->cidade->nome }}</td>
                            @foreach($opcoesVida as $opcao)
                                @php $v = $votosPorCidade["{$pergunta->id}_{$opcao->id}_{$ec->cidade_id}"] ?? 0; $totalPorCidade += $v; @endphp
                                <td class="center">{{ $v }}</td>
                            @endforeach
                            <td class="center val-strong">{{ $totalPorCidade }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @else
            @php
                $opcoesCidade = $pergunta->opcoes->where('cidade_id', $aliancaCidade->cidade_id)
                    ->map(function($opcao) use ($votosRaw, $pergunta) {
                        $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                        return $opcao;
                    })->sortByDesc('total_votos');
                if ($semZeros) $opcoesCidade = $opcoesCidade->filter(fn($o) => $o->total_votos > 0);
                $totalVotosPergunta = $opcoesCidade->sum('total_votos');
            @endphp
            <table class="ata-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Candidato</th>
                        <th class="center">Votos</th>
                        <th class="center">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($opcoesCidade as $opcao)
                        @php $pctOpcao = $totalVotosPergunta > 0 ? round($opcao->total_votos / $totalVotosPergunta * 100, 1) : 0; @endphp
                        <tr>
                            <td style="width:30px;color:#9ca3af;">{{ $loop->iteration }}</td>
                            <td>{{ $opcao->nome }}</td>
                            <td class="center val-strong">{{ $opcao->total_votos }}</td>
                            <td class="center val-pct">{{ $pctOpcao }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- ── 3. Auditoria ───────────────────────────────────────── --}}
    <div class="section-title">3. Auditoria</div>

    @if($mostrarAlianca)
    <p class="nota">Realidade de Aliança — {{ $aliancaCidade->cidade->nome }}</p>
    <table class="audit-table">
        <tbody>
            <tr>
                <td>Horário de abertura</td>
                <td>{{ $aliancaCidade->data_abertura?->format('d/m/Y H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <td>Horário de encerramento</td>
                <td>{{ $aliancaCidade->data_encerramento?->format('d/m/Y H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <td>Aberta por</td>
                <td>{{ $aliancaCidade->abertaPor?->nome ?? '—' }}</td>
            </tr>
            <tr>
                <td>Encerrada por</td>
                <td>{{ $aliancaCidade->encerradaPor?->nome ?? '—' }}</td>
            </tr>
            <tr>
                <td>Total Consagrados</td>
                <td><strong>{{ $aliancaCidade->qtd_consagrados ?: '—' }}</strong></td>
            </tr>
            <tr>
                <td>Membros Aptos</td>
                <td><strong>{{ $aliancaCidade->qtd_eleitorado ?: '—' }}</strong></td>
            </tr>
            <tr>
                <td>Total realizado</td>
                <td>
                    @php
                        $pctRealizado = $aliancaCidade->qtd_consagrados > 0
                            ? floor($aliancaCidade->votos_registrados / $aliancaCidade->qtd_consagrados * 10000) / 100
                            : 0;
                    @endphp
                    <strong>{{ $aliancaCidade->votos_registrados }}</strong>
                    @if($aliancaCidade->qtd_consagrados > 0)
                        &nbsp;<span class="val-pct">({{ number_format($pctRealizado, 2, ',', '') }}%)</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($mostrarVida)
    <p class="nota" style="{{ $mostrarAlianca ? 'margin-top:.8rem;' : '' }}">Realidade de Vida — Todas as Missões</p>
    <table class="audit-table">
        <tbody>
            <tr>
                <td>Horário de abertura</td>
                <td>{{ $eleicao->data_abertura_vida?->format('d/m/Y H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <td>Horário de encerramento</td>
                <td>{{ $eleicao->data_encerramento_vida?->format('d/m/Y H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <td>Total Consagrados</td>
                <td><strong>{{ $vidaTotalConsagrados ?: '—' }}</strong></td>
            </tr>
            <tr>
                <td>Membros Aptos</td>
                <td><strong>{{ $vidaTotalEleit ?? $todasCidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0)) }}</strong></td>
            </tr>
            <tr>
                <td>Total realizado</td>
                <td>
                    @php
                        $vTot = $vidaTotalVotaram ?? array_sum($vidaVotaramPorCidade);
                        $pctVidaRealizado = isset($vidaTotalConsagrados) && $vidaTotalConsagrados > 0
                            ? floor($vTot / $vidaTotalConsagrados * 10000) / 100
                            : 0;
                    @endphp
                    <strong>{{ $vTot }}</strong>
                    @if(isset($vidaTotalConsagrados) && $vidaTotalConsagrados > 0)
                        &nbsp;<span class="val-pct">({{ number_format($pctVidaRealizado, 2, ',', '') }}%)</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- ── 4. Assinatura ──────────────────────────────────────── --}}
    <div class="assinatura-wrapper">
        <div class="section-title" style="margin-top:2rem;">4. Assinatura</div>
        <div class="assinatura-block">
            <div class="assinatura-linha"></div>
            <div class="assinatura-label">Responsável</div>
        </div>
    </div>

    {{-- ── Rodapé ────────────────────────────────────────────── --}}
    <div class="doc-footer">
        Ata gerada automaticamente pelo sistema Assembleia Digital / Assessoria de Gestão — Comunidade Recado em {{ now()->format('d/m/Y \à\s H:i:s') }}
    </div>

</div>
</body>
</html>
