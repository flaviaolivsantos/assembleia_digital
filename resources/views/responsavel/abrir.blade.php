@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Abrir Votação</h2>
        <p class="text-muted mb-0">{{ $eleicaoCidade->eleicao->titulo }}</p>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card border-success" style="max-width: 480px;">
    <div class="card-header bg-success text-white">
        <strong>Confirmação de Abertura</strong>
    </div>
    <div class="card-body">
        <p>Ao confirmar, a votação será <strong>aberta imediatamente</strong> para sua cidade.</p>
        <p class="text-muted small mb-4">Esta ação fica registrada com seu usuário e horário.</p>

        <form method="POST" action="{{ route('responsavel.confirmarAbrir', $eleicaoCidade) }}">
            @csrf
            <div class="mb-3">
                <label for="senha" class="form-label">Confirme sua senha para continuar</label>
                <div class="input-group">
                    <input type="password" id="senha" name="senha"
                        class="form-control @error('senha') is-invalid @enderror"
                        autofocus required>
                    <button type="button" class="btn btn-outline-secondary" tabindex="-1"
                            onclick="toggleSenha('senha','ico-senha')">
                        <i class="bi bi-eye" id="ico-senha"></i>
                    </button>
                </div>
                @error('senha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-success w-100">Confirmar Abertura</button>
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
