@extends('layouts.app')

@section('content')

<style>
    .rel-header-title { font-family: 'Montserrat', sans-serif; font-weight: 700; color: #2C3E50; }
    .rel-filter-bar { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .rel-filter-btn {
        font-family: 'Montserrat', sans-serif; font-size: .75rem; font-weight: 600;
        letter-spacing: .3px; text-transform: uppercase;
        padding: .4rem .9rem; border-radius: 2rem;
        border: 1.5px solid #CED4DA; background: #fff; color: #495057;
        text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        transition: all .15s;
    }
    .rel-filter-btn:hover { border-color: #00BCD4; color: #00BCD4; }
    .rel-filter-btn.active { background: #2C3E50; border-color: #2C3E50; color: #fff; }

    .rel-table { border-collapse: collapse; width: 100%; font-size: .88rem; }
    .rel-table thead tr { background-color: #2C3E50; }
    .rel-table thead th { color: #fff; font-family: 'Montserrat', sans-serif; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; padding: .65rem .85rem; border: none; }
    .rel-table tbody tr:nth-child(odd)  { background: #fff; }
    .rel-table tbody tr:nth-child(even) { background: #F8F9FA; }
    .rel-table tbody tr:hover { background: rgba(0,188,212,.05) !important; }
    .rel-table tbody td { padding: .55rem .85rem; border: 1px solid #dee2e6; color: #495057; vertical-align: middle; }

    .badge-presencial { background: rgba(73,80,87,.12); color: #495057; font-size: .72rem; font-weight: 600; padding: .2em .6em; border-radius: 4px; font-family: 'Montserrat', sans-serif; }
    .badge-remoto     { background: rgba(0,188,212,.12); color: #00899e; font-size: .72rem; font-weight: 600; padding: .2em .6em; border-radius: 4px; font-family: 'Montserrat', sans-serif; }
    .badge-alianca    { background: rgba(73,80,87,.12); color: #495057; font-size: .68rem; padding: .15em .5em; border-radius: 3px; font-family: 'Montserrat', sans-serif; font-weight: 600; }
    .badge-vida       { background: rgba(0,188,212,.12); color: #00899e; font-size: .68rem; padding: .15em .5em; border-radius: 3px; font-family: 'Montserrat', sans-serif; font-weight: 600; }
    .token-mono { font-family: 'Courier New', monospace; font-size: .78rem; color: #6c757d; letter-spacing: .5px; }

    .rel-card { border: none !important; border-radius: .75rem !important; box-shadow: 0 4px 12px rgba(0,0,0,.08) !important; }
    .rel-summary { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .rel-summary-item { font-size: .85rem; color: #6c757d; }
    .rel-summary-item strong { color: #2C3E50; font-family: 'Montserrat', sans-serif; }
</style>

@php
    $filtro  = request('filtro', 'todos');
    $votosFiltrados = match($filtro) {
        'alianca' => $votos->filter(fn($v) => $v->pergunta?->escopo === 'alianca'),
        'vida'    => $votos->filter(fn($v) => $v->pergunta?->escopo === 'vida'),
        'presencial' => $votos->filter(fn($v) => $v->origem === 'presencial'),
        'remoto'     => $votos->filter(fn($v) => $v->origem === 'remoto'),
        default   => $votos,
    };
    $totalAlianca   = $votos->filter(fn($v) => $v->pergunta?->escopo === 'alianca')->count();
    $totalVida      = $votos->filter(fn($v) => $v->pergunta?->escopo === 'vida')->count();
    $totalPresencial= $votos->where('origem', 'presencial')->count();
    $totalRemoto    = $votos->where('origem', 'remoto')->count();
@endphp

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h2 class="rel-header-title mb-1">Relatório de Votos — {{ $eleicao->titulo }}</h2>
        <span style="font-size:.9rem;color:#6c757d;">
            <i class="bi bi-calendar3 me-1"></i>{{ $eleicao->data_eleicao->format('d/m/Y') }}
        </span>
    </div>
    <a href="{{ route('responsavel.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

{{-- Resumo --}}
<div class="rel-summary">
    <div class="rel-summary-item">Total de registros: <strong>{{ $votos->count() }}</strong></div>
    <div class="rel-summary-item">Aliança: <strong>{{ $totalAlianca }}</strong></div>
    <div class="rel-summary-item">Vida: <strong>{{ $totalVida }}</strong></div>
    <div class="rel-summary-item">Presencial: <strong>{{ $totalPresencial }}</strong></div>
    <div class="rel-summary-item">Remoto: <strong>{{ $totalRemoto }}</strong></div>
</div>

{{-- Filtros --}}
<div class="rel-filter-bar">
    @foreach([
        'todos'      => ['icon' => 'bi-list-ul',        'label' => 'Todos'],
        'alianca'    => ['icon' => 'bi-building',        'label' => 'Aliança'],
        'vida'       => ['icon' => 'bi-globe2',          'label' => 'Vida'],
        'presencial' => ['icon' => 'bi-display',         'label' => 'Presencial'],
        'remoto'     => ['icon' => 'bi-wifi',            'label' => 'Remoto'],
    ] as $key => $opt)
    <a href="{{ route('responsavel.relatorios', $eleicaoCidade) }}?filtro={{ $key }}"
       class="rel-filter-btn {{ $filtro === $key ? 'active' : '' }}">
        <i class="{{ $opt['icon'] }}"></i>{{ $opt['label'] }}
    </a>
    @endforeach
</div>

{{-- Tabela --}}
<div class="card rel-card">
    <div class="card-body p-0">
        <table class="rel-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Horário</th>
                    <th>Realidade</th>
                    <th>Origem</th>
                    <th>Máquina</th>
                    <th>Pergunta</th>
                    <th>Candidato</th>
                    <th>Token</th>
                </tr>
            </thead>
            <tbody>
                @forelse($votosFiltrados as $i => $voto)
                <tr>
                    <td style="color:#6c757d;font-size:.8rem;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;">
                        {{ $voto->created_at ? \Carbon\Carbon::parse($voto->created_at)->format('d/m/Y H:i:s') : '—' }}
                    </td>
                    <td>
                        @if($voto->pergunta?->escopo === 'vida')
                            <span class="badge-vida">Vida</span>
                        @else
                            <span class="badge-alianca">Aliança</span>
                        @endif
                    </td>
                    <td>
                        @if($voto->origem === 'presencial')
                            <span class="badge-presencial"><i class="bi bi-display me-1"></i>Presencial</span>
                        @else
                            <span class="badge-remoto"><i class="bi bi-wifi me-1"></i>Remoto</span>
                        @endif
                    </td>
                    <td>{{ $voto->maquina?->name ?? '—' }}</td>
                    <td style="max-width:200px;">{{ $voto->pergunta?->pergunta ?? '—' }}</td>
                    <td class="fw-semibold">{{ $voto->opcao?->nome ?? '—' }}</td>
                    <td>
                        <span class="token-mono">
                            {{ substr($voto->token_hash, 0, 4) }}...{{ substr($voto->token_hash, -4) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Nenhum voto encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ─── Logs da Eleição ───────────────────────────────────────────────── --}}
<h5 class="rel-header-title mt-4 mb-2">Logs da Eleição</h5>
<div class="card rel-card mb-4">
    <div class="card-body p-0">
        <table class="rel-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Horário</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logsEleicao as $i => $log)
                <tr>
                    <td style="color:#6c757d;font-size:.8rem;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;">
                        {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') : '—' }}
                    </td>
                    <td>{{ $log->usuario?->nome ?? '—' }}</td>
                    <td>
                        @php
                            $acaoEleicao = $log->acao;
                            $badgeEleicao = match($acaoEleicao) {
                                'votacao_aberta'    => ['style' => 'background:rgba(0,188,212,.15);color:#00899e;',    'label' => 'Votação Aberta'],
                                'votacao_encerrada' => ['style' => 'background:rgba(108,117,125,.15);color:#495057;', 'label' => 'Votação Encerrada'],
                                'votacao_reaberta'  => ['style' => 'background:rgba(255,193,7,.15);color:#856404;',   'label' => 'Votação Reaberta'],
                                'vida_aberta'       => ['style' => 'background:rgba(0,188,212,.15);color:#00899e;',   'label' => 'Vida Aberta'],
                                'vida_encerrada'    => ['style' => 'background:rgba(108,117,125,.15);color:#495057;', 'label' => 'Vida Encerrada'],
                                'vida_reaberta'     => ['style' => 'background:rgba(255,193,7,.15);color:#856404;',   'label' => 'Vida Reaberta'],
                                'eleicao_criada'    => ['style' => 'background:rgba(0,188,212,.15);color:#00899e;',   'label' => 'Eleição Criada'],
                                'membros_alterados' => ['style' => 'background:rgba(108,117,125,.15);color:#495057;', 'label' => 'Membros Alterados'],
                                default             => ['style' => 'background:rgba(108,117,125,.1);color:#6c757d;',  'label' => $acaoEleicao],
                            };
                        @endphp
                        <span style="font-size:.72rem;font-weight:600;padding:.2em .6em;border-radius:4px;font-family:'Montserrat',sans-serif;{{ $badgeEleicao['style'] }}">
                            {{ $badgeEleicao['label'] }}
                        </span>
                    </td>
                    <td>{{ $log->descricao ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Nenhum log de eleição encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ─── Logs do Sistema ───────────────────────────────────────────────── --}}
<h5 class="rel-header-title mt-2 mb-2">Logs do Sistema</h5>
<div class="card rel-card mb-4">
    <div class="card-body p-0">
        <table class="rel-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Horário</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Descrição</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logsSistema as $i => $log)
                <tr>
                    <td style="color:#6c757d;font-size:.8rem;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;">
                        {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') : '—' }}
                    </td>
                    <td>{{ $log->usuario?->nome ?? '—' }}</td>
                    <td>
                        @php
                            $acaoSistema = $log->acao;
                            $badgeSistema = match($acaoSistema) {
                                'login_sucesso'      => ['style' => 'background:rgba(25,135,84,.15);color:#146c43;',   'label' => 'Login OK'],
                                'login_falha'        => ['style' => 'background:rgba(220,53,69,.15);color:#b02a37;',   'label' => 'Login Falha'],
                                'logout'             => ['style' => 'background:rgba(108,117,125,.15);color:#495057;', 'label' => 'Logout'],
                                'usuario_criado'     => ['style' => 'background:rgba(13,110,253,.15);color:#0a58ca;',  'label' => 'Usuário Criado'],
                                'usuario_atualizado' => ['style' => 'background:rgba(255,193,7,.15);color:#856404;',   'label' => 'Usuário Atualizado'],
                                'usuario_removido'   => ['style' => 'background:rgba(220,53,69,.15);color:#b02a37;',   'label' => 'Usuário Removido'],
                                'membros_atualizados'=> ['style' => 'background:rgba(108,117,125,.15);color:#495057;', 'label' => 'Membros Atualizados'],
                                default              => ['style' => 'background:rgba(108,117,125,.1);color:#6c757d;',  'label' => $acaoSistema],
                            };
                        @endphp
                        <span style="font-size:.72rem;font-weight:600;padding:.2em .6em;border-radius:4px;font-family:'Montserrat',sans-serif;{{ $badgeSistema['style'] }}">
                            {{ $badgeSistema['label'] }}
                        </span>
                    </td>
                    <td>{{ $log->descricao ?? '—' }}</td>
                    <td style="font-family:'Courier New',monospace;font-size:.78rem;color:#6c757d;">
                        {{ $log->ip ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Nenhum log do sistema encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
