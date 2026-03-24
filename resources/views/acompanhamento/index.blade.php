@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Painel de Acompanhamento</h2>
        <p class="text-muted mb-0 small">Atualiza automaticamente a cada 30 segundos.</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm" onclick="atualizar()">
        <i class="bi bi-arrow-clockwise"></i> Atualizar agora
    </button>
</div>

<div id="painel">
    @include('acompanhamento._dados', ['eleicoes' => $eleicoes])
</div>

@push('scripts')
<script>
let intervalo = null;

const statusBadge = {
    aberta:     '<span class="badge text-bg-success">Aberta</span>',
    encerrada:  '<span class="badge text-bg-dark">Encerrada</span>',
    aguardando: '<span class="badge text-bg-secondary">Aguardando</span>',
};

function barraProgresso(pct) {
    const cor = pct >= 100 ? 'bg-success' : (pct >= 50 ? 'bg-primary' : 'bg-warning');
    return `<div class="d-flex align-items-center gap-2">
        <div class="progress flex-grow-1" style="height:8px;">
            <div class="progress-bar ${cor}" style="width:${pct}%"></div>
        </div>
        <span class="small text-muted">${pct}%</span>
    </div>`;
}

function renderTabela(missoes, campos, badge) {
    const { nome: campoNome, status, membros, votaram, faltam, pct } = campos;
    const linhas = missoes.map(m => `
        <tr>
            <td><strong>${m.nome}</strong></td>
            <td>${statusBadge[m[status]] ?? ''}</td>
            <td class="text-center">${m[membros]}</td>
            <td class="text-center text-success fw-semibold">${m[votaram]}</td>
            <td class="text-center text-danger fw-semibold">${m[faltam]}</td>
            <td style="min-width:140px">${barraProgresso(m[pct])}</td>
        </tr>`).join('');

    const totalM = missoes.reduce((s, m) => s + m[membros], 0);
    const totalV = missoes.reduce((s, m) => s + m[votaram], 0);
    const totalF = missoes.reduce((s, m) => s + m[faltam], 0);
    const totalP = totalM > 0 ? Math.round((totalV / totalM) * 100) : 0;
    const tfoot  = missoes.length > 1 ? `
        <tfoot class="table-light">
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td class="text-center fw-semibold">${totalM}</td>
                <td class="text-center text-success fw-semibold">${totalV}</td>
                <td class="text-center text-danger fw-semibold">${totalF}</td>
                <td style="min-width:140px">${barraProgresso(totalP)}</td>
            </tr>
        </tfoot>` : '';

    return `
        <div class="px-3 pt-2 pb-1">${badge}</div>
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Missão</th><th>Status</th>
                    <th class="text-center">Membros</th>
                    <th class="text-center">Votaram</th>
                    <th class="text-center">Faltam</th>
                    <th>Participação</th>
                </tr>
            </thead>
            <tbody>${linhas}</tbody>
            ${tfoot}
        </table>`;
}

function renderEleicao(e) {
    let body = '';

    if (e.tem_alianca) {
        const badge = e.tem_vida
            ? '<span class="badge bg-secondary">Realidade de Aliança</span>'
            : '';
        body += `<div class="card-body p-0">
            ${renderTabela(e.missoes,
                { status: 'status', membros: 'membros', votaram: 'votaram', faltam: 'faltam', pct: 'pct' },
                badge)}
        </div>`;
    }

    if (e.tem_vida) {
        const border = e.tem_alianca ? ' border-top' : '';
        body += `<div class="card-body p-0${border}">
            ${renderTabela(e.missoes,
                { status: 'vida_status', membros: 'vida_membros', votaram: 'vida_votaram', faltam: 'vida_faltam', pct: 'vida_pct' },
                '<span class="badge bg-primary">Realidade de Vida</span>')}
        </div>`;
    }

    return `<div class="card mb-4">
        <div class="card-header"><strong>${e.titulo}</strong></div>
        ${body}
    </div>`;
}

function atualizar() {
    fetch('{{ route('acompanhamento.dados') }}', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.length === 0) {
            document.getElementById('painel').innerHTML =
                '<div class="alert alert-info">Nenhuma eleição aberta no momento.</div>';
        } else {
            document.getElementById('painel').innerHTML = data.map(renderEleicao).join('');
        }
    })
    .catch(() => {});
}

intervalo = setInterval(atualizar, 30000);
</script>
@endpush
@endsection
