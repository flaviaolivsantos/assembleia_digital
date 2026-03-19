@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Editar Candidato</h2>
        <p class="text-muted mb-0">{{ $eleicao->titulo }} &rsaquo; {{ $pergunta->pergunta }}</p>
    </div>
    <a href="{{ route('admin.eleicoes.perguntas.opcoes.index', [$eleicao, $pergunta]) }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.eleicoes.perguntas.opcoes.update', [$eleicao, $pergunta, $opcao]) }}"
              enctype="multipart/form-data" id="form-candidato">
            @csrf
            @method('PUT')

            @if($pergunta->escopo === 'alianca')
            <div class="mb-3">
                <label for="cidade_id" class="form-label">Missão</label>
                <select id="cidade_id" name="cidade_id"
                    class="form-select @error('cidade_id') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    @foreach($cidades as $ec)
                        <option value="{{ $ec->cidade_id }}"
                            {{ old('cidade_id', $opcao->cidade_id) == $ec->cidade_id ? 'selected' : '' }}>
                            {{ $ec->cidade->nome }}
                        </option>
                    @endforeach
                </select>
                @error('cidade_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @else
            <div class="alert alert-primary py-2 small mb-3">
                <strong>Realidade de Vida</strong> — Visível para todas as missões.
            </div>
            @endif

            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Candidato</label>
                <input type="text" id="nome" name="nome"
                    class="form-control @error('nome') is-invalid @enderror"
                    value="{{ old('nome', $opcao->nome) }}" required>
                @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Foto do Candidato</label>

                <div id="preview-wrap" class="{{ $opcao->foto ? '' : 'd-none' }} mb-2">
                    <img id="preview" src="{{ $opcao->foto_url }}" alt="{{ $opcao->nome }}"
                        style="width: 120px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6;">
                </div>

                <div>
                    <input type="file" id="foto-input" accept="image/*" class="d-none">
                    <input type="file" id="foto" name="foto" accept="image/*" class="d-none"
                        @error('foto') aria-invalid="true" @enderror>
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="document.getElementById('foto-input').click()">
                        {{ $opcao->foto ? 'Trocar foto' : 'Selecionar foto' }}
                    </button>
                    <span class="text-muted small ms-2">Deixe em branco para manter a atual.</span>
                </div>
                @error('foto')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>

{{-- Modal Cropper --}}
<div class="modal fade" id="modalCrop" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Posicionar foto</h5>
            </div>
            <div class="modal-body text-center" style="background:#f0f0f0; max-height:60vh; overflow:hidden;">
                <img id="crop-img" src="" style="max-width:100%; display:block;">
            </div>
            <div class="modal-footer justify-content-between">
                <span class="text-muted small">Arraste para posicionar. Role para zoom.</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="btn-cancelar-crop">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-confirmar-crop">Confirmar corte</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script>
let cropper = null;
const modal = new bootstrap.Modal(document.getElementById('modalCrop'));

document.getElementById('foto-input').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = document.getElementById('crop-img');
        img.src = e.target.result;

        if (cropper) { cropper.destroy(); cropper = null; }

        modal.show();

        setTimeout(function () {
            cropper = new Cropper(img, {
                aspectRatio: 3 / 2,
                viewMode: 1,
                autoCropArea: 0.9,
                movable: true,
                zoomable: true,
                rotatable: false,
                scalable: false,
            });
        }, 300);
    };
    reader.readAsDataURL(file);
});

document.getElementById('btn-confirmar-crop').addEventListener('click', function () {
    if (!cropper) return;

    cropper.getCroppedCanvas({ width: 450, height: 300 }).toBlob(function (blob) {
        const file = new File([blob], 'foto.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('foto').files = dt.files;

        document.getElementById('preview').src = URL.createObjectURL(blob);
        document.getElementById('preview-wrap').classList.remove('d-none');

        cropper.destroy(); cropper = null;
        modal.hide();
        document.getElementById('foto-input').value = '';
    }, 'image/jpeg', 0.9);
});

document.getElementById('btn-cancelar-crop').addEventListener('click', function () {
    if (cropper) { cropper.destroy(); cropper = null; }
    modal.hide();
    document.getElementById('foto-input').value = '';
});
</script>
@endpush
@endsection
