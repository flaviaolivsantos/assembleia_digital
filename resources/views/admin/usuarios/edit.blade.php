@extends('layouts.app')

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
                    <input type="password" id="password" name="password" class="form-control">
                    <button type="button" class="btn btn-outline-secondary" tabindex="-1"
                            onclick="toggleSenha('password','ico-password')">
                        <i class="bi bi-eye" id="ico-password"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar Nova Senha</label>
                <div class="input-group">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    <button type="button" class="btn btn-outline-secondary" tabindex="-1"
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

const perfil      = document.getElementById('select-perfil');
const campoCidade = document.getElementById('campo-cidade');

perfil.addEventListener('change', function () {
    campoCidade.style.display = this.value === 'admin' ? 'none' : '';
});

if (perfil.value === 'admin') campoCidade.style.display = 'none';
</script>
@endsection
