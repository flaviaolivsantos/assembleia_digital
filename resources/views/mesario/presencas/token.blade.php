@extends('layouts.admin')
@section('page-title', 'Confirmação de Presença')

@section('content')
<div class="text-center py-4">
    <h2 class="text-success mb-1">Presença Registrada!</h2>
    <p class="text-muted mb-5">Entregue o token abaixo ao eleitor para iniciar a votação.</p>

    <div class="d-inline-block border border-2 border-success rounded-3 px-5 py-4 mb-4 bg-light">
        <div class="text-muted small mb-2">TOKEN DE VOTAÇÃO</div>
        <div class="fw-bold display-4 font-monospace tracking-wide">{{ $token }}</div>
    </div>

    {{-- Mensagem WhatsApp --}}
    <div class="card mx-auto mb-4" style="max-width: 500px;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold small">Mensagem para enviar pelo WhatsApp</span>
            <button class="btn btn-sm btn-success" onclick="copiarMensagem()" id="btn-copiar">
                <i class="bi bi-clipboard"></i> Copiar
            </button>
        </div>
        <div class="card-body">
            <p class="text-start small mb-0" id="msg-whats" style="white-space: pre-line;">Olá! Segue seu token para participar da votação:

🗳️ TOKEN: {{ $token }}
🔗 Link: {{ config('app.url') }}/votar

Acesse o link, digite o token e vote!</p>
        </div>
    </div>

    <div class="alert alert-warning d-inline-block mb-4" style="max-width: 500px;">
        <strong>Atenção:</strong> Anote ou envie o token ao eleitor antes de continuar.
    </div>

    <div class="mt-2">
        <a href="{{ route('mesario.presencas.index', $eleicaoCidade) }}" class="btn btn-primary">
            Registrar Próximo Membro
        </a>
    </div>
</div>

@push('scripts')
<script>
function copiarMensagem() {
    const texto = document.getElementById('msg-whats').innerText;
    navigator.clipboard.writeText(texto).then(() => {
        const btn = document.getElementById('btn-copiar');
        btn.innerHTML = '<i class="bi bi-check2"></i> Copiado!';
        btn.classList.replace('btn-success', 'btn-secondary');
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-clipboard"></i> Copiar';
            btn.classList.replace('btn-secondary', 'btn-success');
        }, 2500);
    });
}
</script>
@endpush
@endsection
