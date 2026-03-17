@extends('layouts.app')

@section('content')
<div class="text-center py-4">
    <h2 class="text-success mb-1">Presenca Registrada!</h2>
    <p class="text-muted mb-5">Entregue o token abaixo ao eleitor para iniciar a votacao.</p>

    <div class="d-inline-block border border-2 border-success rounded-3 px-5 py-4 mb-5 bg-light">
        <div class="text-muted small mb-2">TOKEN DE VOTACAO</div>
        <div class="fw-bold display-5 font-monospace tracking-wide">{{ $token }}</div>
    </div>

    <div class="alert alert-warning d-inline-block" style="max-width: 480px;">
        <strong>Atencao:</strong> Este token nao sera exibido novamente. Anote ou entregue imediatamente ao eleitor.
    </div>

    <div class="mt-4">
        <a href="{{ route('mesario.presencas.index', $eleicaoCidade) }}" class="btn btn-primary">
            Registrar Proximo Membro
        </a>
    </div>
</div>
@endsection
