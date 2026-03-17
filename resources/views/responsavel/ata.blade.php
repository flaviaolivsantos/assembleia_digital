<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ata — {{ $eleicao->titulo }} — {{ $eleicaoCidade->cidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: serif; font-size: 14px; color: #000; }
        .ata-header { text-align: center; margin-bottom: 2rem; }
        .ata-header h1 { font-size: 1.4rem; font-weight: bold; text-transform: uppercase; }
        .section-title { font-weight: bold; border-bottom: 1px solid #000; margin-top: 1.5rem; margin-bottom: 0.5rem; }
        .linha-assinatura { border-top: 1px solid #000; width: 300px; margin-top: 2.5rem; }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body class="p-4">

<div class="no-print mb-3 d-flex gap-2">
    <button onclick="window.print()" class="btn btn-dark btn-sm">Imprimir / Salvar PDF</button>
    <a href="{{ route('responsavel.resultados', $eleicaoCidade) }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
</div>

<div class="ata-header">
    <h1>Ata de Eleição</h1>
    <h2>{{ $eleicao->titulo }}</h2>
    <p>Missão: {{ $eleicaoCidade->cidade->nome }} &middot; Data: {{ $eleicao->data_eleicao->format('d/m/Y') }}</p>
</div>

<div class="section-title">1. Participacao</div>
@php
    $adPct = $eleicaoCidade->qtd_eleitorado > 0 ? round($eleicaoCidade->qtd_membros / $eleicaoCidade->qtd_eleitorado * 100, 1) : 0;
    $apPct = $eleicaoCidade->qtd_membros    > 0 ? round($eleicaoCidade->votos_registrados / $eleicaoCidade->qtd_membros * 100, 1) : 0;
@endphp
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Eleitores Aptos</th>
            <th class="text-center">Compareceram</th>
            <th class="text-center">Votaram</th>
            <th class="text-center">Aderência</th>
            <th class="text-center">Aproveit.</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $eleicaoCidade->qtd_eleitorado }}</td>
            <td class="text-center">{{ $eleicaoCidade->qtd_membros }}</td>
            <td class="text-center">{{ $eleicaoCidade->votos_registrados }}</td>
            <td class="text-center">{{ $adPct }}%</td>
            <td class="text-center">{{ $apPct }}%</td>
        </tr>
    </tbody>
</table>

<div class="section-title mt-4">2. Resultados</div>

@foreach($eleicao->perguntas->sortBy('ordem') as $pergunta)
    @php $isVida = $pergunta->escopo === 'vida'; @endphp
    <p class="fw-bold mb-1 mt-3">
        {{ $loop->iteration }}. {{ $pergunta->pergunta }}
        <small class="fw-normal text-muted">(Realidade de {{ $isVida ? 'Vida' : 'Aliança' }})</small>
    </p>

    @if($isVida)
        @php
            $opcoesVida = $pergunta->opcoes
                ->map(function($opcao) use ($votosRaw, $pergunta) {
                    $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                    return $opcao;
                })->sortByDesc('total_votos');
            $totalVida = $opcoesVida->sum('total_votos');
        @endphp
        <p class="text-muted small mb-1"><em>Placar Nacional</em></p>
        <table class="table table-bordered table-sm mb-3">
            <thead><tr><th>Candidato</th><th class="text-center">Votos</th><th class="text-center">%</th></tr></thead>
            <tbody>
                @foreach($opcoesVida as $opcao)
                    @php $pctOpcao = $totalVida > 0 ? round($opcao->total_votos / $totalVida * 100, 1) : 0; @endphp
                    <tr><td>{{ $opcao->nome }}</td><td class="text-center">{{ $opcao->total_votos }}</td><td class="text-center">{{ $pctOpcao }}%</td></tr>
                @endforeach
            </tbody>
        </table>
        {{-- Participação por missão na ata --}}
        @if($todasCidades->count() > 1)
        <p class="text-muted small mb-1"><em>Participação por missão</em></p>
        <table class="table table-bordered table-sm mb-3">
            <thead><tr><th>Missão</th><th class="text-center">Aptos</th><th class="text-center">Comparec.</th><th class="text-center">Votaram</th></tr></thead>
            <tbody>
                @foreach($todasCidades as $ec)
                    <tr @if($ec->cidade_id === $eleicaoCidade->cidade_id) class="fw-semibold" @endif>
                        <td>{{ $ec->cidade->nome }}</td>
                        <td class="text-center">{{ $ec->qtd_eleitorado }}</td>
                        <td class="text-center">{{ $ec->qtd_membros }}</td>
                        <td class="text-center">{{ $ec->votos_registrados }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @else
        @php
            $opcoesCidade = $pergunta->opcoes->where('cidade_id', $eleicaoCidade->cidade_id)
                ->map(function($opcao) use ($votosRaw, $pergunta) {
                    $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                    return $opcao;
                })->sortByDesc('total_votos');
            $totalVotosPergunta = $opcoesCidade->sum('total_votos');
        @endphp
        <table class="table table-bordered table-sm mb-3">
            <thead><tr><th>Candidato</th><th class="text-center">Votos</th><th class="text-center">%</th></tr></thead>
            <tbody>
                @foreach($opcoesCidade as $opcao)
                    @php $pctOpcao = $totalVotosPergunta > 0 ? round($opcao->total_votos / $totalVotosPergunta * 100, 1) : 0; @endphp
                    <tr><td>{{ $opcao->nome }}</td><td class="text-center">{{ $opcao->total_votos }}</td><td class="text-center">{{ $pctOpcao }}%</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endforeach

<div class="section-title mt-4">3. Assinatura</div>
<div class="linha-assinatura mt-4"></div>
<p class="mb-0 mt-1">Responsavel — {{ $eleicaoCidade->cidade->nome }}</p>

<p class="mt-4 text-muted small text-end">
    Documento gerado automaticamente pelo sistema Assembleia Digital em {{ now()->format('d/m/Y H:i') }}.
</p>

</body>
</html>
