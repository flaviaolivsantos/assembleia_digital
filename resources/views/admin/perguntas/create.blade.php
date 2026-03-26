@extends('layouts.admin')
@section('page-title', 'Nova Pergunta')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Nova Pergunta</h2>
        <p class="text-muted mb-0">{{ $eleicao->titulo }}</p>
    </div>
    <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.eleicoes.perguntas.store', $eleicao) }}">
            @csrf

            <div class="mb-3">
                <label for="pergunta" class="form-label">Texto da Pergunta</label>
                <textarea id="pergunta" name="pergunta" rows="3"
                    class="form-control @error('pergunta') is-invalid @enderror"
                    required>{{ old('pergunta') }}</textarea>
                @error('pergunta')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="qtd_respostas" class="form-label">Quantidade obrigatoria de respostas</label>
                <input type="number" id="qtd_respostas" name="qtd_respostas" min="1"
                    class="form-control @error('qtd_respostas') is-invalid @enderror"
                    value="{{ old('qtd_respostas', 1) }}" required style="max-width: 120px;">
                <div class="form-text">Ex: 3 significa que o eleitor deve escolher exatamente 3 candidatos.</div>
                @error('qtd_respostas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Realidade</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="escopo" id="escopo_alianca"
                            value="alianca" {{ old('escopo', 'alianca') === 'alianca' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="escopo_alianca">
                            <strong>Aliança</strong>
                            <div class="text-muted small">Cada missão tem seus candidatos</div>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="escopo" id="escopo_vida"
                            value="vida" {{ old('escopo') === 'vida' ? 'checked' : '' }}>
                        <label class="form-check-label" for="escopo_vida">
                            <strong>Vida</strong>
                            <div class="text-muted small">Candidatos únicos, todas as missões votam juntas</div>
                        </label>
                    </div>
                </div>
                @error('escopo')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4" id="campo-missao" style="{{ old('escopo', 'alianca') === 'alianca' ? '' : 'display:none' }}">
                <label for="cidade_id" class="form-label">Missão <span class="text-muted small">(para qual missão vai adicionar candidatos agora)</span></label>
                <select id="cidade_id" name="cidade_id" class="form-select @error('cidade_id') is-invalid @enderror">
                    <option value="">— Selecione para ir direto aos candidatos —</option>
                    @foreach($cidades as $ec)
                        <option value="{{ $ec->cidade_id }}" {{ old('cidade_id') == $ec->cidade_id ? 'selected' : '' }}>
                            {{ $ec->cidade->nome }}
                        </option>
                    @endforeach
                </select>
                @error('cidade_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Opcional. Se selecionada, você será redirecionado para adicionar candidatos desta missão ao salvar.</div>
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>

<script>
const radios     = document.querySelectorAll('input[name="escopo"]');
const campoMissao = document.getElementById('campo-missao');

radios.forEach(r => r.addEventListener('change', function () {
    campoMissao.style.display = this.value === 'alianca' ? '' : 'none';
    if (this.value !== 'alianca') document.getElementById('cidade_id').value = '';
}));
</script>
@endsection
