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
              enctype="multipart/form-data">
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
                <strong>Realidade de Vida</strong> — candidato nacional, visível para todas as missões.
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
                <label for="foto" class="form-label">Foto do Candidato</label>

                @if($opcao->foto)
                    <div class="mb-2">
                        <img id="preview" src="{{ $opcao->foto_url }}" alt="{{ $opcao->nome }}"
                            class="rounded" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                @else
                    <img id="preview" src="" alt="" class="mt-2 rounded d-none"
                        style="width: 120px; height: 120px; object-fit: cover;">
                @endif

                <input type="file" id="foto" name="foto" accept="image/*"
                    class="form-control @error('foto') is-invalid @enderror"
                    onchange="previewFoto(this)">
                <div class="form-text">Deixe em branco para manter a foto atual.</div>
                @error('foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>

<script>
function previewFoto(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
