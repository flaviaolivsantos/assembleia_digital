@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Candidatos</h2>
        <p class="text-muted mb-0">
            {{ $eleicao->titulo }} &rsaquo; {{ $pergunta->pergunta }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.eleicoes.perguntas.opcoes.create', [$eleicao, $pergunta]) }}" class="btn btn-primary">+ Adicionar Candidato</a>
        <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="btn btn-outline-secondary">Voltar</a>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

@if($pergunta->escopo === 'vida')
    {{-- Realidade de Vida: candidatos nacionais, sem agrupamento por missão --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Candidatos Realidade de Vida <span class="text-muted fw-normal small ms-1">({{ $opcoes->count() }})</span></strong>
            <span class="badge text-bg-primary">Realidade de Vida</span>
        </div>
        <div class="card-body">
            @if($opcoes->isEmpty())
                <p class="text-muted mb-0">Nenhum candidato cadastrado.</p>
            @else
                <div class="row g-3">
                    @foreach($opcoes as $opcao)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100">
                                <img src="{{ $opcao->foto_url }}" class="card-img-top"
                                    style="height: 180px; object-fit: cover;" alt="{{ $opcao->nome }}">
                                <div class="card-body p-2 text-center">
                                    <p class="mb-2 fw-semibold">{{ $opcao->nome }}</p>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.eleicoes.perguntas.opcoes.edit', [$eleicao, $pergunta, $opcao]) }}"
                                            class="btn btn-sm btn-outline-secondary">Editar</a>
                                        <form method="POST"
                                            action="{{ route('admin.eleicoes.perguntas.opcoes.destroy', [$eleicao, $pergunta, $opcao]) }}"
                                            onsubmit="return confirm('Remover {{ $opcao->nome }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Remover</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@else
    {{-- Realidade de Aliança: candidatos por missão --}}
    @foreach($cidades as $ec)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $ec->cidade->nome }} <span class="text-muted fw-normal small ms-1">({{ $opcoesPorCidade[$ec->cidade_id]->count() }})</span></strong>
                <span class="badge text-bg-secondary">Realidade de Aliança</span>
            </div>
            <div class="card-body">
                @if($opcoesPorCidade[$ec->cidade_id]->isEmpty())
                    <p class="text-muted mb-0">Nenhum candidato cadastrado para esta missão.</p>
                @else
                    <div class="row g-3">
                        @foreach($opcoesPorCidade[$ec->cidade_id] as $opcao)
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <img src="{{ $opcao->foto_url }}" class="card-img-top"
                                        style="height: 180px; object-fit: cover;" alt="{{ $opcao->nome }}">
                                    <div class="card-body p-2 text-center">
                                        <p class="mb-2 fw-semibold">{{ $opcao->nome }}</p>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('admin.eleicoes.perguntas.opcoes.edit', [$eleicao, $pergunta, $opcao]) }}"
                                                class="btn btn-sm btn-outline-secondary">Editar</a>
                                            <form method="POST"
                                                action="{{ route('admin.eleicoes.perguntas.opcoes.destroy', [$eleicao, $pergunta, $opcao]) }}"
                                                onsubmit="return confirm('Remover {{ $opcao->nome }}?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Remover</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif
@endsection
