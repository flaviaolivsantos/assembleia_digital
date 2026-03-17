@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Resultados — {{ $eleicao->titulo }}</h2>
        <span class="text-muted">{{ $eleicaoCidade->cidade->nome }} &middot; {{ $eleicao->data_eleicao->format('d/m/Y') }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('responsavel.ata', $eleicaoCidade) }}" class="btn btn-outline-dark" target="_blank">
            Imprimir Ata
        </a>
        <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>
</div>

{{-- Resumo --}}
<div class="card mb-4">
    <div class="card-body">
        @php
            $eleitorado     = $eleicaoCidade->qtd_eleitorado;
            $compareceram   = $eleicaoCidade->qtd_membros;
            $votaram        = $eleicaoCidade->votos_registrados;
            $pctAderencia   = $eleitorado   > 0 ? round($compareceram / $eleitorado   * 100, 1) : 0;
            $pctAproveit    = $compareceram > 0 ? round($votaram      / $compareceram * 100, 1) : 0;
        @endphp
        <h5 class="text-center mb-4">{{ $eleicaoCidade->cidade->nome }}</h5>
        <div class="row text-center g-3">
            <div class="col-4">
                <div class="fs-3 fw-bold">{{ $eleitorado }}</div>
                <div class="text-muted small">Eleitores aptos</div>
            </div>
            <div class="col-4">
                <div class="fs-3 fw-bold text-primary">{{ $compareceram }}</div>
                <div class="text-muted small">Compareceram</div>
            </div>
            <div class="col-4">
                <div class="fs-3 fw-bold text-success">{{ $votaram }}</div>
                <div class="text-muted small">Votaram</div>
            </div>
        </div>
        <hr class="my-3">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-1">
                    <small class="fw-semibold">Aderência</small>
                    <small>{{ $compareceram }} / {{ $eleitorado }} &middot; <strong>{{ $pctAderencia }}%</strong></small>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: {{ $pctAderencia }}%"></div>
                </div>
                <div class="form-text">Dos eleitores aptos, quantos compareceram.</div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-1">
                    <small class="fw-semibold">Aproveitamento</small>
                    <small>{{ $votaram }} / {{ $compareceram }} &middot; <strong>{{ $pctAproveit }}%</strong></small>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ $pctAproveit }}%"></div>
                </div>
                <div class="form-text">Dos que compareceram, quantos efetivamente votaram.</div>
            </div>
        </div>
    </div>
</div>

{{-- Auditoria por máquina (apenas desta missão) --}}
@php
    $maquinasDaMissao = $votosPorMaquina->filter(fn($r) => $r->maquina?->cidade_id === $eleicaoCidade->cidade_id);
