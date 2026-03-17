@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Missão</h2>
    <a href="{{ route('admin.cidades.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.cidades.update', $cidade) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" id="nome" name="nome"
                    class="form-control @error('nome') is-invalid @enderror"
                    value="{{ old('nome', $cidade->nome) }}" autofocus required>
                @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection
