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

function renderMissao(m) {
    const statusMap = {
        aberta:     '<span class="badge text-bg-success">Aberta</span>',
        encerrada:  '<span class="badge text-bg-dark">Encerrada</span>',
        aguardando: '<span class="badge text-bg-secondary">Aguardando</span>',
    };
    const corBarra = m.pct >= 100 ? 'bg-success' : (m.pct >= 50 ? 'bg-primary' : 'bg-warning');
    return `
        <tr>
            <td><strong>${m.nome}</strong></td>
            <td>${statusMap[m.status] ?? ''}</td>
            <td class="text-center">${m.membros}</td>
            <td class="text-center text-success fw-semibold">${m.votaram}</td>
            <td class="text-center text-danger fw-semibold">${m.faltam}</td>
            <td style="min-width:140px">
                <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:8px;">
                        <div class="progress-bar ${corBarra}" style="width:${m.pct}%"></div>
                    </div>
                    <span class="small text-muted">${m.pct}%</span>
                </div>
            </td>
        </tr>`;
}

function renderEleicao(e) {
    const linhas = e.missoes.map(renderMissao).join('');
    const totalMembros = e.missoes.reduce((s, m) => s + m.membros, 0);
    const totalVotaram = e.missoes.reduce((s, m) => s + m.votaram, 0);
    const totalFaltam  = e.missoes.reduce((s, m) => s + m.faltam, 0);
    const totalPct     = totalMembros > 0 ? Math.round((totalVotaram / totalMembros) * 100) : 0;
    const corTotal     = totalPct >= 100 ? 'bg-success' : (totalPct >= 50 ? 'bg-primary' : 'bg-warning');

    return `
        <div class="card mb-4">
            <div class="card-header"><strong>${e.titulo}</strong></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Missão</th>
                            <th>Status</th>
                            <th class="text-center">Membros</th>
                            <th class="text-center">Votaram</th>
                            <th class="text-center">Faltam</th>
                            <th>Participação</th>
                        </tr>
                    </thead>
                    <tbody>${linhas}</tbody>
                    ${e.missoes.length > 1 ? `
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td class="text-center fw-semibold">${totalMembros}</td>
                            <td class="text-center text-success fw-semibold">${totalVotaram}</td>
                            <td class="text-center text-danger fw-semibold">${totalFaltam}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar ${corTotal}" style="width:${totalPct}%"></div>
                                    </div>
                                    <span class="small text-muted">${totalPct}%</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>` : ''}
                </table>
            </div>
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
