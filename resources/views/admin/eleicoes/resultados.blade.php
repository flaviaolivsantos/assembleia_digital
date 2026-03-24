@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Resultados — {{ $eleicao->titulo }}</h2>
        <span class="text-muted">{{ $eleicao->data_eleicao->format('d/m/Y') }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.eleicoes.ata', $eleicao) }}" class="btn btn-outline-dark" target="_blank">
            Imprimir Ata
        </a>
        <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="btn btn-outline-secondary">Voltar</a>
    </div>
</div>

{{-- Resumo por missão --}}
@php
    $totalAptosGeral    = $eleicao->cidades->sum('qtd_eleitorado');
    $totalComparecGeral = $eleicao->cidades->sum('qtd_membros');
    $totalVotaramGeral  = $eleicao->cidades->sum('votos_registrados');
    $pctAdGeral = $totalAptosGeral    > 0 ? round($totalComparecGeral / $totalAptosGeral    * 100, 1) : 0;
    $pctApGeral = $totalComparecGeral > 0 ? round($totalVotaramGeral  / $totalComparecGeral * 100, 1) : 0;

    $totalVidaEleitores = $eleicao->cidades->sum(fn($ec) => ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0));
    $totalVidaVotaram   = array_sum($vidaVotaramPorCidade);
    $pctVidaAp = $totalVidaEleitores > 0 ? round($totalVidaVotaram / $totalVidaEleitores * 100, 1) : 0;
@endphp

{{-- Card geral --}}
<div class="card mb-3 border-primary">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Geral (todas as missões)</h6>

        @if($temAlianca)
        <p class="text-muted small mb-1 fw-semibold"><span class="badge text-bg-secondary me-1">Aliança</span></p>
        <div class="row text-center g-2 mb-3">
            <div class="col-4"><div class="fw-bold fs-5">{{ $totalAptosGeral }}</div><div class="text-muted small">Aptos</div></div>
            <div class="col-4"><div class="fw-bold fs-5 text-primary">{{ $totalComparecGeral }}</div><div class="text-muted small">Compareceram</div></div>
            <div class="col-4"><div class="fw-bold fs-5 text-success">{{ $totalVotaramGeral }}</div><div class="text-muted small">Votaram</div></div>
        </div>
        <div class="d-flex justify-content-between mb-1"><small>Aderência</small><small><strong>{{ $pctAdGeral }}%</strong></small></div>
        <div class="progress mb-2" style="height:6px"><div class="progress-bar bg-primary" style="width:{{ $pctAdGeral }}%"></div></div>
        <div class="d-flex justify-content-between mb-1"><small>Aproveitamento</small><small><strong>{{ $pctApGeral }}%</strong></small></div>
        <div class="progress {{ $temVida ? 'mb-3' : '' }}" style="height:6px"><div class="progress-bar bg-success" style="width:{{ $pctApGeral }}%"></div></div>
        @endif

        @if($temVida)
        <p class="text-muted small mb-1 fw-semibold"><span class="badge text-bg-primary me-1">Vida</span></p>
        <div class="row text-center g-2 mb-3">
            <div class="col-6"><div class="fw-bold fs-5">{{ $totalVidaEleitores }}</div><div class="text-muted small">Remotos (aptos)</div></div>
            <div class="col-6"><div class="fw-bold fs-5 text-success">{{ $totalVidaVotaram }}</div><div class="text-muted small">Votaram</div></div>
        </div>
        <div class="d-flex justify-content-between mb-1"><small>Aproveitamento</small><small><strong>{{ $pctVidaAp }}%</strong></small></div>
        <div class="progress" style="height:6px"><div class="progress-bar bg-success" style="width:{{ $pctVidaAp }}%"></div></div>
        @endif
    </div>
</div>

