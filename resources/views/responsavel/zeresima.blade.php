<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zerésima — {{ $eleicao->titulo }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Coag-Vertical.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --azul:         #2C3E50;
            --ciano:        #00BCD4;
            --cinza-claro:  #F8F9FA;
            --cinza-medio:  #CED4DA;
            --cinza-escuro: #495057;
            --branco:       #FFFFFF;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Roboto', sans-serif; font-size: 13px; color: var(--cinza-escuro); background: #fff; margin: 0; padding: 0; }

        .ata-page { max-width: 21cm; margin: 0 auto; padding: 2cm 2cm 3cm; min-height: 29.7cm; position: relative; }

        .no-print {
            position: sticky; top: 0; z-index: 100;
            background: var(--azul); padding: .6rem 1rem;
            display: flex; gap: .5rem; align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        .no-print .btn-imprimir {
            background: var(--ciano); color: #fff; border: none;
            padding: .4rem .9rem; border-radius: .3rem;
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: .82rem;
            cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .no-print .btn-voltar {
            background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.35);
            padding: .4rem .9rem; border-radius: .3rem; font-size: .82rem;
            cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .no-print .btn-imprimir:hover { background: #00a5bb; }
        .no-print .btn-voltar:hover   { border-color: #fff; }

        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--cinza-medio); }
        .doc-brand { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 1rem; color: var(--azul); letter-spacing: .3px; }
        .doc-brand-sub { font-size: .72rem; color: var(--cinza-escuro); font-weight: 400; margin-top: 2px; }
        .doc-meta { text-align: right; font-size: .75rem; color: var(--cinza-escuro); line-height: 1.6; }

        .doc-title-block { text-align: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--ciano); }
        .doc-title-label { font-family: 'Montserrat', sans-serif; font-size: .68rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--ciano); margin-bottom: .3rem; }
        .doc-title-main  { font-family: 'Montserrat', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--azul); margin-bottom: .2rem; }
        .doc-title-sub   { font-size: .85rem; color: var(--cinza-escuro); }

        .doc-aviso { background: #fff8e1; border: 1px solid #ffe082; border-radius: .4rem; padding: .6rem 1rem; font-size: .82rem; color: #7a5800; margin-bottom: 1.2rem; }

        .section-title { font-family: 'Montserrat', sans-serif; font-size: .95rem; font-weight: 700; color: var(--azul); margin: 1.4rem 0 .6rem; padding-bottom: .3rem; border-bottom: 1px solid var(--cinza-medio); page-break-after: avoid; }
        .section-title .section-icon { display: inline-block; width: 4px; height: 14px; background: var(--ciano); border-radius: 2px; margin-right: 8px; vertical-align: middle; }
        .sub-title { font-family: 'Montserrat', sans-serif; font-size: .82rem; font-weight: 600; color: var(--cinza-escuro); margin: .9rem 0 .4rem; text-transform: uppercase; letter-spacing: .5px; }

        .ata-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; font-size: .85rem; page-break-inside: avoid; }
        .ata-table thead tr { background-color: var(--azul); }
        .ata-table thead th { color: var(--branco); font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: .75rem; text-transform: uppercase; letter-spacing: .4px; padding: .55rem .75rem; border: 1px solid var(--azul); }
        .ata-table thead th.center { text-align: center; }
        .ata-table tbody tr:nth-child(odd)  { background-color: var(--branco); }
        .ata-table tbody tr:nth-child(even) { background-color: var(--cinza-claro); }
        .ata-table tbody td { padding: .5rem .75rem; border: 1px solid var(--cinza-medio); color: var(--cinza-escuro); vertical-align: middle; }
        .ata-table tbody td.center { text-align: center; }
        .zero-badge { display: inline-block; background: var(--cinza-claro); color: var(--cinza-escuro); font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: .78rem; padding: .15em .55em; border-radius: .25rem; border: 1px solid var(--cinza-medio); }

        .audit-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; font-size: .85rem; }
        .audit-table td { padding: .45rem .75rem; border: 1px solid var(--cinza-medio); vertical-align: middle; }
        .audit-table tr:nth-child(odd)  td:first-child { background: var(--cinza-claro); }
        .audit-table tr:nth-child(even) td:first-child { background: var(--branco); }
        .audit-table td:first-child { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: .78rem; color: var(--azul); width: 42%; text-transform: uppercase; letter-spacing: .3px; }

        .assinatura-block { margin-top: 2.5rem; }
        .assinatura-linha { border-bottom: 1px solid var(--cinza-medio); width: 55%; margin-bottom: .4rem; }
        .assinatura-label { font-size: .82rem; color: var(--cinza-escuro); }

        .doc-footer { position: fixed; bottom: .8cm; left: 2cm; right: 2cm; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--cinza-medio); padding-top: .35rem; font-size: .7rem; color: var(--cinza-medio); }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .ata-page { padding: 0; margin: 0; max-width: none; min-height: auto; }
            @page { size: A4 portrait; margin: 2cm 2cm 2.5cm; }
            .section-title, .sub-title { page-break-after: avoid; break-after: avoid; }
            .ata-table  { page-break-inside: auto; break-inside: auto; }
            .ata-table tr { page-break-inside: avoid; break-inside: avoid; }
            .doc-footer { position: static; margin-top: 1.5rem; border-top: 1px solid var(--cinza-medio); padding-top: .35rem; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-imprimir">&#128438; Imprimir / Salvar PDF</button>
    <a href="{{ route('responsavel.index') }}" class="btn-voltar">&#8592; Voltar ao Painel</a>
</div>

<div class="ata-page">

    {{-- Cabeçalho --}}
    <div class="doc-header" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <img src="{{ asset('images/Coag-Vertical.png') }}" alt="Comunidade Recado" style="height:70px;object-fit:contain;">
        </div>
        <div class="doc-meta">
            Gerado em {{ now()->format('d/m/Y \à\s H:i') }}<br>
            @if($escopo === 'alianca')
                Missão: {{ $eleicaoCidade->cidade->nome }}
            @else
                Todas as Missões
            @endif
            <br><strong>Comunidade Recado</strong>
        </div>
    </div>

    {{-- Título central --}}
    <div class="doc-title-block">
        <div class="doc-title-label">Documento Oficial</div>
        <div class="doc-title-main">Zerésima</div>
        <div class="doc-title-sub">
            {{ $eleicao->titulo }}
            &nbsp;&middot;&nbsp;
            @if($escopo === 'alianca')
                Realidade de Aliança — {{ $eleicaoCidade->cidade->nome }}
            @else
                Realidade de Vida — Todas as Missões
            @endif
            &nbsp;&middot;&nbsp;
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="doc-aviso">
        Este documento certifica que, no momento da abertura da votação, todos os candidatos registrados possuem <strong>zero votos</strong>, atestando a integridade do processo eleitoral.
    </div>

    {{-- 1. Dados da Abertura --}}
    <div class="section-title">
        <span class="section-icon"></span>1. Dados da Abertura
    </div>
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
                        Realidade de Vida — Todas as Missões
                    @endif
                </td>
            </tr>
            <tr>
                <td>Data da eleição</td>
                <td>{{ $eleicao->data_eleicao->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Horário de abertura</td>
                <td><strong>{{ now()->format('d/m/Y H:i:s') }}</strong></td>
            </tr>
            @if($escopo === 'alianca')
            <tr>
                <td>Aberta por</td>
                <td>{{ $eleicaoCidade->abertaPor?->nome ?? auth()->user()->name }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- 2. Relação de Candidatos --}}
    <div class="section-title">
        <span class="section-icon"></span>2. Relação de Candidatos (votos zerados)
    </div>

    @foreach($eleicao->perguntas->where('escopo', $escopo)->sortBy('ordem') as $pergunta)
        <div class="sub-title">{{ $loop->iteration }}. {{ $pergunta->pergunta }}</div>

        @php
            $opcoes = $escopo === 'alianca'
                ? $pergunta->opcoes->where('cidade_id', $eleicaoCidade->cidade_id)->sortBy('nome')
                : $pergunta->opcoes->sortBy('nome');
        @endphp

        @if($opcoes->isEmpty())
            <p style="font-size:.8rem;color:#6c757d;font-style:italic;">Nenhum candidato cadastrado.</p>
        @else
        <table class="ata-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Candidato</th>
                    <th class="center">Votos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opcoes as $i => $opcao)
                <tr>
                    <td style="width:30px;color:#6c757d;">{{ $loop->iteration }}</td>
                    <td>{{ $opcao->nome }}</td>
                    <td class="center"><span class="zero-badge">0</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach

    {{-- 3. Assinatura --}}
    <div class="section-title">
        <span class="section-icon"></span>3. Assinatura
    </div>
    <div class="assinatura-block">
        <div class="assinatura-linha"></div>
        <div class="assinatura-label">Responsável</div>
    </div>

    <div class="doc-footer">
        <span>Zerésima gerada automaticamente pelo sistema Comunidade Recado em {{ now()->format('d/m/Y H:i') }}.</span>
    </div>

</div>

</body>
</html>
