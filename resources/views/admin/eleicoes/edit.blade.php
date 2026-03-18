@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Eleição</h2>
    <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.eleicoes.update', $eleicao) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" id="titulo" name="titulo"
                    class="form-control @error('titulo') is-invalid @enderror"
                    value="{{ old('titulo', $eleicao->titulo) }}" autofocus required>
                @error('titulo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="data_eleicao" class="form-label">Data da Eleição</label>
                <input type="date" id="data_eleicao" name="data_eleicao"
                    class="form-control @error('data_eleicao') is-invalid @enderror"
                    value="{{ old('data_eleicao', $eleicao->data_eleicao->format('Y-m-d')) }}" required>
                @error('data_eleicao')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Missões participantes</label>
                @error('cidades')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror
                @foreach($cidades as $cidade)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            name="cidades[]" value="{{ $cidade->id }}"
                            id="cidade_{{ $cidade->id }}"
                            {{ in_array($cidade->id, old('cidades', $cidadesSelecionadas)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="cidade_{{ $cidade->id }}">
                            {{ $cidade->nome }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection
