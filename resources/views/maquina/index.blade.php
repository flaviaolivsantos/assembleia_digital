<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eleição dos Membros Delegados</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_recado.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #F0F2F5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* ── Wrapper centralizado ───────────────────────── */
        .login-wrap {
            width: 100%;
            max-width: 440px;
        }

        /* ── Banner (imagem roxo de branding) ──────────── */
        .brand-banner {
            width: 100%;
            border-radius: 14px 14px 0 0;
            display: block;
            object-fit: cover;
        }

        /* ── Card de login ──────────────────────────────── */
        .login-card {
            background: #fff;
            border-radius: 0 0 14px 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,.12);
            padding: 2rem 2rem 1.5rem;
        }

        /* ── Título eleição ─────────────────────────────── */
        .election-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        /* ── Escopo selector ────────────────────────────── */
        .escopo-label {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            margin-bottom: .5rem;
        }
        .escopo-options { display: flex; gap: .5rem; margin-bottom: 1.25rem; }
        .escopo-card {
            flex: 1;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: .65rem .9rem;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            font-size: .85rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .escopo-card input[type=radio] { accent-color: #00BCD4; }
        .escopo-card:has(input:checked) {
            border-color: #00BCD4;
            background: #f0fdff;
            color: #111827;
        }

        /* ── Escopo único (apenas aliança ou vida) ──────── */
        .escopo-single {
            font-size: .82rem;
            color: #374151;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }
        .escopo-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #00BCD4;
            flex-shrink: 0;
        }

        /* ── Contador presencial ────────────────────────── */
        .contador-row {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .contador-item {
            font-size: .82rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: .3rem;
        }
        .contador-num {
            font-weight: 700;
            color: #00BCD4;
            font-size: .9rem;
        }

        /* ── Instrução ──────────────────────────────────── */
        .instrucao {
            font-size: .8rem;
            color: #4b5563;
            margin-bottom: 1rem;
            text-align: center;
        }

        /* ── Campo de senha ─────────────────────────────── */
        .password-wrap { position: relative; margin-bottom: 1.25rem; }
        .password-input {
            width: 100%;
            padding: .75rem 2.8rem .75rem 1rem;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            color: #111827;
            text-align: center;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #fff;
        }
        .password-input::placeholder { color: #9ca3af; }
        .password-input:focus {
            border-color: #00BCD4;
            box-shadow: 0 0 0 3px rgba(0,188,212,.12);
        }
        .password-toggle {
            position: absolute; right: 0; top: 0;
            height: 100%; width: 2.8rem;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; cursor: pointer;
            color: #6b7280; font-size: .95rem; transition: color .2s; outline: none;
        }
        .password-toggle:hover { color: #00BCD4; }

        /* ── Botão principal ────────────────────────────── */
        .btn-liberar {
            width: 100%;
            padding: .85rem;
            background: #00BCD4;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .3px;
            cursor: pointer;
            transition: filter .15s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }
        .btn-liberar:hover  { filter: brightness(.92); transform: translateY(-1px); }
        .btn-liberar:active { transform: translateY(0); }

        /* ── Botão sair ─────────────────────────────────── */
        .btn-sair {
            width: 100%;
            margin-top: .75rem;
            padding: .55rem;
            background: transparent;
            color: #6b7280;
            border: none;
            font-family: inherit;
            font-size: .82rem;
            cursor: pointer;
            transition: color .15s;
            text-align: center;
        }
        .btn-sair:hover { color: #111827; }

        /* ── Alerta de erro ─────────────────────────────── */
        .alert-erro {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: .6rem .85rem;
            font-size: .82rem;
            color: #b91c1c;
            margin-bottom: 1rem;
        }
        .alert-aviso {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: .6rem .85rem;
            font-size: .85rem;
            color: #92400e;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-wrap">

    {{-- ── Banner de branding ──────────────────────────── --}}
    @php
        $nomeCidade   = $eleicaoCidade?->cidade?->nome ?? '';
        $arquivoCidade = public_path('images/' . $nomeCidade . '.png');
        $bannerImg    = file_exists($arquivoCidade) ? asset('images/' . $nomeCidade . '.png') : asset('images/Tatui.png');
    @endphp
    <img src="{{ $bannerImg }}" alt="{{ $nomeCidade }}" class="brand-banner">

    {{-- ── Card de login ──────────────────────────────── --}}
    <div class="login-card">

        @if(!$eleicaoCidade || (!$aliancaAberta && !$vidaAberta))
            <div class="alert-aviso">Nenhuma votação aberta para esta cidade no momento.</div>
        @else

            {{-- Título da eleição --}}
            <div class="election-title">{{ $eleicaoCidade->eleicao->titulo }}</div>

            @if($errors->any())
                <div class="alert-erro">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('maquina.presencial') }}">
                @csrf

                {{-- Escopo --}}
                @if($aliancaAberta && $vidaAberta)
                    <div class="escopo-label">Tipo de votante</div>
                    <div class="escopo-options">
                        <label class="escopo-card">
                            <input type="radio" name="escopo" value="alianca"
                                   {{ old('escopo', 'alianca') === 'alianca' ? 'checked' : '' }} required>
                            Realidade de Aliança
                        </label>
                        <label class="escopo-card">
                            <input type="radio" name="escopo" value="vida"
                                   {{ old('escopo') === 'vida' ? 'checked' : '' }}>
                            Realidade de Vida
                        </label>
                    </div>
                @elseif($aliancaAberta)
                    <input type="hidden" name="escopo" value="alianca">
                    <div class="escopo-single">
                        <span class="escopo-dot"></span>Realidade de Aliança
                    </div>
                @else
                    <input type="hidden" name="escopo" value="vida">
                    <div class="escopo-single">
                        <span class="escopo-dot"></span>Realidade de Vida
                    </div>
                @endif

                {{-- Contadores --}}
                @if($aliancaAberta && $eleicaoCidade->qtd_presencial > 0)
                <div class="contador-row">
                    <span class="contador-item">
                        <span class="contador-num">{{ $eleicaoCidade->votos_presenciais }}</span>
                        / {{ $eleicaoCidade->qtd_presencial }} presenciais
                    </span>
                </div>
                @endif
                @if($vidaAberta && $eleicaoCidade->qtd_presencial_vida > 0)
                <div class="contador-row">
                    <span class="contador-item">
                        <span class="contador-num">{{ $eleicaoCidade->votos_presenciais_vida }}</span>
                        / {{ $eleicaoCidade->qtd_presencial_vida }} presenciais vida
                    </span>
                </div>
                @endif

                <p class="instrucao">Digite sua senha para liberar a votação.</p>

                {{-- Campo senha --}}
                <div class="password-wrap">
                    <input type="password" id="senha-maquina" name="senha"
                        class="password-input"
                        placeholder="Sua senha"
                        autocomplete="current-password"
                        autofocus required>
                    <button type="button" class="password-toggle" tabindex="-1"
                            onclick="toggleSenha('senha-maquina','ico-senha-maquina')">
                        <i class="bi bi-eye" id="ico-senha-maquina"></i>
                    </button>
                </div>

                {{-- Liberar --}}
                <button type="submit" class="btn-liberar">
                    <i class="bi bi-unlock-fill"></i> Liberar Votação
                </button>
            </form>

            {{-- Sair --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-sair">Sair</button>
            </form>

        @endif
    </div>

</div>
<script>
function toggleSenha(inputId, icoId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(icoId);
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
