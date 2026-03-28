@extends('layouts.admin')
@section('page-title', 'Editar Usuário')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Usuário</h2>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card" style="max-width: 560px;">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $erro)
                    <div>{{ $erro }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control"
                       value="{{ old('nome', $usuario->nome) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $usuario->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nova Senha <span class="text-muted small">(deixe em branco para manter)</span></label>
                <div class="input-group">
                    <div class="password-field flex-grow-1">
                        <input type="password" id="password" name="password" class="form-control">
                        <button type="button" class="password-toggle" tabindex="-1"
                                onclick="toggleSenha('password','ico-password')">
                            <i class="bi bi-eye" id="ico-password"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" onclick="gerarSenha()" title="Gerar senha aleatória de 4 dígitos">
                        <i class="bi bi-shuffle"></i> Gerar
                    </button>
                </div>
                <div id="senha-gerada" class="form-text text-success fw-semibold" style="display:none;"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar Nova Senha</label>
                <div class="password-field">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    <button type="button" class="password-toggle" tabindex="-1"
                            onclick="toggleSenha('password_confirmation','ico-password-conf')">
                        <i class="bi bi-eye" id="ico-password-conf"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Perfil</label>
                <select name="perfil" class="form-select" required id="select-perfil">
                    <option value="admin"       {{ old('perfil', $usuario->perfil) === 'admin'       ? 'selected' : '' }}>Administrador</option>
                    <option value="responsavel" {{ old('perfil', $usuario->perfil) === 'responsavel' ? 'selected' : '' }}>Responsável Local</option>
                    <option value="mesario"     {{ old('perfil', $usuario->perfil) === 'mesario'     ? 'selected' : '' }}>Mesário</option>
                    <option value="maquina"     {{ old('perfil', $usuario->perfil) === 'maquina'     ? 'selected' : '' }}>Máquina de Votação</option>
                </select>
            </div>

            <div class="mb-3" id="campo-cidade">
                <label class="form-label">Missão</label>
                <select name="cidade_id" class="form-select">
                    <option value="">— Nenhuma —</option>
                    @foreach($cidades as $cidade)
                        <option value="{{ $cidade->id }}"
                            {{ old('cidade_id', $usuario->cidade_id) == $cidade->id ? 'selected' : '' }}>
                            {{ $cidade->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="campo-escopo" style="{{ old('perfil', $usuario->perfil) === 'maquina' ? '' : 'display:none;' }}">
                <label class="form-label d-block">Realidade visível</label>
                @php $escopoAtual = old('escopo_maquina', $usuario->escopo_maquina ?? 'ambos'); @endphp
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="escopo_alianca" id="escopo_alianca" value="1"
                        {{ in_array($escopoAtual, ['ambos','alianca']) ? 'checked' : '' }}>
                    <label class="form-check-label" for="escopo_alianca">Aliança</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="escopo_vida" id="escopo_vida" value="1"
                        {{ in_array($escopoAtual, ['ambos','vida']) ? 'checked' : '' }}>
                    <label class="form-check-label" for="escopo_vida">Vida</label>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Acesso válido até <span class="text-muted small">(opcional)</span></label>
                <input type="datetime-local" name="acesso_ate" class="form-control @error('acesso_ate') is-invalid @enderror"
                       value="{{ old('acesso_ate', $usuario->acesso_ate?->format('Y-m-d\TH:i')) }}">
                @error('acesso_ate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Deixe em branco para acesso ilimitado.</div>
            </div>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
</div>

<script>
function toggleSenha(inputId, icoId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(icoId);
    if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; ico.className = 'bi bi-eye'; }
}

function gerarSenha() {
    const senha = String(Math.floor(1000 + Math.random() * 9000));
    document.getElementById('password').value = senha;
    document.getElementById('password_confirmation').value = senha;
    document.getElementById('password').type = 'text';
    document.getElementById('password_confirmation').type = 'text';
    document.getElementById('ico-password').className = 'bi bi-eye-slash';
    document.getElementById('ico-password-conf').className = 'bi bi-eye-slash';
    const aviso = document.getElementById('senha-gerada');
    aviso.textContent = 'Senha gerada: ' + senha + ' — anote antes de salvar.';
    aviso.style.display = '';
}

const perfil      = document.getElementById('select-perfil');
const campoCidade = document.getElementById('campo-cidade');
const campoEscopo = document.getElementById('campo-escopo');

function atualizarCampos() {
    campoCidade.style.display = perfil.value === 'admin'   ? 'none' : '';
    campoEscopo.style.display = perfil.value === 'maquina' ? ''     : 'none';
}

perfil.addEventListener('change', atualizarCampos);
atualizarCampos();
</script>
@endsection
