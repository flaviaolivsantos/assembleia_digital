@extends('layouts.admin')
@section('page-title', 'Membros')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Alterar Quantidade de Membros</h2>
        <p class="text-muted mb-0">{{ $eleicaoCidade->eleicao->titulo }} — {{ $eleicaoCidade->cidade->nome }}</p>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <p class="text-muted small mb-4">
            Votos vida já registrados: <strong>{{ $eleicaoCidade->votos_registrados_vida }}</strong>.
            O total vida (presencial + remoto) não pode ser menor que este valor.<br>
            Votos aliança já registrados: <strong>{{ $eleicaoCidade->votos_registrados }}</strong>.
            O total aliança (presencial + remoto) não pode ser menor que este valor.
        </p>

        <form method="POST" action="{{ route('responsavel.membros.update', $eleicaoCidade) }}">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold">Total de eleitores aptos</label>
                <input type="number" name="qtd_eleitorado" class="form-control"
                       value="{{ old('qtd_eleitorado', $eleicaoCidade->qtd_eleitorado) }}"
                       min="0" required>
                <div class="form-text">Total de membros que poderiam votar. Usado para calcular o índice de aderência.</div>
            </div>

            <hr class="my-3">

            {{-- Realidade de Vida --}}
            <h6 class="text-primary mb-3"><i class="bi bi-globe me-1"></i>Realidade de Vida</h6>

            <div class="mb-3">
                <label class="form-label">Votarão presencialmente (vida)</label>
                <input type="number" name="qtd_presencial_vida" id="qtd_presencial_vida" class="form-control"
                       value="{{ old('qtd_presencial_vida', $eleicaoCidade->qtd_presencial_vida) }}"
                       min="0" required>
                <div class="form-text">Votos vida liberados pela senha na máquina de votação.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Votarão remotamente (vida)</label>
                <input type="number" name="qtd_vida" id="qtd_vida" class="form-control"
                       value="{{ old('qtd_vida', $eleicaoCidade->qtd_vida) }}"
                       min="0" required>
                <div class="form-text">
                    Tokens vida a serem gerados pelo mesário.
                    @php
                        $tokensVida = \App\Models\TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
                            ->where('cidade_id', $eleicaoCidade->cidade_id)
                            ->where('escopo', 'vida')
                            ->count();
                    @endphp
                    @if($tokensVida > 0)
                        Tokens vida já gerados: <strong>{{ $tokensVida }}</strong>.
                    @endif
                </div>
            </div>

            <div class="alert alert-primary py-2 small mb-4">
                Total vida: <strong id="total_vida">{{ ($eleicaoCidade->qtd_presencial_vida ?? 0) + ($eleicaoCidade->qtd_vida ?? 0) }}</strong> membros
            </div>

            <hr class="my-3">

            {{-- Realidade de Aliança --}}
            <h6 class="text-secondary mb-3"><i class="bi bi-building me-1"></i>Realidade de Aliança</h6>

            <div class="mb-3">
                <label class="form-label">Votarão presencialmente (aliança)</label>
                <input type="number" name="qtd_presencial" class="form-control"
                       value="{{ old('qtd_presencial', $eleicaoCidade->qtd_presencial) }}"
                       min="0" required autofocus>
                <div class="form-text">Votos liberados pela senha na máquina de votação.</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Votarão remotamente (aliança)</label>
                <input type="number" name="qtd_remoto" class="form-control"
                       value="{{ old('qtd_remoto', $eleicaoCidade->qtd_remoto) }}"
                       min="0" required>
                <div class="form-text">
                    Tokens aliança a serem gerados pelo mesário.
                    @php
                        $tokensAlianca = \App\Models\TokenVotacao::where('eleicao_id', $eleicaoCidade->eleicao_id)
                            ->where('cidade_id', $eleicaoCidade->cidade_id)
                            ->where('escopo', 'alianca')
                            ->count();
                    @endphp
                    @if($tokensAlianca > 0)
                        Tokens aliança já gerados: <strong>{{ $tokensAlianca }}</strong>.
                    @endif
                </div>
            </div>

            <div class="alert alert-secondary py-2 small mb-4">
                Total aliança: <strong id="total">{{ $eleicaoCidade->qtd_membros }}</strong> membros
            </div>

            @if($eleicaoCidade->aberta || $eleicaoCidade->data_encerramento)
            <div class="mb-4">
                <label class="form-label fw-semibold">Justificativa da alteração</label>
                <textarea name="justificativa" class="form-control @error('justificativa') is-invalid @enderror"
                          rows="3" required
                          placeholder="Explique o motivo da alteração...">{{ old('justificativa') }}</textarea>
                @error('justificativa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Obrigatório. Será registrado no log da eleição.</div>
            </div>
            @endif

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>

<script>
const presencial     = document.querySelector('[name="qtd_presencial"]');
const remoto         = document.querySelector('[name="qtd_remoto"]');
const total          = document.getElementById('total');
const presencialVida = document.getElementById('qtd_presencial_vida');
const remotoVida     = document.getElementById('qtd_vida');
const totalVida      = document.getElementById('total_vida');

function atualizarTotal() {
    total.textContent = (parseInt(presencial.value) || 0) + (parseInt(remoto.value) || 0);
}

function atualizarTotalVida() {
    totalVida.textContent = (parseInt(presencialVida.value) || 0) + (parseInt(remotoVida.value) || 0);
}

presencial.addEventListener('input', atualizarTotal);
remoto.addEventListener('input', atualizarTotal);
presencialVida.addEventListener('input', atualizarTotalVida);
remotoVida.addEventListener('input', atualizarTotalVida);
</script>
@endsection
