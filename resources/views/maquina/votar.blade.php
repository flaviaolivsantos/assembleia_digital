<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votacao — Assembleia Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .candidato-card { cursor: pointer; transition: border 0.15s, background 0.15s; }
        .candidato-card.selecionado { border-color: #0d6efd !important; background: #e8f0fe; }
        .candidato-card input[type=checkbox] { display: none; }
        .candidato-foto { width: 100%; height: 140px; object-fit: cover; border-radius: 6px 6px 0 0; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4" style="max-width: 860px;">
    <div class="text-center mb-5">
        <h3>{{ $eleicaoCidade->eleicao->titulo }}</h3>
        <p class="text-muted">Leia com atencao cada pergunta e selecione a quantidade indicada de candidatos.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $erro)
                <div>{{ $erro }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route($confirmRoute ?? 'maquina.confirmarVoto') }}" id="form-votacao">
        @csrf

        @foreach($perguntas as $index => $pergunta)
            <div class="card mb-5">
                <div class="card-header">
                    <strong>{{ $index + 1 }}. {{ $pergunta->pergunta }}</strong>
                    <span class="badge text-bg-primary ms-2" id="contador-{{ $pergunta->id }}">
                        0 / {{ $pergunta->qtd_respostas }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($pergunta->opcoesDaCidade as $opcao)
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="candidato-card card h-100 border-2 w-100 p-0"
                                    id="label-{{ $pergunta->id }}-{{ $opcao->id }}">
                                    <input type="checkbox"
                                        name="respostas[{{ $pergunta->id }}][]"
                                        value="{{ $opcao->id }}"
                                        data-pergunta="{{ $pergunta->id }}"
                                        data-max="{{ $pergunta->qtd_respostas }}"
                                        class="opcao-check">
                                    <img src="{{ $opcao->foto_url }}" alt="{{ $opcao->nome }}" class="candidato-foto">
                                    <div class="card-body p-2 text-center">
                                        <span class="fw-semibold small">{{ $opcao->nome }}</span>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">Nenhum candidato cadastrado para esta missão.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg px-5" id="btn-votar" disabled>
                Confirmar Voto
            </button>
            <p class="text-muted small mt-2">Revise suas escolhas antes de confirmar. Esta acao e irreversivel.</p>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.candidato-card').forEach(function(card) {
    card.addEventListener('click', function(e) {
        e.preventDefault();

        const checkbox    = this.querySelector('.opcao-check');
        const perguntaId  = checkbox.dataset.pergunta;
        const max         = parseInt(checkbox.dataset.max);

        if (!checkbox.checked) {
            const jaChecados = document.querySelectorAll('.opcao-check[data-pergunta="' + perguntaId + '"]:checked').length;
            if (jaChecados >= max) return;
            checkbox.checked = true;
            this.classList.add('selecionado');
        } else {
            checkbox.checked = false;
            this.classList.remove('selecionado');
        }

        const total = document.querySelectorAll('.opcao-check[data-pergunta="' + perguntaId + '"]:checked').length;

        // Bloqueia/desbloqueia cards restantes
        document.querySelectorAll('.candidato-card').forEach(function(c) {
            const cb = c.querySelector('.opcao-check');
            if (cb.dataset.pergunta !== perguntaId) return;
            if (!cb.checked) {
                c.style.opacity = (total >= max) ? '0.45' : '1';
                cb.disabled = (total >= max);
            } else {
                c.style.opacity = '1';
            }
        });

        // Atualiza contador
        document.getElementById('contador-' + perguntaId).textContent = total + ' / ' + max;

        // Habilita botão se todas as perguntas estiverem completas
        verificarCompleto();
    });
});

function verificarCompleto() {
    const perguntas = {!! $perguntas->pluck('qtd_respostas', 'id')->toJson() !!};
    let completo = true;
    for (const [id, qtd] of Object.entries(perguntas)) {
        const sel = document.querySelectorAll('.opcao-check[data-pergunta="' + id + '"]:checked').length;
        if (sel !== parseInt(qtd)) { completo = false; break; }
    }
    document.getElementById('btn-votar').disabled = !completo;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
