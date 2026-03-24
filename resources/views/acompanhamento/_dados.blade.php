@forelse($eleicoes as $eleicao)
@php
    $temAlianca = $eleicao['tem_alianca'];
    $temVida    = $eleicao['tem_vida'];
@endphp
<div class="painel-card">
    <div class="painel-card-header">{{ $eleicao['titulo'] }}</div>

    {{-- ── Realidade de Aliança ─────────────────────────────────── --}}
    @if($temAlianca)
    @php
        $totalM   = $eleicao['missoes']->sum('membros');
        $totalV   = $eleicao['missoes']->sum('votaram');
        $totalF   = $eleicao['missoes']->sum('faltam');
        $totalPct = $totalM > 0 ? round(($totalV / $totalM) * 100) : 0;
    @endphp
    <div class="painel-section {{ $temAlianca && $temVida ? 'has-border-bottom' : '' }}">
        @if($temVida)
        <div class="painel-section-label">
            <span class="tipo-badge tipo-alianca">Realidade de Aliança</span>
        </div>
        @endif
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
                <tbody>
                    @foreach($eleicao['missoes'] as $m)
                    <tr>
                        <td class="td-nome">{{ $m['nome'] }}</td>
                        <td>
                            @if($m['status'] === 'aberta')
                                <span class="status-badge status-aberta">Aberta</span>
                            @elseif($m['status'] === 'encerrada')
                                <span class="status-badge status-encerrada">Encerrada</span>
                            @else
                                <span class="status-badge status-aguardando">Aguardando</span>
                            @endif
                        </td>
                        <td class="col-num td-membros">{{ $m['membros'] }}</td>
                        <td class="col-num td-votaram">{{ $m['votaram'] }}</td>
                        <td class="col-num td-faltam">{{ $m['faltam'] }}</td>
                        <td class="col-progresso">
                            <div class="progresso-wrap">
                                <div class="progresso-bg">
                                    <div class="progresso-fill" style="width:{{ $m['pct'] }}%"></div>
                                </div>
                                <span class="progresso-pct">{{ $m['pct'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if(count($eleicao['missoes']) > 1)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" class="td-total-label">Total</td>
                        <td class="col-num td-total-num">{{ $totalM }}</td>
                        <td class="col-num td-total-num">{{ $totalV }}</td>
                        <td class="col-num td-total-num">{{ $totalF }}</td>
                        <td class="col-progresso">
                            <div class="progresso-wrap">
                                <div class="progresso-bg">
                                    <div class="progresso-fill" style="width:{{ $totalPct }}%"></div>
                                </div>
                                <span class="progresso-pct">{{ $totalPct }}%</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- ── Realidade de Vida ────────────────────────────────────── --}}
    @if($temVida)
    @php
        $totalVM  = $eleicao['missoes']->sum('vida_membros');
        $totalVV  = $eleicao['missoes']->sum('vida_votaram');
        $totalVF  = $eleicao['missoes']->sum('vida_faltam');
        $totalVPct = $totalVM > 0 ? round(($totalVV / $totalVM) * 100) : 0;
    @endphp
    <div class="painel-section">
        <div class="painel-section-label">
            <span class="tipo-badge tipo-vida">Realidade de Vida</span>
        </div>
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
                <tbody>
                    @foreach($eleicao['missoes'] as $m)
                    <tr>
                        <td class="td-nome">{{ $m['nome'] }}</td>
                        <td>
                            @if($m['vida_status'] === 'aberta')
                                <span class="status-badge status-aberta">Aberta</span>
                            @elseif($m['vida_status'] === 'encerrada')
                                <span class="status-badge status-encerrada">Encerrada</span>
                            @else
                                <span class="status-badge status-aguardando">Aguardando</span>
                            @endif
                        </td>
                        <td class="col-num td-membros">{{ $m['vida_membros'] }}</td>
                        <td class="col-num td-votaram">{{ $m['vida_votaram'] }}</td>
                        <td class="col-num td-faltam">{{ $m['vida_faltam'] }}</td>
                        <td class="col-progresso">
                            <div class="progresso-wrap">
                                <div class="progresso-bg">
                                    <div class="progresso-fill" style="width:{{ $m['vida_pct'] }}%"></div>
                                </div>
                                <span class="progresso-pct">{{ $m['vida_pct'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if(count($eleicao['missoes']) > 1)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" class="td-total-label">Total</td>
                        <td class="col-num td-total-num">{{ $totalVM }}</td>
                        <td class="col-num td-total-num">{{ $totalVV }}</td>
                        <td class="col-num td-total-num">{{ $totalVF }}</td>
                        <td class="col-progresso">
                            <div class="progresso-wrap">
                                <div class="progresso-bg">
                                    <div class="progresso-fill" style="width:{{ $totalVPct }}%"></div>
                                </div>
                                <span class="progresso-pct">{{ $totalVPct }}%</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>
@empty
<div class="alert alert-info">Nenhuma eleição aberta no momento.</div>
@endforelse
