@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Encerrar Votacao</h2>
        <p class="text-muted mb-0">{{ $eleicaoCidade->eleicao->titulo }}</p>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card border-danger" style="max-width: 480px;">
    <div class="card-header bg-danger text-white">
        <strong>Confirmacao de Encerramento</strong>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <strong>Atencao:</strong> Esta acao e irreversivel. Apos encerrar, nenhum voto adicional sera aceito.
        </div>

        <p class="text-muted small mb-4">
            <strong>Votos registrados:</strong> {{ $eleicaoCidade->votos_registrados }} /
            {{ $eleicaoCidade->qtd_membros }} membros
        </p>

        <form method="POST" action="{{ route('responsavel.confirmarEncerrar', $eleicaoCidade) }}">
            @csrf
            <div class="mb-3">
                <label for="justificativa" class="form-label">Justificativa do encerramento</label>
                <textarea id="justificativa" name="justificativa" rows="3"
                    class="form-control @error('justificativa') is-invalid @enderror"
                    placeholder="Ex: Todos os membros presentes ja votaram." required>{{ old('justificativa') }}</textarea>
                @error('justificativa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label">Confirme sua senha</label>
                <input type="password" id="senha" name="senha"
                    class="form-control @error('senha') is-invalid @enderror"
                    required>
                @error('senha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger w-100">Confirmar Encerramento</button>
        </form>
    </div>
</div>
@endsection
