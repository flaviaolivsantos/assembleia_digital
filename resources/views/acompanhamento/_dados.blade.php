@forelse($eleicoes as $eleicao)
    @php
        $totalMembros = $eleicao['missoes']->sum('membros');
        $totalVotaram = $eleicao['missoes']->sum('votaram');
        $totalFaltam  = $eleicao['missoes']->sum('faltam');
        $totalPct     = $totalMembros > 0 ? round(($totalVotaram / $totalMembros) * 100) : 0;
    @endphp
    <div class="card mb-4">
        <div class="card-header"><strong>{{ $eleicao['titulo'] }}</strong></div>
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
                <tbody>
                    @foreach($eleicao['missoes'] as $m)
                        @php
                            $corBarra = $m['pct'] >= 100 ? 'bg-success' : ($m['pct'] >= 50 ? 'bg-primary' : 'bg-warning');
                        @endphp
                        <tr>
                            <td><strong>{{ $m['nome'] }}</strong></td>
                            <td>
                                @if($m['status'] === 'aberta')
                                    <span class="badge text-bg-success">Aberta</span>
                                @elseif($m['status'] === 'encerrada')
                                    <span class="badge text-bg-dark">Encerrada</span>
                                @else
                                    <span class="badge text-bg-secondary">Aguardando</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $m['membros'] }}</td>
                            <td class="text-center text-success fw-semibold">{{ $m['votaram'] }}</td>
                            <td class="text-center text-danger fw-semibold">{{ $m['faltam'] }}</td>
                            <td style="min-width:140px">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar {{ $corBarra }}" style="width:{{ $m['pct'] }}%"></div>
                                    </div>
                                    <span class="small text-muted">{{ $m['pct'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @if(count($eleicao['missoes']) > 1)
                <tfoot class="table-light">
                    @php $corTotal = $totalPct >= 100 ? 'bg-success' : ($totalPct >= 50 ? 'bg-primary' : 'bg-warning'); @endphp
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td class="text-center fw-semibold">{{ $totalMembros }}</td>
                        <td class="text-center text-success fw-semibold">{{ $totalVotaram }}</td>
                        <td class="text-center text-danger fw-semibold">{{ $totalFaltam }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px;">
                                    <div class="progress-bar {{ $corTotal }}" style="width:{{ $totalPct }}%"></div>
                                </div>
                                <span class="small text-muted">{{ $totalPct }}%</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
@empty
    <div class="alert alert-info">Nenhuma eleição aberta no momento.</div>
@endforelse
