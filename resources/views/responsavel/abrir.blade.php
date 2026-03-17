@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Abrir Votacao</h2>
        <p class="text-muted mb-0">{{ $eleicaoCidade->eleicao->titulo }}</p>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card border-success" style="max-width: 480px;">
    <div class="card-header bg-success text-white">
        <strong>Confirmacao de Abertura</strong>
    </div>
    <div class="card-body">
        <p>Ao confirmar, a votacao sera <strong>aberta imediatamente</strong> para sua cidade.</p>
        <p class="text-muted small mb-4">Esta acao fica registrada com seu usuario e horario.</p>

        <form method="POST" action="{{ route('responsavel.confirmarAbrir', $eleicaoCidade) }}">
            @csrf
            <div class="mb-3">
                <label for="senha" class="form-label">Confirme sua senha para continuar</label>
                <input type="password" id="senha" name="senha"
                    class="form-control @error('senha') is-invalid @enderror"
                    autofocus required>
                @error('senha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-success w-100">Confirmar Abertura</button>
        </form>
    </div>
</div>
@endsection
