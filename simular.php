<?php
/**
 * Script de simulação — Assembleia Digital
 * Cria eleição completa com 100 votantes (50 por missão), usuários de todos os perfis,
 * gera tokens remotos e simula votos presenciais e remotos.
 *
 * Uso: php simular.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cidade;
use App\Models\Eleicao;
use App\Models\EleicaoCidade;
use App\Models\LogEleicao;
use App\Models\Opcao;
use App\Models\Pergunta;
use App\Models\Presenca;
use App\Models\TokenVotacao;
use App\Models\User;
use App\Models\Voto;
use Illuminate\Support\Facades\DB;

echo "\n=== Assembleia Digital — Simulação de Eleição ===\n\n";

// ─── 1. MISSÕES ───────────────────────────────────────────────────────────────
// Autenticar como admin para os logs funcionarem
$admin = User::where('perfil', 'admin')->first();
if (!$admin) {
    die("ERRO: Nenhum usuário admin encontrado. Crie um admin antes de rodar este script.\n");
}
auth()->login($admin);
echo "        Autenticado como: {$admin->email}\n\n";

echo "[ 1/7 ] Verificando missões...\n";

$tatui     = Cidade::firstOrCreate(['nome' => 'Tatuí']);
$fortaleza = Cidade::firstOrCreate(['nome' => 'Fortaleza']);

echo "        Tatuí (id={$tatui->id}) ✓\n";
echo "        Fortaleza (id={$fortaleza->id}) ✓\n";

// ─── 2. USUÁRIOS ──────────────────────────────────────────────────────────────
echo "\n[ 2/7 ] Criando usuários...\n";

$usuarios = [
    ['nome' => 'Responsável Tatuí',      'email' => 'resp.tatui@teste.com',       'perfil' => 'responsavel', 'cidade_id' => $tatui->id],
    ['nome' => 'Responsável Fortaleza',  'email' => 'resp.fortaleza@teste.com',    'perfil' => 'responsavel', 'cidade_id' => $fortaleza->id],
    ['nome' => 'Mesário Tatuí',          'email' => 'mesario.tatui@teste.com',     'perfil' => 'mesario',     'cidade_id' => $tatui->id],
    ['nome' => 'Mesário Fortaleza',      'email' => 'mesario.fortaleza@teste.com', 'perfil' => 'mesario',     'cidade_id' => $fortaleza->id],
    ['nome' => 'Máquina Tatuí 1',        'email' => 'maquina.tatui1@teste.com',    'perfil' => 'maquina',     'cidade_id' => $tatui->id],
    ['nome' => 'Máquina Tatuí 2',        'email' => 'maquina.tatui2@teste.com',    'perfil' => 'maquina',     'cidade_id' => $tatui->id],
    ['nome' => 'Máquina Fortaleza 1',    'email' => 'maquina.fortaleza1@teste.com','perfil' => 'maquina',     'cidade_id' => $fortaleza->id],
    ['nome' => 'Máquina Fortaleza 2',    'email' => 'maquina.fortaleza2@teste.com','perfil' => 'maquina',     'cidade_id' => $fortaleza->id],
];

$usersById = [];
foreach ($usuarios as $dados) {
    $user = User::updateOrCreate(
        ['email' => $dados['email']],
        array_merge($dados, ['password' => '123456'])
    );
    $usersById[$dados['email']] = $user;
    echo "        {$dados['perfil']} — {$dados['nome']} ({$dados['email']}) ✓\n";
}

$respTatui     = $usersById['resp.tatui@teste.com'];
$respFortaleza = $usersById['resp.fortaleza@teste.com'];
$maqTatui1     = $usersById['maquina.tatui1@teste.com'];
$maqTatui2     = $usersById['maquina.tatui2@teste.com'];
$maqFortaleza1 = $usersById['maquina.fortaleza1@teste.com'];
$maqFortaleza2 = $usersById['maquina.fortaleza2@teste.com'];

// ─── 3. ELEIÇÃO ───────────────────────────────────────────────────────────────
echo "\n[ 3/7 ] Criando eleição...\n";

$eleicao = Eleicao::create([
    'titulo'       => 'Assembleia Geral — Simulação 2025',
    'data_eleicao' => now()->toDateString(),
    'status'       => 'aberta',
]);

echo "        Eleição criada (id={$eleicao->id}) ✓\n";

// Vincular missões: 50 membros cada (30 presencial + 20 remoto), 60 eleitores aptos
$ecTatui = EleicaoCidade::create([
    'eleicao_id'        => $eleicao->id,
    'cidade_id'         => $tatui->id,
    'qtd_eleitorado'    => 60,
    'qtd_presencial'    => 30,
    'qtd_remoto'        => 20,
    'qtd_membros'       => 50,
    'votos_registrados' => 0,
    'votos_presenciais' => 0,
    'aberta'            => true,
    'data_abertura'     => now(),
    'aberta_por'        => $respTatui->id,
]);

$ecFortaleza = EleicaoCidade::create([
    'eleicao_id'        => $eleicao->id,
    'cidade_id'         => $fortaleza->id,
    'qtd_eleitorado'    => 60,
    'qtd_presencial'    => 30,
    'qtd_remoto'        => 20,
    'qtd_membros'       => 50,
    'votos_registrados' => 0,
    'votos_presenciais' => 0,
    'aberta'            => true,
    'data_abertura'     => now(),
    'aberta_por'        => $respFortaleza->id,
]);

LogEleicao::registrar($eleicao->id, 'votacao_aberta', "Votação aberta em Tatuí.");
LogEleicao::registrar($eleicao->id, 'votacao_aberta', "Votação aberta em Fortaleza.");

echo "        Tatuí: 60 aptos, 50 membros (30 presencial + 20 remoto) ✓\n";
echo "        Fortaleza: 60 aptos, 50 membros (30 presencial + 20 remoto) ✓\n";

// ─── 4. PERGUNTAS E CANDIDATOS ────────────────────────────────────────────────
echo "\n[ 4/7 ] Criando perguntas e candidatos...\n";

// Pergunta 1 — Realidade de Aliança (candidatos por missão)
$pAlianca = Pergunta::create([
    'eleicao_id'    => $eleicao->id,
    'pergunta'      => 'Escolha o representante da missão para a Assembleia',
    'qtd_respostas' => 2,
    'escopo'        => 'alianca',
    'ordem'         => 1,
]);

$candidatosTatui = ['Ana Carvalho', 'Bruno Ferreira', 'Carla Mendes', 'Daniel Oliveira', 'Elisa Santos'];
$candidatosFortaleza = ['Fábio Lima', 'Gabriela Rocha', 'Henrique Alves', 'Isabela Costa', 'João Pedro'];

foreach ($candidatosTatui as $nome) {
    Opcao::create(['pergunta_id' => $pAlianca->id, 'nome' => $nome, 'cidade_id' => $tatui->id]);
}
foreach ($candidatosFortaleza as $nome) {
    Opcao::create(['pergunta_id' => $pAlianca->id, 'nome' => $nome, 'cidade_id' => $fortaleza->id]);
}

echo "        Pergunta 1 (Aliança): 5 candidatos Tatuí + 5 Fortaleza ✓\n";

// Pergunta 2 — Realidade de Vida (candidatos nacionais)
$pVida = Pergunta::create([
    'eleicao_id'    => $eleicao->id,
    'pergunta'      => 'Escolha o coordenador nacional de Vida',
    'qtd_respostas' => 1,
    'escopo'        => 'vida',
    'ordem'         => 2,
]);

$candidatosNacionais = ['Pastor Ricardo Souza', 'Pr. Marcos Evangelista', 'Missionário André Lima'];
foreach ($candidatosNacionais as $nome) {
    Opcao::create(['pergunta_id' => $pVida->id, 'nome' => $nome, 'cidade_id' => null]);
}

echo "        Pergunta 2 (Vida): 3 candidatos nacionais ✓\n";

// Recarregar com opções
$eleicao->load('perguntas.opcoes');
$opcoesTatui     = $pAlianca->opcoes->where('cidade_id', $tatui->id)->values();
$opcoesFortaleza = $pAlianca->opcoes->where('cidade_id', $fortaleza->id)->values();
$opcoesVida      = $pVida->opcoes->values();

// ─── 5. TOKENS REMOTOS ────────────────────────────────────────────────────────
echo "\n[ 5/7 ] Gerando tokens remotos...\n";

$tokensTatui = [];
for ($i = 1; $i <= 20; $i++) {
    $resultado = TokenVotacao::gerar($eleicao->id, $tatui->id);
    Presenca::create([
        'eleicao_id' => $eleicao->id,
        'cidade_id'  => $tatui->id,
        'nome'       => "Membro Remoto Tatuí {$i}",
        'votou'      => false,
    ]);
    $tokensTatui[] = $resultado['hash'];
}

$tokensFortaleza = [];
for ($i = 1; $i <= 20; $i++) {
    $resultado = TokenVotacao::gerar($eleicao->id, $fortaleza->id);
    Presenca::create([
        'eleicao_id' => $eleicao->id,
        'cidade_id'  => $fortaleza->id,
        'nome'       => "Membro Remoto Fortaleza {$i}",
        'votou'      => false,
    ]);
    $tokensFortaleza[] = $resultado['hash'];
}

echo "        20 tokens gerados para Tatuí ✓\n";
echo "        20 tokens gerados para Fortaleza ✓\n";

// ─── 6. SIMULAR VOTOS ─────────────────────────────────────────────────────────
echo "\n[ 6/7 ] Simulando votos...\n";

$votosRegistradosTatui     = 0;
$votosRegistradosFortaleza = 0;
$votosPresenciaisTatui     = 0;
$votosPresenciaisFortaleza = 0;

// Função auxiliar para votar (seleciona N opções aleatórias da lista)
function votar(array $perguntas, array $opcoesPorPergunta, string $tokenHash, string $origem, ?int $maquinaId): void
{
    foreach ($perguntas as $pergunta) {
        $opcoes = $opcoesPorPergunta[$pergunta->id];
        $qtd    = min($pergunta->qtd_respostas, count($opcoes));
        $escolhidas = collect($opcoes)->shuffle()->take($qtd);
        foreach ($escolhidas as $opcao) {
            Voto::create([
                'token_hash'  => $tokenHash,
                'pergunta_id' => $pergunta->id,
                'opcao_id'    => $opcao->id,
                'origem'      => $origem,
                'maquina_id'  => $maquinaId,
                'created_at'  => now(),
            ]);
        }
    }
}

$perguntasTatui     = collect([$pAlianca, $pVida]);
$perguntasFortaleza = collect([$pAlianca, $pVida]);

$opcoesPorPerguntaTatui = [
    $pAlianca->id => $opcoesTatui->all(),
    $pVida->id    => $opcoesVida->all(),
];
$opcoesPorPerguntaFortaleza = [
    $pAlianca->id => $opcoesFortaleza->all(),
    $pVida->id    => $opcoesVida->all(),
];

// — Votos REMOTOS Tatuí (18 de 20 tokens usados)
foreach (array_slice($tokensTatui, 0, 18) as $hash) {
    votar($perguntasTatui->all(), $opcoesPorPerguntaTatui, $hash, 'remoto', null);
    TokenVotacao::where('token_hash', $hash)->update(['usado' => true]);
    $votosRegistradosTatui++;
}

// — Votos REMOTOS Fortaleza (17 de 20 tokens usados)
foreach (array_slice($tokensFortaleza, 0, 17) as $hash) {
    votar($perguntasFortaleza->all(), $opcoesPorPerguntaFortaleza, $hash, 'remoto', null);
    TokenVotacao::where('token_hash', $hash)->update(['usado' => true]);
    $votosRegistradosFortaleza++;
}

// — Votos PRESENCIAIS Tatuí (28 de 30 slots) — distribuídos entre 2 máquinas
$maquinasTatui = [$maqTatui1->id, $maqTatui2->id];
for ($i = 0; $i < 28; $i++) {
    $hash      = hash('sha256', 'presencial_tatui_' . $i . '_' . uniqid());
    $maquinaId = $maquinasTatui[$i % 2];
    votar($perguntasTatui->all(), $opcoesPorPerguntaTatui, $hash, 'presencial', $maquinaId);
    $votosRegistradosTatui++;
    $votosPresenciaisTatui++;
}

// — Votos PRESENCIAIS Fortaleza (26 de 30 slots) — distribuídos entre 2 máquinas
$maquinasFortaleza = [$maqFortaleza1->id, $maqFortaleza2->id];
for ($i = 0; $i < 26; $i++) {
    $hash      = hash('sha256', 'presencial_fortaleza_' . $i . '_' . uniqid());
    $maquinaId = $maquinasFortaleza[$i % 2];
    votar($perguntasFortaleza->all(), $opcoesPorPerguntaFortaleza, $hash, 'presencial', $maquinaId);
    $votosRegistradosFortaleza++;
    $votosPresenciaisFortaleza++;
}

// Atualizar contadores nas missões
$ecTatui->update([
    'votos_registrados' => $votosRegistradosTatui,
    'votos_presenciais' => $votosPresenciaisTatui,
]);
$ecFortaleza->update([
    'votos_registrados' => $votosRegistradosFortaleza,
    'votos_presenciais' => $votosPresenciaisFortaleza,
]);

echo "        Tatuí: {$votosRegistradosTatui} votos ({$votosPresenciaisTatui} presencial + 18 remoto) ✓\n";
echo "        Fortaleza: {$votosRegistradosFortaleza} votos ({$votosPresenciaisFortaleza} presencial + 17 remoto) ✓\n";

// ─── 7. ENCERRAR ──────────────────────────────────────────────────────────────
echo "\n[ 7/7 ] Encerrando votação...\n";

$ecTatui->update([
    'aberta'            => false,
    'data_encerramento' => now(),
    'encerrada_por'     => $respTatui->id,
]);
$ecFortaleza->update([
    'aberta'            => false,
    'data_encerramento' => now(),
    'encerrada_por'     => $respFortaleza->id,
]);
$eleicao->update(['status' => 'encerrada']);

LogEleicao::registrar($eleicao->id, 'votacao_encerrada', "Votação encerrada em Tatuí. Justificativa: Encerramento da simulação de testes.");
LogEleicao::registrar($eleicao->id, 'votacao_encerrada', "Votação encerrada em Fortaleza. Justificativa: Encerramento da simulação de testes.");

echo "        Ambas as missões encerradas ✓\n";
echo "        Eleição status: encerrada ✓\n";

// ─── RESUMO ───────────────────────────────────────────────────────────────────
echo "\n════════════════════════════════════════\n";
echo " SIMULAÇÃO CONCLUÍDA\n";
echo "════════════════════════════════════════\n\n";

echo " Eleição: {$eleicao->titulo} (id={$eleicao->id})\n\n";

echo " Tatuí\n";
echo "   Aptos:        60\n";
echo "   Compareceram: 50\n";
echo "   Votaram:      {$votosRegistradosTatui}  (ader. " . round(50/60*100,1) . "% · aprov. " . round($votosRegistradosTatui/50*100,1) . "%)\n";
echo "   Presencial:   {$votosPresenciaisTatui} votos (2 máquinas)\n";
echo "   Remoto:       18 votos\n\n";

echo " Fortaleza\n";
echo "   Aptos:        60\n";
echo "   Compareceram: 50\n";
echo "   Votaram:      {$votosRegistradosFortaleza}  (ader. " . round(50/60*100,1) . "% · aprov. " . round($votosRegistradosFortaleza/50*100,1) . "%)\n";
echo "   Presencial:   {$votosPresenciaisFortaleza} votos (2 máquinas)\n";
echo "   Remoto:       17 votos\n\n";

echo " Usuários de teste (senha: 123456)\n";
echo "   admin@teste.com              → Administrador\n";
echo "   resp.tatui@teste.com         → Responsável Tatuí\n";
echo "   resp.fortaleza@teste.com     → Responsável Fortaleza\n";
echo "   mesario.tatui@teste.com      → Mesário Tatuí\n";
echo "   mesario.fortaleza@teste.com  → Mesário Fortaleza\n";
echo "   maquina.tatui1@teste.com     → Máquina Tatuí 1\n";
echo "   maquina.tatui2@teste.com     → Máquina Tatuí 2\n";
echo "   maquina.fortaleza1@teste.com → Máquina Fortaleza 1\n";
echo "   maquina.fortaleza2@teste.com → Máquina Fortaleza 2\n\n";

echo " Resultados disponíveis em:\n";
echo "   /admin/eleicoes/{$eleicao->id}/resultados\n";
echo "   /admin/eleicoes/{$eleicao->id}/ata\n\n";
