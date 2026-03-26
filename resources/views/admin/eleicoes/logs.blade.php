@extends('layouts.admin')
@section('page-title', 'Logs da Eleição')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Logs de Auditoria</h2>
        <p class="text-muted mb-0">{{ $eleicao->titulo }}</p>
    </div>
    <a href="{{ route('admin.eleicoes.show', $eleicao) }}" class="btn btn-outline-secondary">Voltar</a>
</div>

@php
$labels = [
    'eleicao_criada'      => ['text' => 'Eleição Criada',       'badge' => 'primary'],
    'eleicao_atualizada'  => ['text' => 'Eleição Atualizada',   'badge' => 'secondary'],
    'votacao_aberta'      => ['text' => 'Votação Aberta',       'badge' => 'success'],
    'votacao_encerrada'   => ['text' => 'Votação Encerrada',    'badge' => 'dark'],
    'membros_alterados'   => ['text' => 'Membros Alterados',    'badge' => 'warning'],
    'pergunta_adicionada' => ['text' => 'Pergunta Adicionada',  'badge' => 'info'],
    'pergunta_atualizada' => ['text' => 'Pergunta Atualizada',  'badge' => 'info'],
    'pergunta_removida'   => ['text' => 'Pergunta Removida',    'badge' => 'danger'],
    'candidato_adicionado'=> ['text' => 'Candidato Adicionado', 'badge' => 'info'],
    'candidato_atualizado'=> ['text' => 'Candidato Atualizado', 'badge' => 'info'],
    'candidato_removido'  => ['text' => 'Candidato Removido',   'badge' => 'danger'],
];
@endphp

<div class="card">
    <div class="card-body p-0">
        @if($logs->isEmpty())
            <p class="text-muted text-center py-4">Nenhum log registrado ainda.</p>
        @else
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data/Hora</th>
                        <th>Ação</th>
                        <th>Usuário</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        @php $info = $labels[$log->acao] ?? ['text' => $log->acao, 'badge' => 'secondary']; @endphp
                        <tr>
                            <td class="text-muted small text-nowrap">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td>
                                <span class="badge text-bg-{{ $info['badge'] }}">{{ $info['text'] }}</span>
                            </td>
                            <td class="small">{{ $log->usuario->nome ?? '—' }}</td>
                            <td class="small text-muted">{{ $log->descricao ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