{{-- Cards por missão --}}
<div class="row g-3 mb-4">
    @foreach($eleicao->cidades as $ec)
        <div class="col-md-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    @php
                        $pctAd = $ec->qtd_eleitorado > 0 ? round($ec->qtd_membros / $ec->qtd_eleitorado * 100, 1) : 0;
                        $pctAp = $ec->qtd_membros    > 0 ? round($ec->votos_registrados / $ec->qtd_membros * 100, 1) : 0;
                        $cidVidaEleit   = ($ec->qtd_presencial_vida ?? 0) + ($ec->qtd_vida ?? 0);
                        $cidVidaVotaram = $vidaVotaramPorCidade[$ec->cidade_id] ?? 0;
                        $pctVidaApCid   = $cidVidaEleit > 0 ? round($cidVidaVotaram / $cidVidaEleit * 100, 1) : 0;
                    @endphp
                    <h6 class="card-title mb-2">{{ $ec->cidade->nome }}</h6>

                    @if($temAlianca)
                    <p class="text-muted" style="font-size:.68rem;margin-bottom:.25rem;"><span class="badge text-bg-secondary" style="font-size:.65rem;">Aliança</span></p>
                    <div class="d-flex justify-content-around text-center mb-2">
                        <div><div class="fw-bold">{{ $ec->qtd_eleitorado }}</div><div class="text-muted" style="font-size:.7rem">Aptos</div></div>
                        <div><div class="fw-bold text-primary">{{ $ec->qtd_membros }}</div><div class="text-muted" style="font-size:.7rem">Comparec.</div></div>
                        <div><div class="fw-bold text-success">{{ $ec->votos_registrados }}</div><div class="text-muted" style="font-size:.7rem">Votaram</div></div>
                    </div>
                    <div class="progress mb-1" style="height: 5px;" title="Aderência {{ $pctAd }}%">
                        <div class="progress-bar bg-primary" style="width: {{ $pctAd }}%"></div>
                    </div>
                    <div class="progress {{ $temVida ? 'mb-2' : '' }}" style="height: 5px;" title="Aproveitamento {{ $pctAp }}%">
                        <div class="progress-bar bg-success" style="width: {{ $pctAp }}%"></div>
                    </div>
                    <small class="text-muted d-block {{ $temVida ? 'mb-2' : 'mt-1' }}">Ader. {{ $pctAd }}% · Aprov. {{ $pctAp }}%</small>
                    @endif

                    @if($temVida)
                    <p class="text-muted" style="font-size:.68rem;margin-bottom:.25rem;"><span class="badge text-bg-primary" style="font-size:.65rem;">Vida</span></p>
                    <div class="d-flex justify-content-around text-center mb-2">
                        <div><div class="fw-bold">{{ $cidVidaEleit }}</div><div class="text-muted" style="font-size:.7rem">Eleitores</div></div>
                        <div><div class="fw-bold text-success">{{ $cidVidaVotaram }}</div><div class="text-muted" style="font-size:.7rem">Votaram</div></div>
                    </div>
                    <div class="progress" style="height: 5px;" title="Aproveitamento Vida {{ $pctVidaApCid }}%">
                        <div class="progress-bar bg-success" style="width: {{ $pctVidaApCid }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-1">Aprov. {{ $pctVidaApCid }}%</small>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Resultados por pergunta --}}
{{-- Auditoria por máquina --}}
@if($votosPorMaquina->isNotEmpty())
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Auditoria — Votos por Máquina</strong>
        <span class="badge text-bg-secondary">{{ $votosPorMaquina->count() }} máquina(s)</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Máquina</th>
                    <th>Missão</th>
                    <th class="text-end">Votos registrados</th>
                </tr>
            </thead>
            <tbody>
                @php $totalMaquinas = 0; $missaoAtual = null; @endphp
                @foreach($votosPorMaquina as $row)
                    @php
                        $nomeMissao = $row->maquina?->cidade->nome ?? '—';
                        $totalMaquinas += $row->total_votos;
                    @endphp
                    <tr>
                        <td>{{ $row->maquina?->nome ?? 'Desconhecida' }}</td>
                        <td><span class="badge text-bg-light text-dark border">{{ $nomeMissao }}</span></td>
                        <td class="text-end fw-semibold">{{ $row->total_votos }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="2" class="fw-semibold">Total presencial</td>
                    <td class="text-end fw-bold">{{ $totalMaquinas }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

@foreach($eleicao->perguntas->sortBy('ordem') as $pergunta)
    @php
        $isVida = $pergunta->escopo === 'vida';
        $nomesMissoes = $eleicao->cidades->map(fn($ec) => $ec->cidade->nome)->join(' e ');
    @endphp
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ $loop->iteration }}. {{ $pergunta->pergunta }}</strong>
            <span class="badge {{ $isVida ? 'text-bg-primary' : 'text-bg-secondary' }}">
                Realidade de {{ $isVida ? 'Vida' : 'Aliança' }}
            </span>
        </div>
        <div class="card-body">
            @if($isVida)
                {{-- Vida: placar consolidado --}}
                @php
                    $opcoesVida = $pergunta->opcoes
                        ->map(function($opcao) use ($votosRaw, $pergunta) {
                            $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                            return $opcao;
                        })->sortByDesc('total_votos');
                    $totalVidaVotos = $opcoesVida->sum('total_votos');
                @endphp
                <p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.5px">
                    Realidade de Vida ({{ $nomesMissoes }})
                </p>
                <table class="table table-sm table-hover mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Candidato</th>
                            @foreach($eleicao->cidades as $ec)
                                <th class="text-end">{{ $ec->cidade->nome }}</th>
                            @endforeach
                            <th class="text-end">Total</th>
                            <th style="width:160px">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opcoesVida as $i => $opcao)
                            <tr @if($i === 0 && $totalVidaVotos > 0) class="table-success" @endif>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>{{ $opcao->nome }}</td>
                                @foreach($eleicao->cidades as $ec)
                                    <td class="text-end text-muted">
                                        {{ $votosPorCidade["{$pergunta->id}_{$opcao->id}_{$ec->cidade_id}"] ?? 0 }}
                                    </td>
                                @endforeach
                                <td class="text-end fw-semibold">{{ $opcao->total_votos }}</td>
                                <td>
                                    @php $pctOpcao = $totalVidaVotos > 0 ? round($opcao->total_votos / $totalVidaVotos * 100, 1) : 0; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:8px"><div class="progress-bar" style="width:{{ $pctOpcao }}%"></div></div>
                                        <small class="text-muted" style="min-width:36px">{{ $pctOpcao }}%</small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                {{-- Aliança: placar por missão --}}
                @foreach($eleicao->cidades as $ec)
                    @php
                        $opcoesCidade = $pergunta->opcoes->where('cidade_id', $ec->cidade_id)
                            ->map(function($opcao) use ($votosRaw, $pergunta) {
                                $opcao->total_votos = $votosRaw->get($pergunta->id . '_' . $opcao->id)?->total ?? 0;
                                return $opcao;
                            })->sortByDesc('total_votos');
                        $totalVotosPergunta = $opcoesCidade->sum('total_votos');
                    @endphp
                    @if($opcoesCidade->isNotEmpty())
                        <p class="fw-semibold small text-muted text-uppercase mb-2 mt-3" style="letter-spacing:.5px">
                            Realidade de Aliança ({{ $ec->cidade->nome }})
                        </p>
                        <table class="table table-sm table-hover mb-3">
                            <thead class="table-light">
                                <tr><th>#</th><th>Candidato</th><th class="text-end">Votos</th><th style="width:200px">%</th></tr>
                            </thead>
                            <tbody>
                                @foreach($opcoesCidade as $i => $opcao)
                                    <tr @if($i === 0 && $totalVotosPergunta > 0) class="table-success" @endif>
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td>{{ $opcao->nome }}</td>
                                        <td class="text-end fw-semibold">{{ $opcao->total_votos }}</td>
                                        <td>
                                            @php $pctOpcao = $totalVotosPergunta > 0 ? round($opcao->total_votos / $totalVotosPergunta * 100, 1) : 0; @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:8px"><div class="progress-bar" style="width:{{ $pctOpcao }}%"></div></div>
                                                <small class="text-muted" style="min-width:40px">{{ $pctOpcao }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@endforeach
@endsection
