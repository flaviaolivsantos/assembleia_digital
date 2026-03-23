@forelse($eleicoes as $eleicao)
    @php
        $temAlianca = $eleicao['tem_alianca'];
        $temVida    = $eleicao['tem_vida'];
    @endphp
    <div class="card mb-4">
        <div class="card-header"><strong>{{ $eleicao['titulo'] }}</strong></div>

        {{-- Aliança section --}}
        @if($temAlianca)
        <div class="card-body p-0">
            @if($temVida)
            <div class="px-3 pt-2 pb-1">
                <span class="badge bg-secondary">Realidade de Aliança</span>
            </div>
            @endif
            @php
                $totalMembros = $eleicao['missoes']->sum('membros');
                $totalVotaram = $eleicao['missoes']->sum('votaram');
                $totalFaltam  = $eleicao['missoes']->sum('faltam');
                $totalPct     = $totalMembros > 0 ? round(($totalVotaram / $totalMembros) * 100) : 0;
            @endphp
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
                        @php $corBarra = $m['pct'] >= 100 ? 'bg-success' : ($m['pct'] >= 50 ? 'bg-primary' : 'bg-warning'); @endphp
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
        @endif

        {{-- Vida section --}}
        @if($temVida)
        <div class="card-body p-0 {{ $temAlianca ? 'border-top' : '' }}">
            <div class="px-3 pt-2 pb-1">
                <span class="badge bg-primary">Realidade de Vida</span>
            </div>
            @php
                $totalVidaMembros = $eleicao['missoes']->sum('vida_membros');
                $totalVidaVotaram = $eleicao['missoes']->sum('vida_votaram');
                $totalVidaFaltam  = $eleicao['missoes']->sum('vida_faltam');
                $totalVidaPct     = $totalVidaMembros > 0 ? round(($totalVidaVotaram / $totalVidaMembros) * 100) : 0;
            @endphp
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
                        @php $corBarra = $m['vida_pct'] >= 100 ? 'bg-success' : ($m['vida_pct'] >= 50 ? 'bg-primary' : 'bg-warning'); @endphp
                        <tr>
                            <td><strong>{{ $m['nome'] }}</strong></td>
                            <td>
                                @if($m['vida_status'] === 'aberta')
                                    <span class="badge text-bg-success">Aberta</span>
                                @elseif($m['vida_status'] === 'encerrada')
                                    <span class="badge text-bg-dark">Encerrada</span>
                                @else
                                    <span class="badge text-bg-secondary">Aguardando</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $m['vida_membros'] }}</td>
                            <td class="text-center text-success fw-semibold">{{ $m['vida_votaram'] }}</td>
                            <td class="text-center text-danger fw-semibold">{{ $m['vida_faltam'] }}</td>
                            <td style="min-width:140px">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar {{ $corBarra }}" style="width:{{ $m['vida_pct'] }}%"></div>
                                    </div>
                                    <span class="small text-muted">{{ $m['vida_pct'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @if(count($eleicao['missoes']) > 1)
                <tfoot class="table-light">
                    @php $corTotal = $totalVidaPct >= 100 ? 'bg-success' : ($totalVidaPct >= 50 ? 'bg-primary' : 'bg-warning'); @endphp
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td class="text-center fw-semibold">{{ $totalVidaMembros }}</td>
                        <td class="text-center text-success fw-semibold">{{ $totalVidaVotaram }}</td>
                        <td class="text-center text-danger fw-semibold">{{ $totalVidaFaltam }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px;">
                                    <div class="progress-bar {{ $corTotal }}" style="width:{{ $totalVidaPct }}%"></div>
                                </div>
                                <span class="small text-muted">{{ $totalVidaPct }}%</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @endif
    </div>
@empty
    <div class="alert alert-info">Nenhuma eleição aberta no momento.</div>
@endforelse
