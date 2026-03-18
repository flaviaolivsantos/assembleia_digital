<div class="mb-3">
    <p class="text-muted small mb-3">
        Pré-visualização de como a pergunta aparecerá na tela de votação.
    </p>

    <div class="card">
        <div class="card-header">
            <strong>1. {{ $pergunta->pergunta }}</strong>
            <span class="badge text-bg-primary ms-2">
                0 / {{ $pergunta->qtd_respostas }}
            </span>
        </div>
        <div class="card-body">

            @if($pergunta->escopo === 'vida')
                {{-- Realidade de Vida: todos os candidatos juntos --}}
                @if($opcoes->isEmpty())
                    <p class="text-muted">Nenhum candidato cadastrado.</p>
                @else
                    <div class="row g-3">
                        @foreach($opcoes as $opcao)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 border-2">
                                    <img src="{{ $opcao->foto_url }}"
                                         alt="{{ $opcao->nome }}"
                                         style="width:100%; height:140px; object-fit:cover; border-radius:4px 4px 0 0;">
                                    <div class="card-body p-2 text-center">
                                        <span class="fw-semibold small">{{ $opcao->nome }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            @else
                {{-- Realidade de Aliança: agrupado por missão --}}
                @forelse($cidades as $ec)
                    @php $opcoesCidade = $opcoes->where('cidade_id', $ec->cidade_id); @endphp
                    <h6 class="mt-3 mb-2 text-muted">{{ $ec->cidade->nome }}</h6>
                    @if($opcoesCidade->isEmpty())
                        <p class="text-muted small">Nenhum candidato para esta missão.</p>
                    @else
                        <div class="row g-3 mb-3">
                            @foreach($opcoesCidade as $opcao)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card h-100 border-2">
                                        <img src="{{ $opcao->foto_url }}"
                                             alt="{{ $opcao->nome }}"
                                             style="width:100%; height:140px; object-fit:cover; border-radius:4px 4px 0 0;">
                                        <div class="card-body p-2 text-center">
                                            <span class="fw-semibold small">{{ $opcao->nome }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @empty
                    <p class="text-muted">Nenhuma missão vinculada à eleição.</p>
                @endforelse
            @endif

        </div>
    </div>
</div>
