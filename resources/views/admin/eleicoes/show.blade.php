@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>{{ $eleicao->titulo }}</h2>
        @if($eleicao->data_eleicao)
        <span class="text-muted">{{ $eleicao->data_eleicao->format('d/m/Y') }}</span>
        @endif
        &nbsp;
        @php $badges = ['rascunho' => 'secondary', 'aberta' => 'success', 'encerrada' => 'dark']; @endphp
        <span class="badge text-bg-{{ $badges[$eleicao->status] ?? 'secondary' }}">
            {{ ucfirst($eleicao->status) }}
        </span>
    </div>
    <div class="d-flex gap-2">
        @if($eleicao->status === 'encerrada')
            <a href="{{ route('admin.eleicoes.resultados', ['eleicao' => $eleicao->id]) }}" class="btn btn-success">Ver Resultados</a>
        @endif
        <a href="{{ route('admin.eleicoes.logs', ['eleicao' => $eleicao->id]) }}" class="btn btn-outline-dark">Logs</a>
        @if(!$eleicao->estaAberta())
            <a href="{{ route('admin.eleicoes.edit', ['eleicao' => $eleicao->id]) }}" class="btn btn-outline-secondary">Editar</a>
        @endif
        <a href="{{ route('admin.eleicoes.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">{{ session('sucesso') }}</div>
@endif

<div class="card mb-4">
    <div class="card-header"><strong>Missões participantes</strong></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Missão</th>
                    <th>Membros</th>
                    <th>Votos</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eleicao->cidades as $ec)
                    <tr>
                        <td>{{ $ec->cidade->nome }}</td>
                        <td>{{ $ec->qtd_membros }}</td>
                        <td>{{ $ec->votos_registrados }}</td>
                        <td>
                            @if($ec->aberta)
                                <span class="badge text-bg-success">Aberta</span>
                            @elseif($ec->data_encerramento)
                                <span class="badge text-bg-dark">Encerrada</span>
                            @else
                                <span class="badge text-bg-secondary">Aguardando</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Nenhuma missão vinculada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Perguntas</strong>
        @if(!$eleicao->estaAberta())
            <a href="{{ route('admin.eleicoes.perguntas.create', ['eleicao' => $eleicao->id]) }}" class="btn btn-sm btn-primary">+ Adicionar Pergunta</a>
        @endif
    </div>
    <div class="card-body">
        @forelse($eleicao->perguntas as $pergunta)
            <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $loop->iteration }}.</strong> {{ $pergunta->pergunta }}
                    <span class="text-muted small ms-2">({{ $pergunta->qtd_respostas }} resposta(s) obrigatoria(s))</span>
                </div>
                @if(!$eleicao->estaAberta())
                    <div class="d-flex gap-1 ms-3 flex-shrink-0">
                        <a href="{{ route('admin.eleicoes.perguntas.opcoes.index', ['eleicao' => $eleicao->id, 'pergunta' => $pergunta->id]) }}"
                            class="btn btn-sm btn-outline-primary">Candidatos</a>
                        <a href="{{ route('admin.eleicoes.perguntas.edit', ['eleicao' => $eleicao->id, 'pergunta' => $pergunta->id]) }}"
                            class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form method="POST" action="{{ route('admin.eleicoes.perguntas.destroy', ['eleicao' => $eleicao->id, 'pergunta' => $pergunta->id]) }}"
                              onsubmit="return confirm('Remover esta pergunta e todos os candidatos?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-muted">Nenhuma pergunta cadastrada.</p>
        @endforelse
    </div>
</div>
@endsection
