<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votação — Assembleia Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .candidato-card { cursor: pointer; transition: border-color 0.15s, background 0.15s, box-shadow 0.15s; }
        .candidato-card.selecionado {
            border-color: #00BCD4 !important;
            border-width: 3px !important;
            background: #f0fdff;
            box-shadow: 0 0 0 2px rgba(0,188,212,.25);
        }
        .candidato-card input[type=checkbox] { display: none; }
        .candidato-foto { width: 100%; height: 140px; object-fit: cover; border-radius: 6px 6px 0 0; }

        /* ── Sticky bar ── */
        body { padding-bottom: 80px; }

        #sticky-bar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            z-index: 1000;
            background: #fff;
            border-top: 2px solid #e5e7eb;
            padding: .65rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 -4px 16px rgba(0,0,0,.08);
            transition: border-color .2s;
        }
        #sticky-bar.completo { border-top-color: #00BCD4; }

        .bar-progress {
            display: flex;
            flex-direction: column;
            gap: .15rem;
            flex: 1;
        }
        .bar-progress-label {
            font-family: 'Inter', sans-serif;
            font-size: .82rem;
            color: #6b7280;
            transition: color .2s;
        }
        #sticky-bar.completo .bar-progress-label { color: #00899e; font-weight: 600; }

        .bar-progress-track {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            max-width: 280px;
        }
        .bar-progress-fill {
            height: 100%;
            background: #00BCD4;
            border-radius: 2px;
            transition: width .2s;
        }

        .bar-perguntas {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            flex: 1;
        }
        .bar-pergunta-chip {
            font-size: .72rem;
            font-family: 'Inter', sans-serif;
            padding: .2em .65em;
            border-radius: 2rem;
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            transition: all .2s;
            white-space: nowrap;
        }
        .bar-pergunta-chip.chip-ok {
            background: rgba(0,188,212,.12);
            color: #00899e;
            border-color: rgba(0,188,212,.3);
        }

        .btn-confirmar {
            padding: .55rem 1.5rem;
            background: #00BCD4;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: filter .15s, opacity .15s;
            flex-shrink: 0;
        }
        .btn-confirmar:disabled { opacity: .45; cursor: not-allowed; filter: none; }
        .btn-confirmar:not(:disabled):hover { filter: brightness(.9); }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width: 860px;">
    <div class="text-center mb-5">
        <h3>{{ $eleicaoCidade->eleicao->titulo }}</h3>

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
                    <strong>{{ $pergunta->pergunta }}</strong>
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

    </form>
</div>

{{-- ── Sticky bottom bar ─────────────────────────────────── --}}
<div id="sticky-bar">
    <div class="bar-perguntas" id="bar-chips"></div>
    <div class="bar-progress">
        <span class="bar-progress-label" id="bar-label">Selecione os candidatos</span>
        <div class="bar-progress-track">
            <div class="bar-progress-fill" id="bar-fill" style="width:0%"></div>
        </div>
    </div>
    <button type="submit" form="form-votacao" class="btn-confirmar" id="btn-votar" disabled>
        Confirmar Voto
    </button>
</div>

<script>
const PERGUNTAS = {!! $perguntas->map(fn($p) => ['id' => $p->id, 'label' => $p->pergunta, 'qtd' => $p->qtd_respostas])->values()->toJson() !!};

// Cria chips na barra
const chipsContainer = document.getElementById('bar-chips');
PERGUNTAS.forEach(function(p) {
    const chip = document.createElement('span');
    chip.className = 'bar-pergunta-chip';
    chip.id = 'chip-' + p.id;
    chip.textContent = '0/' + p.qtd;
    chipsContainer.appendChild(chip);
});

document.querySelectorAll('.candidato-card').forEach(function(card) {
    card.addEventListener('click', function(e) {
        e.preventDefault();

        const checkbox   = this.querySelector('.opcao-check');
        const perguntaId = checkbox.dataset.pergunta;
        const max        = parseInt(checkbox.dataset.max);

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

        // Atualiza contador no card-header
        const contadorEl = document.getElementById('contador-' + perguntaId);
        if (contadorEl) contadorEl.textContent = total + ' / ' + max;

        atualizarBarraInferior();
    });
});

function atualizarBarraInferior() {
    let totalSel = 0, totalMax = 0, completo = true;

    PERGUNTAS.forEach(function(p) {
        const sel = document.querySelectorAll('.opcao-check[data-pergunta="' + p.id + '"]:checked').length;
        const qtd = parseInt(p.qtd);
        totalSel += sel;
        totalMax += qtd;
        if (sel !== qtd) completo = false;

        // Chip individual
        const chip = document.getElementById('chip-' + p.id);
        if (chip) {
            chip.textContent = sel + '/' + qtd;
            chip.classList.toggle('chip-ok', sel === qtd);
        }
    });

    // Barra de progresso
    const pct = totalMax > 0 ? Math.round((totalSel / totalMax) * 100) : 0;
    document.getElementById('bar-fill').style.width = pct + '%';

    // Label
    const label = document.getElementById('bar-label');
    if (completo) {
        label.textContent = 'Tudo pronto! Confirme seu voto.';
    } else {
        const faltam = totalMax - totalSel;
        label.textContent = totalSel + ' de ' + totalMax + ' selecionados — faltam ' + faltam;
    }

    // Estado da barra
    const bar = document.getElementById('sticky-bar');
    bar.classList.toggle('completo', completo);

    // Botão
    document.getElementById('btn-votar').disabled = !completo;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
