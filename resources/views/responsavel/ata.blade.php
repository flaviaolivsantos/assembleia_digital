<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ata — {{ $eleicao->titulo }} — {{ $eleicaoCidade->cidade->nome }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ── Variáveis ──────────────────────────────────────── */
        :root {
            --azul:   #2C3E50;
            --ciano:  #00BCD4;
            --cinza-claro:  #F8F9FA;
            --cinza-medio:  #CED4DA;
            --cinza-escuro: #495057;
            --branco: #FFFFFF;
        }

        /* ── Base ───────────────────────────────────────────── */
        * { box-sizing: border-box; }
        body {
            font-family: 'Roboto', Lato, sans-serif;
            font-size: 13px;
            color: var(--cinza-escuro);
            background: #fff;
            margin: 0; padding: 0;
        }

        /* ── Área de impressão ──────────────────────────────── */
        .ata-page {
            max-width: 21cm;
            margin: 0 auto;
            padding: 2cm 2cm 3cm;
            min-height: 29.7cm;
            position: relative;
        }

        /* ── Barra de ações (oculta na impressão) ───────────── */
        .no-print {
            position: sticky;
            top: 0; z-index: 100;
            background: var(--azul);
            padding: .6rem 1rem;
            display: flex; gap: .5rem; align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        .no-print .btn-imprimir {
            background: var(--ciano); color: #fff; border: none;
            padding: .4rem .9rem; border-radius: .3rem;
            font-family: 'Montserrat', sans-serif; font-weight: 600;
            font-size: .82rem; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .no-print .btn-voltar {
            background: transparent; color: #fff;
            border: 1px solid rgba(255,255,255,.35);
            padding: .4rem .9rem; border-radius: .3rem;
            font-size: .82rem; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .no-print .btn-imprimir:hover { background: #00a5bb; }
        .no-print .btn-voltar:hover   { border-color: #fff; }

        /* ── Cabeçalho do documento ─────────────────────────── */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--cinza-medio);
        }
        .doc-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--azul);
            letter-spacing: .3px;
        }
        .doc-brand-sub {
            font-size: .72rem;
            color: var(--cinza-escuro);
            font-weight: 400;
            margin-top: 2px;
        }
        .doc-meta {
            text-align: right;
            font-size: .75rem;
            color: var(--cinza-escuro);
            line-height: 1.6;
        }

        /* ── Título central ─────────────────────────────────── */
        .doc-title-block {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--ciano);
        }
        .doc-title-label {
            font-family: 'Montserrat', sans-serif;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--ciano);
            margin-bottom: .3rem;
        }
        .doc-title-main {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--azul);
            margin-bottom: .2rem;
        }
        .doc-title-sub {
            font-size: .85rem;
            color: var(--cinza-escuro);
        }

        /* ── Títulos de seção ───────────────────────────────── */
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            color: var(--azul);
            margin: 1.4rem 0 .6rem;
            padding-bottom: .3rem;
            border-bottom: 1px solid var(--cinza-medio);
            page-break-after: avoid;
        }
        .section-title .section-icon {
            display: inline-block;
            width: 4px; height: 14px;
            background: var(--ciano);
            border-radius: 2px;
            margin-right: 8px;
            vertical-align: middle;
        }

        /* ── Subtítulos ─────────────────────────────────────── */
        .sub-title {
            font-family: 'Montserrat', sans-serif;
            font-size: .82rem;
            font-weight: 600;
            color: var(--cinza-escuro);
            margin: .9rem 0 .4rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ── Tabelas ────────────────────────────────────────── */
        .ata-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: .85rem;
            page-break-inside: avoid;
        }
        .ata-table thead tr {
            background-color: var(--azul);
        }
        .ata-table thead th {
            color: var(--branco);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: .55rem .75rem;
            border: 1px solid var(--azul);
            text-align: left;
        }
        .ata-table thead th.center { text-align: center; }
        .ata-table tbody tr:nth-child(odd)  { background-color: var(--branco); }
        .ata-table tbody tr:nth-child(even) { background-color: var(--cinza-claro); }
        .ata-table tbody td {
            padding: .5rem .75rem;
            border: 1px solid var(--cinza-medio);
            color: var(--cinza-escuro);
            vertical-align: middle;
        }
        .ata-table tbody td.center { text-align: center; }
        .ata-table tfoot td {
            padding: .5rem .75rem;
            border: 1px solid var(--cinza-medio);
            background: var(--cinza-claro);
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: .82rem;
            color: var(--azul);
        }
        .ata-table tfoot td.center { text-align: center; }

        /* Valores em destaque */
        .val-azul  { color: var(--azul);  font-weight: 700; }
        .val-ciano { color: var(--ciano); font-weight: 600; }

        /* ── Auditoria: tabela de 2 colunas ─────────────────── */
        .audit-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: .85rem;
        }
        .audit-table tr:nth-child(odd)  td:first-child { background: var(--cinza-claro); }
        .audit-table tr:nth-child(even) td:first-child { background: var(--branco); }
        .audit-table td {
            padding: .45rem .75rem;
            border: 1px solid var(--cinza-medio);
            vertical-align: middle;
        }
        .audit-table td:first-child {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: .78rem;
            color: var(--azul);
            width: 42%;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .audit-table td:last-child { color: var(--cinza-escuro); }

        /* ── Assinatura ─────────────────────────────────────── */
        .assinatura-block {
            margin-top: 2.5rem;
        }
        .assinatura-linha {
            border-bottom: 1px solid var(--cinza-medio);
            width: 55%;
            margin-bottom: .4rem;
        }
        .assinatura-label {
            font-size: .82rem;
            color: var(--cinza-escuro);
        }

        /* ── Rodapé fixo ────────────────────────────────────── */
        .doc-footer {
            position: fixed;
            bottom: .8cm; left: 2cm; right: 2cm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--cinza-medio);
            padding-top: .35rem;
            font-size: .7rem;
            color: var(--cinza-medio);
        }

        /* ── Print ──────────────────────────────────────────── */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .ata-page { padding: 0; margin: 0; max-width: none; min-height: auto; }
            @page {
                size: A4 portrait;
                margin: 2cm 2cm 2.5cm;
            }
            .section-title, .sub-title { page-break-after: avoid; }
            .ata-table { page-break-inside: avoid; }
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

