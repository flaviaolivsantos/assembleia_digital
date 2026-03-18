<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EleicaoController;
use App\Http\Controllers\OpcaoController;
use App\Http\Controllers\PerguntaController;
use App\Http\Controllers\PresencaController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ResponsavelController;
use App\Http\Controllers\ResultadoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VotacaoController;
use App\Http\Controllers\MesarioController;
use App\Http\Controllers\VotacaoPublicaController;
use Illuminate\Support\Facades\Route;

// Raiz — redireciona para login ou dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Login / Logout
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (redireciona por perfil)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', 'acesso.ate']);

// Admin
Route::middleware(['auth', 'acesso.ate', 'perfil:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('cidades', CidadeController::class);
    Route::resource('eleicoes', EleicaoController::class)
        ->parameters(['eleicoes' => 'eleicao']);
    Route::resource('eleicoes.perguntas', PerguntaController::class)
        ->except(['index', 'show'])
        ->parameters(['eleicoes' => 'eleicao']);
    Route::get('eleicoes/{eleicao}/perguntas/{pergunta}/preview', [PerguntaController::class, 'preview'])
        ->name('eleicoes.perguntas.preview');
    Route::resource('eleicoes.perguntas.opcoes', OpcaoController::class)
        ->except(['show'])
        ->parameters(['eleicoes' => 'eleicao', 'opcoes' => 'opcao']);
    Route::get('eleicoes/{eleicao}/resultados', [ResultadoController::class, 'show'])->name('eleicoes.resultados');
    Route::get('eleicoes/{eleicao}/ata',        [ResultadoController::class, 'ata'])->name('eleicoes.ata');
    Route::get('eleicoes/{eleicao}/logs',       [LogController::class, 'index'])->name('eleicoes.logs');
    Route::resource('usuarios', UsuarioController::class)->except(['show']);
});

// Responsavel
Route::middleware(['auth', 'acesso.ate', 'perfil:responsavel'])->prefix('responsavel')->name('responsavel.')->group(function () {
    Route::get('/', [ResponsavelController::class, 'index'])->name('index');
    Route::get('/{eleicaoCidade}/abrir', [ResponsavelController::class, 'abrir'])->name('abrir');
    Route::post('/{eleicaoCidade}/abrir', [ResponsavelController::class, 'confirmarAbrir'])->name('confirmarAbrir');
    Route::get('/{eleicaoCidade}/encerrar', [ResponsavelController::class, 'encerrar'])->name('encerrar');
    Route::post('/{eleicaoCidade}/encerrar', [ResponsavelController::class, 'confirmarEncerrar'])->name('confirmarEncerrar');
    Route::get('/{eleicaoCidade}/resultados', [ResultadoController::class, 'showResponsavel'])->name('resultados');
    Route::get('/{eleicaoCidade}/ata',        [ResultadoController::class, 'ataResponsavel'])->name('ata');
    Route::get('/{eleicaoCidade}/membros',    [ResponsavelController::class, 'editarMembros'])->name('membros');
    Route::post('/{eleicaoCidade}/membros',   [ResponsavelController::class, 'atualizarMembros'])->name('membros.update');
});

// Mesario
Route::middleware(['auth', 'acesso.ate', 'perfil:mesario'])->prefix('mesario')->name('mesario.')->group(function () {
    Route::get('/',                                    [MesarioController::class,  'index'])->name('index');
    Route::get('/{eleicaoCidade}/presencas',          [PresencaController::class, 'index'])->name('presencas.index');
    Route::post('/{eleicaoCidade}/presencas',          [PresencaController::class, 'store'])->name('presencas.store');
    Route::get('/{eleicaoCidade}/presencas/token',     [PresencaController::class, 'token'])->name('presencas.token');
    Route::post('/{eleicaoCidade}/presencas/importar', [PresencaController::class, 'importar'])->name('presencas.importar');
});

// Votacao remota publica (sem login)
Route::prefix('votar')->name('votacao.')->group(function () {
    Route::get('/',          [VotacaoPublicaController::class, 'index'])->name('index');
    Route::post('/token',    [VotacaoPublicaController::class, 'validarToken'])->name('token');
    Route::get('/votacao',   [VotacaoPublicaController::class, 'votar'])->name('votar');
    Route::post('/votacao',  [VotacaoPublicaController::class, 'confirmarVoto'])->name('confirmarVoto');
    Route::get('/confirmado',[VotacaoPublicaController::class, 'confirmado'])->name('confirmado');
});

// Maquina de votacao (presencial, requer login)
Route::middleware(['auth', 'acesso.ate', 'perfil:maquina'])->prefix('votacao')->name('maquina.')->group(function () {
    Route::get('/',             [VotacaoController::class, 'index'])->name('index');
    Route::post('/token',       [VotacaoController::class, 'validarToken'])->name('token');
    Route::post('/presencial',  [VotacaoController::class, 'liberarPresencial'])->name('presencial');
    Route::get('/votar',        [VotacaoController::class, 'votar'])->name('votar');
    Route::post('/votar',       [VotacaoController::class, 'confirmarVoto'])->name('confirmarVoto');
    Route::get('/confirmado',   [VotacaoController::class, 'confirmado'])->name('confirmado');
});
