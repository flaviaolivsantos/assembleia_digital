@extends('layouts.app')

@section('content')

{{-- ── Estilos do Painel ──────────────────────────────────────────────── --}}
<style>
:root {
    --azul:    #2C3E50;
    --ciano:   #00BCD4;
    --branco:  #FFFFFF;
    --bg-page: #F8F9FA;
    --cinza-m: #CED4DA;
    --cinza-e: #495057;
    --verde:   #28A745;
    --vermelho:#DC3545;
    --bg-total:#F0F2F5;
    --sombra:  0 4px 12px rgba(0,0,0,0.06);
    --radius:  0.75rem;
    --font-titulo: 'Montserrat', 'Open Sans', sans-serif;
    --font-corpo:  'Roboto', 'Lato', sans-serif;
}

/* Página */
.painel-wrap { background: var(--bg-page); }

/* Cabeçalho */
.painel-titulo {
    font-family: var(--font-titulo);
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--azul);
    margin-bottom: .15rem;
}
.painel-subtitulo {
    font-family: var(--font-corpo);
    font-size: .875rem;
    color: var(--cinza-e);
}
.btn-atualizar {
    font-family: var(--font-corpo);
    font-size: .875rem;
    color: var(--cinza-e);
    background: transparent;
    border: 1px solid var(--cinza-m);
    border-radius: .4rem;
    padding: .45rem .9rem;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    transition: all .2s ease-in-out;
    cursor: pointer;
}
.btn-atualizar:hover {
    background: var(--ciano);
    border-color: var(--ciano);
    color: var(--branco);
}

/* Card principal */
.painel-card {
    background: var(--branco);
    border-radius: var(--radius);
    box-shadow: var(--sombra);
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.painel-card-header {
    font-family: var(--font-titulo);
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--azul);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--cinza-m);
}

/* Seções dentro do card */
.painel-section { }
.painel-section.has-border-bottom { border-bottom: 2px solid var(--bg-total); }
.painel-section-label {
    padding: .75rem 1.25rem .5rem;
}
.tipo-badge {
    font-family: var(--font-corpo);
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--ciano);
    background: rgba(0, 188, 212, 0.08);
    border: 1px solid var(--ciano);
    padding: .25rem .75rem;
    border-radius: .35rem;
    display: inline-block;
}
/* ambos usam a mesma identidade ciano; diferenciação pelo rótulo */
.tipo-alianca { }
.tipo-vida    { }

