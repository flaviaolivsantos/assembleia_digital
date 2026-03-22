@extends('layouts.app')

@section('content')
<h2 class="mb-4">Painel do Mesário</h2>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@if($eleicoesCidade->isEmpty())
    <div class="alert alert-info">Nenhuma votação aberta no momento para sua missão.</div>
@else
    <p class="text-muted">Selecione a votação que deseja gerenciar:</p>
    <div class="row g-3">
        @foreach($eleicoesCidade as $ec)
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="card-title mb-0">{{ $ec->eleicao->titulo }}</h5>
                            @if(auth()->user()->perfil === 'admin')
                                <span class="badge text-bg-secondary">{{ $ec->cidade->nome }}</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-3">
                            Aberta em {{ \Carbon\Carbon::parse($ec->data_abertura)->format('d/m/Y H:i') }}
                        </p>
                        <a href="{{ route('mesario.presencas.index', $ec) }}" class="btn btn-primary btn-sm">
                            Gerenciar Presenças
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
