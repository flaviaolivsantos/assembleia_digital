@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Encerrar Votação — Realidade de Vida</h2>
        <p class="text-muted mb-0">{{ $eleicao->titulo }}</p>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>

<div class="card border-danger" style="max-width: 460px;">
    <div class="card-body">
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Atenção: Esta ação é irreversível.</strong><br>
            Ao encerrar a votação Vida, nenhum novo voto poderá ser registrado.
        </div>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('responsavel.vida.confirmarEncerrar', $eleicao) }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Justificativa</label>
                <textarea name="justificativa"
                          class="form-control @error('justificativa') is-invalid @enderror"
                          rows="3" required
                          placeholder="Descreva o motivo do encerramento...">{{ old('justificativa') }}</textarea>
                @error('justificativa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Mínimo 10 caracteres. Será registrado no log.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirme sua senha</label>
                <div class="password-field">
                    <input type="password" id="senha" name="senha"
                           class="form-control @error('senha') is-invalid @enderror"
                           placeholder="Sua senha" autocomplete="current-password" autofocus required>
                    <button type="button" class="password-toggle" tabindex="-1"
                            onclick="toggleSenha('senha','ico-senha')">
                        <i class="bi bi-eye" id="ico-senha"></i>
                    </button>
                </div>
                @error('senha')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-stop-circle me-2"></i>Confirmar Encerramento — Realidade de Vida
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSenha(inputId, icoId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(icoId);
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
@endpush
@endsection
