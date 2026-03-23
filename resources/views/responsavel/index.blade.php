@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="mb-0">Painel do Responsável Local</h2>
    <p class="text-muted mb-0">Gerencie a votação na sua cidade.</p>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@forelse($eleicoes as $eleicao)
    <div class="card eleicao-card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="eleicao-titulo mb-0">{{ $eleicao->titulo }}</h5>
                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $eleicao->data_eleicao->format('d/m/Y') }}</small>
            </div>
            @if($eleicao->status === 'aberta')
                <span class="status-tag status-aberta"><i class="bi bi-play-circle-fill me-1"></i>Em andamento</span>
            @elseif($eleicao->status === 'encerrada')
                <span class="status-tag status-encerrada"><i class="bi bi-check-circle-fill me-1"></i>Encerrada</span>
            @else
                <span class="status-tag status-aguardando"><i class="bi bi-clock me-1"></i>Aguardando</span>
            @endif
        </div>

        <div class="card-body p-0">

            {{-- ── Realidade de Vida (election-wide) ── --}}
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary">Realidade de Vida</span>
                            @if($eleicao->aberta_vida)
                                <span class="status-tag status-aberta"><i class="bi bi-play-circle-fill me-1"></i>Aberta</span>
                            @elseif($eleicao->data_encerramento_vida)
                                <span class="status-tag status-encerrada"><i class="bi bi-check-circle-fill me-1"></i>Encerrada</span>
                            @else
                                <span class="status-tag status-aguardando"><i class="bi bi-clock me-1"></i>Aguardando</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-0">Votação nacional — única abertura para todas as cidades.</p>
                        @if($eleicao->data_abertura_vida)
                            <small class="text-muted">Aberta em: {{ $eleicao->data_abertura_vida->format('d/m/Y H:i') }}</small>
                        @endif
                        @if($eleicao->data_encerramento_vida)
                            <small class="text-muted ms-3">Encerrada em: {{ $eleicao->data_encerramento_vida->format('d/m/Y H:i') }}</small>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-2 align-items-end">
                        @if(!$eleicao->aberta_vida && !$eleicao->data_encerramento_vida)
                            <a href="{{ route('responsavel.vida.abrir', $eleicao) }}" class="btn btn-resp-primario">
                                <i class="bi bi-play-circle me-1"></i>Abrir Vida
                            </a>
                        @endif
                        @if($eleicao->aberta_vida)
                            <a href="{{ route('responsavel.vida.encerrar', $eleicao) }}" class="btn btn-resp-encerrar">
                                <i class="bi bi-stop-circle me-1"></i>Encerrar Vida
                            </a>
                        @endif
                        @if(auth()->user()->perfil === 'admin' && !$eleicao->aberta_vida && $eleicao->data_encerramento_vida)
                            <a href="{{ route('responsavel.vida.reabrir', $eleicao) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reabrir Vida
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Realidade de Aliança (per city) ── --}}
            @foreach($eleicao->cidades as $ec)
            <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-secondary">Realidade de Aliança</span>
                            @if(auth()->user()->perfil === 'admin')
                                <span class="fw-semibold">{{ $ec->cidade->nome }}</span>
                            @endif
                            @if($ec->aberta)
                                <span class="status-tag status-aberta"><i class="bi bi-play-circle-fill me-1"></i>Aberta</span>
                            @elseif($ec->data_encerramento)
                                <span class="status-tag status-encerrada"><i class="bi bi-check-circle-fill me-1"></i>Finalizada</span>
                            @else
                                <span class="status-tag status-aguardando"><i class="bi bi-clock me-1"></i>Aguardando</span>
                            @endif
                        </div>

                        <div class="d-flex gap-4 mt-2">
                            <div>
                                <div class="resp-metric-label">Vida (limite)</div>
                                <div class="resp-metric-value">{{ $ec->qtd_vida }}</div>
                            </div>
                            <div>
                                <div class="resp-metric-label">Aliança (membros)</div>
                                <div class="resp-metric-value">{{ $ec->qtd_membros }}</div>
                            </div>
                            <div>
                                <div class="resp-metric-label">Votos Aliança</div>
                                <div class="resp-metric-value">{{ $ec->votos_registrados }}</div>
                            </div>
                            @if($ec->qtd_membros > 0)
                            <div>
                                <div class="resp-metric-label">Participação</div>
                                <div class="resp-metric-value">{{ number_format(($ec->votos_registrados / $ec->qtd_membros) * 100, 0) }}%</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 align-items-end">
                        @if(!$ec->data_encerramento)
                            <a href="{{ route('responsavel.membros', $ec) }}" class="btn btn-resp-secundario btn-sm">
                                <i class="bi bi-people me-1"></i>Alterar Membros
                            </a>
                        @endif

                        @if(!$ec->aberta && !$ec->data_encerramento)
                            <a href="{{ route('responsavel.abrir', $ec) }}" class="btn btn-resp-primario">
                                <i class="bi bi-play-circle me-1"></i>Abrir Aliança
                            </a>
                        @endif

                        @if($ec->aberta)
                            <a href="{{ route('responsavel.encerrar', $ec) }}" class="btn btn-resp-encerrar">
                                <i class="bi bi-stop-circle me-1"></i>Encerrar Aliança
                            </a>
                        @endif

                        @if($ec->data_encerramento)
                            <a href="{{ route('responsavel.resultados', $ec) }}" class="btn btn-resp-resultado btn-sm">
                                <i class="bi bi-bar-chart me-1"></i>Ver Resultados
                            </a>
                        @endif
                        @if(auth()->user()->perfil === 'admin' && !$ec->aberta && $ec->data_encerramento)
                            <a href="{{ route('responsavel.alianca.reabrir', $ec) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reabrir Aliança
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
@empty
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Nenhuma eleição disponível para sua cidade.
    </div>
@endforelse
@endsection
