@extends('layouts.admin')
@section('page-title', 'Mesário')

@section('content')
<div class="mb-4">
    <h2 style="font-family:'Montserrat',sans-serif;font-size:1.35rem;font-weight:700;color:#111827;margin:0;">Painel do Mesário</h2>
    <p style="font-size:.85rem;color:#6B7280;margin:.1rem 0 0;">Selecione a votação que deseja gerenciar.</p>
</div>

@if(session('sucesso'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('sucesso') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($eleicoesCidade->isEmpty())
    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:2.5rem;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.06);">
        <i class="bi bi-inbox" style="font-size:2rem;color:#D1D5DB;"></i>
        <p style="margin:.5rem 0 0;color:#6B7280;">Nenhuma votação aberta no momento para sua missão.</p>
    </div>
@else
    <div class="row g-3">
        @foreach($eleicoesCidade as $ec)
            <div class="col-md-6">
                <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden;">
                    <div style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;padding:.85rem 1.25rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem;flex-wrap:wrap;">
                        <span style="font-family:'Montserrat',sans-serif;font-size:.9rem;font-weight:700;color:#111827;">
                            {{ $ec->eleicao->titulo }}
                        </span>
                        @if(auth()->user()->perfil === 'admin')
                            <span style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#6B7280;display:inline-flex;align-items:center;gap:4px;">
                                <i class="bi bi-geo-alt"></i>{{ $ec->cidade->nome }}
                            </span>
                        @endif
                    </div>
                    <div style="padding:1rem 1.25rem;">
                        <p style="font-size:.78rem;color:#6B7280;margin:0 0 1rem;display:flex;align-items:center;gap:5px;">
                            <i class="bi bi-play-circle text-success"></i>
                            Aberta em {{ \Carbon\Carbon::parse($ec->data_abertura)->format('d/m/Y H:i') }}
                        </p>
                        <a href="{{ route('mesario.presencas.index', $ec) }}"
                           style="font-family:'Montserrat',sans-serif;font-size:.78rem;font-weight:600;background:#1B2A3B;color:#fff;padding:.45rem 1rem;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:filter .15s;"
                           onmouseover="this.style.filter='brightness(.88)'" onmouseout="this.style.filter=''">
                            <i class="bi bi-people-fill"></i>Gerenciar Presenças
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
