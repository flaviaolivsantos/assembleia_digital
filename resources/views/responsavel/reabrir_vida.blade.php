@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Reabrir Votação — Realidade de Vida</h2>
        <p class="text-muted mb-0">{{ $eleicao->titulo }}</p>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>

<div class="card border-warning" style="max-width: 500px;">
    <div class="card-body">
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Ação exclusiva de administrador.</strong><br>
            Esta ação reabre a votação da <strong>Realidade de Vida</strong> para <strong>todas as cidades</strong>.
            Os votos já registrados <strong>não serão alterados</strong>.
            A justificativa ficará registrada no log da eleição.
        </div>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('responsavel.vida.confirmarReabrir', $eleicao) }}">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold">Motivo da reabertura <span class="text-danger">*</span></label>
                <textarea name="justificativa"
                          class="form-control @error('justificativa') is-invalid @enderror"
                          rows="3" required autofocus
                          placeholder="Descreva detalhadamente o motivo da reabertura...">{{ old('justificativa') }}</textarea>
                @error('justificativa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Mínimo 10 caracteres. Obrigatório.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirme sua senha <span class="text-danger">*</span></label>
                <div class="password-field">
                    <input type="password" id="senha" name="senha"
                           class="form-control @error('senha') is-invalid @enderror"
                           placeholder="Senha do administrador"
                           autocomplete="current-password" required>
                    <button type="button" class="password-toggle" tabindex="-1"
                            onclick="toggleSenha('senha','ico-senha')">
                        <i class="bi bi-eye" id="ico-senha"></i>
                    </button>
                </div>
                @error('senha')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-semibold">
                <i class="bi bi-arrow-counterclockwise me-2"></i>Confirmar Reabertura — Realidade de Vida
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSenha(inputId, icoId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(icoId);
    if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; ico.className = 'bi bi-eye'; }
}
</script>
@endpush
@endsection
