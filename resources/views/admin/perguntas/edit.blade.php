@extends('layouts.admin')
@section('page-title', 'Editar Pergunta')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Editar Pergunta</h2>
        <p class="text-muted mb-0">{{ $eleicao->titulo }}</p>
    </div>
    <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.eleicoes.perguntas.update', [$eleicao, $pergunta]) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="pergunta" class="form-label">Texto da Pergunta</label>
                <textarea id="pergunta" name="pergunta" rows="3"
                    class="form-control @error('pergunta') is-invalid @enderror"
                    required>{{ old('pergunta', $pergunta->pergunta) }}</textarea>
                @error('pergunta')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="qtd_respostas" class="form-label">Quantidade obrigatoria de respostas</label>
                <input type="number" id="qtd_respostas" name="qtd_respostas" min="1"
                    class="form-control @error('qtd_respostas') is-invalid @enderror"
                    value="{{ old('qtd_respostas', $pergunta->qtd_respostas) }}" required style="max-width: 120px;">
                @error('qtd_respostas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Realidade</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="escopo" id="escopo_alianca"
                            value="alianca" {{ old('escopo', $pergunta->escopo) === 'alianca' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="escopo_alianca">
                            <strong>Aliança</strong>
                            <div class="text-muted small">Cada missão tem seus candidatos</div>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="escopo" id="escopo_vida"
                            value="vida" {{ old('escopo', $pergunta->escopo) === 'vida' ? 'checked' : '' }}>
                        <label class="form-check-label" for="escopo_vida">
                            <strong>Vida</strong>
                            <div class="text-muted small">Candidatos nacionais, todas as missões votam juntas</div>
                        </label>
                    </div>
                </div>
                @error('escopo')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection
