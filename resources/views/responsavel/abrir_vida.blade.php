@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Abrir Votação — Realidade de Vida</h2>
        <p class="text-muted mb-0">{{ $eleicao->titulo }}</p>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>

<div class="card" style="max-width: 460px;">
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="bi bi-globe me-2"></i>
            Esta ação abre a votação da <strong>Realidade de Vida</strong> para <strong>todas as cidades</strong> simultaneamente.
        </div>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('responsavel.vida.confirmarAbrir', $eleicao) }}">
            @csrf

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

            <button type="submit" class="btn btn-resp-primario w-100">
                <i class="bi bi-play-circle me-2"></i>Confirmar Abertura — Realidade de Vida
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
