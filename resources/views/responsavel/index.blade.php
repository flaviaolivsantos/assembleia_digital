@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="mb-0">Painel do Responsável Local</h2>
    <p class="text-muted mb-0">Gerencie a votação na sua cidade.</p>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@forelse($eleicoes as $ec)
    <div class="card eleicao-card mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

                {{-- Informações da eleição --}}
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h5 class="eleicao-titulo mb-0">{{ $ec->eleicao->titulo }}</h5>
                        @if(auth()->user()->perfil === 'admin')
                            <span class="badge text-bg-secondary">{{ $ec->cidade->nome }}</span>
                        @endif
                        {{-- Status --}}
                        @if($ec->aberta)
                            <span class="status-tag status-aberta"><i class="bi bi-play-circle-fill me-1"></i>Em andamento</span>
                        @elseif($ec->data_encerramento)
                            <span class="status-tag status-encerrada"><i class="bi bi-check-circle-fill me-1"></i>Finalizada</span>
                        @else
                            <span class="status-tag status-aguardando"><i class="bi bi-clock me-1"></i>Aguardando</span>
                        @endif
                    </div>

                    <p class="eleicao-data mb-3">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $ec->eleicao->data_eleicao->format('d/m/Y') }}
                    </p>

                    <div class="d-flex gap-4">
                        <div>
                            <div class="resp-metric-label">Membros</div>
                            <div class="resp-metric-value">{{ $ec->qtd_membros }}</div>
                        </div>
                        <div>
                            <div class="resp-metric-label">Votos</div>
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

                {{-- Ações --}}
                <div class="d-flex flex-column gap-2 align-items-end">
                    @if(!$ec->data_encerramento)
                        <a href="{{ route('responsavel.membros', $ec) }}" class="btn btn-resp-secundario btn-sm">
                            <i class="bi bi-people me-1"></i>Alterar Membros
                        </a>
                    @endif

                    @if(!$ec->aberta && !$ec->data_encerramento)
                        <a href="{{ route('responsavel.abrir', $ec) }}" class="btn btn-resp-primario">
                            <i class="bi bi-play-circle me-1"></i>Abrir Votação
                        </a>
                    @endif

                    @if($ec->aberta)
                        <a href="{{ route('responsavel.encerrar', $ec) }}" class="btn btn-resp-encerrar">
                            <i class="bi bi-stop-circle me-1"></i>Encerrar Votação
                        </a>
                    @endif

                    @if($ec->data_encerramento)
                        <a href="{{ route('responsavel.resultados', $ec) }}" class="btn btn-resp-resultado btn-sm">
                            <i class="bi bi-bar-chart me-1"></i>Ver Resultados
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Nenhuma eleição disponível para sua cidade.
    </div>
@endforelse
@endsection