{{-- ── Barra de ações (não imprime) ──────────────────────── --}}
<div class="no-print">
    <button onclick="window.print()" class="btn-imprimir">
        &#128438; Imprimir / Salvar PDF
    </button>
    <a href="{{ $toggleUrl }}" class="btn-voltar" style="{{ $semZeros ? 'background:rgba(0,188,212,.2);border-color:#00BCD4;color:#00BCD4;' : '' }}">
        {{ $semZeros ? '&#10003; Ocultando zeros' : 'Ocultar zeros' }}
    </a>
    <a href="{{ route('responsavel.resultados', $eleicaoCidade) }}?filtro={{ $filtro }}{{ $filtro === 'alianca' ? '&alianca_cidade_id='.$aliancaCidade->cidade_id : '' }}" class="btn-voltar">
        &#8592; Voltar
    </a>
</div>

{{-- ── Página do documento ────────────────────────────────── --}}
<div class="ata-page">

    {{-- Cabeçalho --}}
    <div class="doc-header">
        <div>
            <div class="doc-brand">Assembleia Digital</div>
            <div class="doc-brand-sub">Sistema de Votação</div>
        </div>
        <div class="doc-meta">
            Gerado em {{ now()->format('d/m/Y \à\s H:i') }}<br>
            @if($mostrarVida && !$mostrarAlianca)
                Todas as Missões
            @else
                Missão: {{ $aliancaCidade->cidade->nome }}
            @endif
        </div>
    </div>

    {{-- Título central --}}
    <div class="doc-title-block">
        <div class="doc-title-label">Documento Oficial</div>
        <div class="doc-title-main">Ata de Eleição</div>
        <div class="doc-title-sub">
            {{ $eleicao->titulo }}
            &nbsp;&middot;&nbsp;
            @if($mostrarVida && !$mostrarAlianca)
                Todas as Missões
            @else
                {{ $aliancaCidade->cidade->nome }}
            @endif
            &nbsp;&middot;&nbsp;
            {{ $eleicao->data_eleicao->format('d/m/Y') }}
        </div>
    </div>

    {{-- 1. Participação --}}
    <div class="section-title">
        <span class="section-icon"></span>1. Participação
    </div>

    @if($mostrarAlianca)
    @php
        $adPct = $aliancaCidade->qtd_eleitorado > 0
            ? round($aliancaCidade->qtd_membros       / $aliancaCidade->qtd_eleitorado * 100, 1) : 0;
        $apPct = $aliancaCidade->qtd_membros    > 0
            ? round($aliancaCidade->votos_registrados / $aliancaCidade->qtd_membros    * 100, 1) : 0;
    @endphp
    <p style="font-size:.75rem;color:#6c757d;margin:.2rem 0 .4rem;font-style:italic;">Realidade de Aliança — {{ $aliancaCidade->cidade->nome }}</p>
    <table class="ata-table">
        <thead>
            <tr>
                <th class="center">Eleitores Aptos</th>
                <th class="center">Compareceram</th>
                <th class="center">Votaram</th>
                <th class="center">Aderência</th>
                <th class="center">Aproveit.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center val-azul">{{ $aliancaCidade->qtd_membros }}</td>
                <td class="center val-azul">{{ $aliancaCidade->qtd_membros }}</td>
                <td class="center val-azul">{{ $aliancaCidade->votos_registrados }}</td>
                <td class="center val-ciano">{{ $adPct }}%</td>
                <td class="center val-ciano">{{ $apPct }}%</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($mostrarVida)
    @php
        $vidaTotalEleit  = $todasCidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0));
        $vidaTotalVotaram = array_sum($vidaVotaramPorCidade);
        $vidaApPct        = $vidaTotalEleit > 0 ? round($vidaTotalVotaram / $vidaTotalEleit * 100, 1) : 0;
    @endphp
    <p style="font-size:.75rem;color:#6c757d;margin:.8rem 0 .4rem;font-style:italic;">Realidade de Vida — Todas as Missões</p>
    <table class="ata-table">
        <thead>
            <tr>
                <th class="center">Eleitores Aptos</th>
                <th class="center">Votaram</th>
                <th class="center">Aproveit.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center val-azul">{{ $vidaTotalEleit }}</td>
                <td class="center val-azul">{{ $vidaTotalVotaram }}</td>
                <td class="center val-ciano">{{ $vidaApPct }}%</td>
            </tr>
        </tbody>
    </table>

    @if($todasCidades->count() > 1)
    <p style="font-size:.75rem;color:#6c757d;margin:.4rem 0 .4rem;font-style:italic;">Participação Vida por Missão</p>
    <table class="ata-table">
        <thead>
            <tr>
                <th>Missão</th>
                <th class="center">Eleitores</th>
                <th class="center">Votaram</th>
                <th class="center">Aproveit.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todasCidades as $ec)
            @php
                $ecEleit = ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0);
                $ecVot   = $vidaVotaramPorCidade[$ec->cidade_id] ?? 0;
                $ecAp    = $ecEleit > 0 ? round($ecVot / $ecEleit * 100, 1) : 0;
            @endphp
            <tr>
                <td>{{ $ec->cidade->nome }}</td>
                <td class="center">{{ $ecEleit }}</td>
                <td class="center val-azul">{{ $ecVot }}</td>
                <td class="center val-ciano">{{ $ecAp }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="center">{{ $vidaTotalEleit }}</td>
                <td class="center">{{ $vidaTotalVotaram }}</td>
                <td class="center">{{ $vidaApPct }}%</td>
            </tr>
        </tfoot>
    </table>
    @endif
    @endif

    {{-- 2. Resultados --}}
    <div class="section-title">
        <span class="section-icon"></span>2. Resultados
    </div>

    @foreach($eleicao->perguntas->sortBy('ordem') as $pergunta)
        @php $isVida = $pergunta->escopo === 'vida'; @endphp
        @if($filtro === 'alianca' && $isVida) @continue @endif
        @if($filtro === 'vida'    && !$isVida) @continue @endif

        {{-- Para aliança: verifica se há candidatos para esta cidade antes de exibir --}}
        @if(!$isVida)
            @php
                $preCheck = $pergunta->opcoes->where('cidade_id', $aliancaCidade->cidade_id);
            @endphp
            @if($preCheck->isEmpty()) @continue @endif
        @endif

        <div class="sub-title">
            {{ $loop->iteration }}. {{ $pergunta->pergunta }}
            <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#6c757d;font-size:.75rem;">
                — Realidade de {{ $isVida ? 'Vida' : 'Aliança' }}
            </span>
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
            <p style="font-size:.75rem;color:#6c757d;margin:.2rem 0 .4rem;font-style:italic;">Placar Total Geral</p>
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
                    @foreach($opcoesVida as $i => $opcao)
                        @php $pctOpcao = $totalVida > 0 ? round($opcao->total_votos / $totalVida * 100, 1) : 0; @endphp
                        <tr>
                            <td style="width:30px;color:#6c757d;">{{ $loop->iteration }}</td>
                            <td>{{ $opcao->nome }}</td>
                            <td class="center val-azul">{{ $opcao->total_votos }}</td>
                            <td class="center val-ciano">{{ $pctOpcao }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- votos por missão detalhados dentro de cada pergunta vida --}}
            @if($todasCidades->count() > 1)
                <p style="font-size:.75rem;color:#6c757d;margin:.2rem 0 .4rem;font-style:italic;">Votos por missão</p>
                <table class="ata-table">
                    <thead>
                        <tr>
                            <th>Missão</th>
                            @foreach($opcoesVida as $opcao)
                                <th class="center" style="font-size:.72rem;">{{ $opcao->nome }}</th>
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
                            <td class="center val-azul">{{ $totalPorCidade }}</td>
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
                    @foreach($opcoesCidade as $i => $opcao)
                        @php $pctOpcao = $totalVotosPergunta > 0 ? round($opcao->total_votos / $totalVotosPergunta * 100, 1) : 0; @endphp
                        <tr>
                            <td style="width:30px;color:#6c757d;">{{ $loop->iteration }}</td>
                            <td>{{ $opcao->nome }}</td>
                            <td class="center val-azul">{{ $opcao->total_votos }}</td>
                            <td class="center val-ciano">{{ $pctOpcao }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- 3. Auditoria --}}
    <div class="section-title">
        <span class="section-icon"></span>3. Auditoria
    </div>

    @if($mostrarAlianca)
    <p style="font-size:.75rem;color:#6c757d;margin:.2rem 0 .4rem;font-style:italic;">Realidade de Aliança — {{ $aliancaCidade->cidade->nome }}</p>
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
                <td>Total esperado de votos</td>
                <td class="val-azul">{{ $aliancaCidade->qtd_membros }}</td>
            </tr>
            <tr>
                <td>Total realizado</td>
                <td>
                    <span class="val-azul">{{ $aliancaCidade->votos_registrados }}</span>
                    @if($aliancaCidade->qtd_membros > 0)
                        &nbsp;<span class="val-ciano">({{ round($aliancaCidade->votos_registrados / $aliancaCidade->qtd_membros * 100, 1) }}%)</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($mostrarVida)
    <p style="font-size:.75rem;color:#6c757d;margin:.8rem 0 .4rem;font-style:italic;">Realidade de Vida — Todas as Missões</p>
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
                <td>Total esperado de votos</td>
                <td class="val-azul">{{ $vidaTotalEleit ?? $todasCidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0)) }}</td>
            </tr>
            <tr>
                <td>Total realizado</td>
                <td>
                    @php $vTot = $vidaTotalVotaram ?? array_sum($vidaVotaramPorCidade); $vEleit = $vidaTotalEleit ?? $todasCidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0)); @endphp
                    <span class="val-azul">{{ $vTot }}</span>
                    @if($vEleit > 0)
                        &nbsp;<span class="val-ciano">({{ round($vTot / $vEleit * 100, 1) }}%)</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- 4. Assinatura --}}
    <div class="section-title">
        <span class="section-icon"></span>4. Assinatura
    </div>
    <div class="assinatura-block">
        <div class="assinatura-linha"></div>
        <div class="assinatura-label">Responsável — {{ $aliancaCidade->cidade->nome }}</div>
    </div>

    {{-- Rodapé fixo --}}
    <div class="doc-footer">
        <span>Documento gerado automaticamente pelo sistema Assembleia Digital em {{ now()->format('d/m/Y H:i') }}.</span>
    </div>

</div>{{-- /ata-page --}}

</body>
</html>