/* Tabela */
.painel-table {
    width: 100%;
    border-collapse: collapse;
    font-family: var(--font-corpo);
}
.painel-table thead tr {
    background: var(--bg-page);
    border-bottom: 2px solid var(--cinza-m);
}
.painel-table thead th {
    font-family: var(--font-titulo);
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--azul);
    padding: .8rem 1rem;
    white-space: nowrap;
}
.painel-table tbody tr:nth-child(odd)  { background: var(--branco); }
.painel-table tbody tr:nth-child(even) { background: var(--bg-page); }
.painel-table tbody tr:hover { background: #eef6f8; }
.painel-table td {
    padding: .75rem 1rem;
    font-size: .92rem;
    color: var(--cinza-e);
    vertical-align: middle;
}

/* Colunas específicas */
.td-nome   { font-weight: 500; color: var(--azul); }
.col-num   { text-align: center; white-space: nowrap; }
.td-membros{ font-weight: 600; color: var(--azul); }
.td-votaram{ font-weight: 600; color: var(--verde); }
.td-faltam { font-weight: 600; color: var(--vermelho); }
.col-progresso { min-width: 160px; }

/* Status badges */
.status-badge {
    font-size: .75rem;
    font-weight: 600;
    padding: .25rem .65rem;
    border-radius: 2rem;
    color: var(--branco);
    white-space: nowrap;
}
.status-aberta    { background: var(--verde); }
.status-encerrada { background: var(--azul); }
.status-aguardando{ background: var(--cinza-m); color: var(--cinza-e); }

/* Barra de progresso */
.progresso-wrap {
    display: flex;
    align-items: center;
    gap: .5rem;
}
.progresso-bg {
    flex: 1;
    height: 7px;
    background: #E9ECEF;
    border-radius: 99px;
    overflow: hidden;
}
.progresso-fill {
    height: 100%;
    background: var(--ciano);
    border-radius: 99px;
    transition: width .4s ease;
}
.progresso-pct {
    font-size: .8rem;
    color: var(--cinza-e);
    min-width: 2.4rem;
    text-align: right;
}

/* Linha de total */
.total-row {
    background: var(--bg-total) !important;
    border-top: 2px solid var(--cinza-m);
}
.td-total-label {
    font-family: var(--font-titulo);
    font-weight: 700;
    color: var(--azul) !important;
}
.td-total-num {
    font-family: var(--font-titulo);
    font-weight: 700;
    color: var(--azul) !important;
    text-align: center;
}
</style>

{{-- ── Cabeçalho ──────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 painel-wrap">
    <div>
        <h1 class="painel-titulo">Painel de Acompanhamento</h1>
        <p class="painel-subtitulo mb-0">Atualiza automaticamente a cada 30 segundos.</p>
    </div>
    <button class="btn-atualizar" onclick="atualizar()">
        <i class="bi bi-arrow-clockwise"></i> Atualizar agora
    </button>
</div>

{{-- ── Conteúdo (renderizado pelo Blade na primeira carga) ────────────── --}}
<div id="painel">
    @include('acompanhamento._dados', ['eleicoes' => $eleicoes])
</div>

@push('scripts')
<script>
// ── Helpers ────────────────────────────────────────────────────────────
const statusBadge = {
    aberta:     '<span class="status-badge status-aberta">Aberta</span>',
    encerrada:  '<span class="status-badge status-encerrada">Encerrada</span>',
    aguardando: '<span class="status-badge status-aguardando">Aguardando</span>',
};

function progressoHtml(pct) {
    return `<div class="progresso-wrap">
        <div class="progresso-bg"><div class="progresso-fill" style="width:${pct}%"></div></div>
        <span class="progresso-pct">${pct}%</span>
    </div>`;
}

/**
 * Renderiza uma tabela (aliança ou vida) usando os campos corretos do objeto missão.
 * @param {Array}  missoes  - lista de missões
 * @param {Object} k        - mapa de chaves: { status, membros, votaram, faltam, pct }
 * @param {string} badgeHtml - HTML do badge de tipo (pode ser '')
 */
function renderTabela(missoes, k, badgeHtml) {
    const linhas = missoes.map(m => `
        <tr>
            <td class="td-nome">${m.nome}</td>
            <td>${statusBadge[m[k.status]] ?? ''}</td>
            <td class="col-num td-membros">${m[k.membros]}</td>
            <td class="col-num td-votaram">${m[k.votaram]}</td>
            <td class="col-num td-faltam">${m[k.faltam]}</td>
            <td class="col-progresso">${progressoHtml(m[k.pct])}</td>
        </tr>`).join('');

    const totalM = missoes.reduce((s, m) => s + m[k.membros], 0);
    const totalV = missoes.reduce((s, m) => s + m[k.votaram], 0);
    const totalF = missoes.reduce((s, m) => s + m[k.faltam], 0);
    const totalP = totalM > 0 ? Math.round((totalV / totalM) * 100) : 0;

    const tfoot = missoes.length > 1 ? `
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="td-total-label">Total</td>
                <td class="col-num td-total-num">${totalM}</td>
                <td class="col-num td-total-num">${totalV}</td>
                <td class="col-num td-total-num">${totalF}</td>
                <td class="col-progresso">${progressoHtml(totalP)}</td>
            </tr>
        </tfoot>` : '';

    const labelHtml = badgeHtml
        ? `<div class="painel-section-label">${badgeHtml}</div>`
        : '';

    return `${labelHtml}
        <div class="table-responsive">
            <table class="painel-table">
                <thead>
                    <tr>
                        <th>Missão</th>
                        <th>Status</th>
                        <th class="col-num">Membros</th>
                        <th class="col-num">Votaram</th>
                        <th class="col-num">Faltam</th>
                        <th class="col-progresso">Participação</th>
                    </tr>
                </thead>
                <tbody>${linhas}</tbody>
                ${tfoot}
            </table>
        </div>`;
}

function renderEleicao(e) {
    let sections = '';

    if (e.tem_alianca) {
        const badge = e.tem_vida
            ? '<span class="tipo-badge tipo-alianca">Realidade de Aliança</span>'
            : '';
        const borderClass = e.tem_vida ? ' has-border-bottom' : '';
        sections += `<div class="painel-section${borderClass}">
            ${renderTabela(e.missoes,
                { status:'status', membros:'membros', votaram:'votaram', faltam:'faltam', pct:'pct' },
                badge)}
        </div>`;
    }

    if (e.tem_vida) {
        sections += `<div class="painel-section">
            ${renderTabela(e.missoes,
                { status:'vida_status', membros:'vida_membros', votaram:'vida_votaram', faltam:'vida_faltam', pct:'vida_pct' },
                '<span class="tipo-badge tipo-vida">Realidade de Vida</span>')}
        </div>`;
    }

    return `<div class="painel-card">
        <div class="painel-card-header">${e.titulo}</div>
        ${sections}
    </div>`;
}

// ── Auto-refresh ───────────────────────────────────────────────────────
function atualizar() {
    fetch('{{ route('acompanhamento.dados') }}', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('painel').innerHTML = data.length === 0
            ? '<div class="alert alert-info">Nenhuma eleição aberta no momento.</div>'
            : data.map(renderEleicao).join('');
    })
    .catch(() => {});
}

setInterval(atualizar, 30000);
</script>
@endpush
@endsection