@endphp
@if($maquinasDaMissao->isNotEmpty())
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Auditoria — Votos por Máquina</strong>
        <span class="badge text-bg-secondary">{{ $maquinasDaMissao->count() }} máquina(s)</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Máquina</th>
                    <th class="text-end">Votos registrados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maquinasDaMissao as $row)
                    <tr>
                        <td>{{ $row->maquina?->nome ?? 'Desconhecida' }}</td>
                        <td class="text-end fw-semibold">{{ $row->total_votos }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td class="fw-semibold">Total presencial</td>
                    <td class="text-end fw-bold">{{ $maquinasDaMissao->sum('total_votos') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- Resultados por pergunta --}}
@foreach($eleicao->perguntas->sortBy('ordem') as $pergunta)
    @php
        $isVida = $pergunta->escopo === 'vida';
        $nomesMissoes = $todasCidades->map(fn($ec) => $ec->cidade->nome)->join(' e ');

        if ($isVida) {
            $opcoesCidade = $pergunta->opcoes
                ->map(function($opcao) use ($votosRaw, $pergunta) {
                    $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                    return $opcao;
                })->sortByDesc('total_votos');
        } else {
            $opcoesCidade = $pergunta->opcoes->where('cidade_id', $eleicaoCidade->cidade_id)
                ->map(function($opcao) use ($votosRaw, $pergunta) {
                    $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                    return $opcao;
                })->sortByDesc('total_votos');
        }
        $totalVotosPergunta = $opcoesCidade->sum('total_votos');
    @endphp

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ $loop->iteration }}. {{ $pergunta->pergunta }}</strong>
            <span class="badge {{ $isVida ? 'text-bg-primary' : 'text-bg-secondary' }}">
                Realidade de {{ $isVida ? 'Vida' : 'Aliança' }}
            </span>
        </div>
        <div class="card-body">

            @if(!$isVida)
                <p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.5px">
                    Realidade de Aliança ({{ $eleicaoCidade->cidade->nome }})
                </p>
            @else
                <p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.5px">
                    Realidade de Vida ({{ $nomesMissoes }})
                </p>
            @endif

            {{-- Placar --}}
            @if($isVida)
                {{-- Vida: tabela com colunas por missão --}}
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Candidato</th>
                            @foreach($todasCidades as $ec)
                                <th class="text-end">{{ $ec->cidade->nome }}</th>
                            @endforeach
                            <th class="text-end">Total</th>
                            <th style="width:140px">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opcoesCidade as $i => $opcao)
                            <tr @if($i === 0 && $totalVotosPergunta > 0) class="table-success" @endif>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>{{ $opcao->nome }}</td>
                                @foreach($todasCidades as $ec)
                                    <td class="text-end text-muted">
                                        {{ $votosPorCidade["{$pergunta->id}_{$opcao->id}_{$ec->cidade_id}"] ?? 0 }}
                                    </td>
                                @endforeach
                                <td class="text-end fw-semibold">{{ $opcao->total_votos }}</td>
                                <td>
                                    @php $pctOpcao = $totalVotosPergunta > 0 ? round($opcao->total_votos / $totalVotosPergunta * 100, 1) : 0; @endphp
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:8px"><div class="progress-bar" style="width:{{ $pctOpcao }}%"></div></div>
                                        <small class="text-muted">{{ $pctOpcao }}%</small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                {{-- Aliança: placar simples --}}
                @forelse($opcoesCidade as $i => $opcao)
                    <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded @if($i === 0 && $totalVotosPergunta > 0) bg-success bg-opacity-10 @endif">
                        <span class="text-muted" style="min-width: 24px;">{{ $i + 1 }}.</span>
                        <span class="flex-grow-1">{{ $opcao->nome }}</span>
                        <span class="fw-semibold" style="min-width: 40px; text-align: right;">{{ $opcao->total_votos }}</span>
                        @php $pctOpcao = $totalVotosPergunta > 0 ? round($opcao->total_votos / $totalVotosPergunta * 100, 1) : 0; @endphp
                        <div class="d-flex align-items-center gap-1" style="min-width: 140px;">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $pctOpcao }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pctOpcao }}%</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Nenhum candidato encontrado.</p>
                @endforelse
            @endif

            {{-- Vida: indicadores gerais + participação por missão --}}
            @if($isVida && $todasCidades->count() > 0)
                @php
                    $totalAptos    = $todasCidades->sum('qtd_eleitorado');
                    $totalComparec = $todasCidades->sum('qtd_membros');
                    $totalVotaram  = $todasCidades->sum('votos_registrados');
                    $pctAdGeral    = $totalAptos    > 0 ? round($totalComparec / $totalAptos    * 100, 1) : 0;
                    $pctApGeral    = $totalComparec > 0 ? round($totalVotaram  / $totalComparec * 100, 1) : 0;
                @endphp
                <hr class="my-3">
                <p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.5px">Indicadores Gerais</p>
                <div class="row g-2 mb-3">
                    <div class="col-4 text-center">
                        <div class="fw-bold">{{ $totalAptos }}</div>
                        <div class="text-muted" style="font-size:.75rem">Aptos</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold text-primary">{{ $totalComparec }}</div>
                        <div class="text-muted" style="font-size:.75rem">Compareceram</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold text-success">{{ $totalVotaram }}</div>
                        <div class="text-muted" style="font-size:.75rem">Votaram</div>
                    </div>
                </div>
                <div class="mb-1 d-flex justify-content-between"><small>Aderência geral</small><small><strong>{{ $pctAdGeral }}%</strong></small></div>
                <div class="progress mb-2" style="height:6px"><div class="progress-bar bg-primary" style="width:{{ $pctAdGeral }}%"></div></div>
                <div class="mb-1 d-flex justify-content-between"><small>Aproveitamento geral</small><small><strong>{{ $pctApGeral }}%</strong></small></div>
                <div class="progress mb-3" style="height:6px"><div class="progress-bar bg-success" style="width:{{ $pctApGeral }}%"></div></div>

                <p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.5px">Participação por Missão</p>
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Missão</th>
                            <th class="text-center">Aptos</th>
                            <th class="text-center">Comparec.</th>
                            <th class="text-center">Votaram</th>
                            <th class="text-center">Aderência</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todasCidades as $ec)
                            @php $adPct = $ec->qtd_eleitorado > 0 ? round($ec->qtd_membros / $ec->qtd_eleitorado * 100, 1) : 0; @endphp
                            <tr @if($ec->cidade_id === $eleicaoCidade->cidade_id) class="table-warning" @endif>
                                <td>{{ $ec->cidade->nome }} @if($ec->cidade_id === $eleicaoCidade->cidade_id)<small class="text-muted">(esta)</small>@endif</td>
                                <td class="text-center">{{ $ec->qtd_eleitorado }}</td>
                                <td class="text-center">{{ $ec->qtd_membros }}</td>
                                <td class="text-center">{{ $ec->votos_registrados }}</td>
                                <td class="text-center">{{ $adPct }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endforeach
@endsection
