# Assembleia Digital — Roteiro de Teste do Zero

> Este roteiro cobre um teste completo: configuração da eleição, votação presencial, votação remota, encerramento e apuração de resultados.

---

## Pré-requisitos

- Servidor rodando: `php artisan serve` → acesse `http://localhost:8000`
- Banco migrado e populado: `php artisan migrate:fresh --seed`
- Usuário administrador criado (veja abaixo se necessário)

> Após `migrate:fresh --seed`, as missões **Tatuí** e **Fortaleza** são criadas automaticamente.

---

## Etapa 0 — Verificar/criar o administrador

Se o banco foi zerado, recrie o admin via terminal:

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
App\Models\User::create([
    'nome'     => 'Administrador',
    'email'    => 'admin@teste.com',
    'password' => '123456',
    'perfil'   => 'admin',
]);
echo 'Admin criado.' . PHP_EOL;
"
```

Login: `admin@teste.com` / Senha: `123456`

---

## Etapa 1 — Criar usuários de teste

Acesse: **Admin > Usuários > Novo Usuário**

Crie os seguintes usuários:

| Nome | E-mail | Senha | Perfil | Missão |
|------|--------|-------|--------|--------|
| Responsável Tatuí | resp.tatui@teste.com | 123456 | Responsável Local | Tatuí |
| Responsável Fortaleza | resp.fortaleza@teste.com | 123456 | Responsável Local | Fortaleza |
| Mesário Tatuí | mesario.tatui@teste.com | 123456 | Mesário | Tatuí |
| Máquina Tatuí 1 | maquina1@teste.com | 123456 | Máquina de Votação | Tatuí |

> Deixe o campo "Acesso válido até" em branco para acesso ilimitado.

---

## Etapa 2 — Criar a eleição

Acesse: **Admin > Eleições > Nova Eleição**

Preencha:
- **Título**: Eleição de Teste 2025
- **Data**: (data de hoje ou futura)

Salve. A eleição aparece com status **Rascunho**.

---

## Etapa 3 — Adicionar missões à eleição

Dentro da eleição criada, vá em **Missões** e adicione:
- Tatuí
- Fortaleza

---

## Etapa 4 — Criar perguntas

Dentro da eleição, vá em **Perguntas > Nova Pergunta**.

### Pergunta 1 — Realidade de Aliança

| Campo | Valor |
|-------|-------|
| Pergunta | Escolha o representante da missão |
| Qtd. de respostas obrigatórias | 1 |
| Escopo | Realidade de Aliança |

### Pergunta 2 — Realidade de Vida

| Campo | Valor |
|-------|-------|
| Pergunta | Escolha o coordenador nacional |
| Qtd. de respostas obrigatórias | 1 |
| Escopo | Realidade de Vida |

---

## Etapa 5 — Cadastrar candidatos (opções)

### Pergunta 1 (Aliança) — candidatos por missão

Acesse a pergunta 1 > **Opções > Nova Opção**

**Tatuí:**
- João Silva (missão: Tatuí)
- Maria Souza (missão: Tatuí)

**Fortaleza:**
- Carlos Lima (missão: Fortaleza)
- Ana Paula (missão: Fortaleza)

### Pergunta 2 (Vida) — candidatos nacionais

Acesse a pergunta 2 > **Opções > Nova Opção**

> Para Realidade de Vida, o campo missão não aparece — os candidatos são nacionais.

- Roberto Alves
- Fernanda Costa

---

## Etapa 6 — Configurar membros por missão

Ainda dentro da eleição, edite cada missão e defina:

**Tatuí:**
- Eleitores aptos: 10
- Votarão presencialmente: 3
- Votarão remotamente: 3

**Fortaleza:**
- Eleitores aptos: 8
- Votarão presencialmente: 2
- Votarão remotamente: 2

> O total (presencial + remoto) representa os membros que irão comparecer/votar.

---

## Etapa 7 — Abrir a votação em Tatuí

Faça login como **Responsável Tatuí** (`resp.tatui@teste.com`).

1. O painel exibe a eleição com status **Aguardando**
2. Clique em **Abrir Votação**
3. Confirme digitando sua senha (`123456`)
4. Status muda para **Em andamento**

Repita o processo logando como **Responsável Fortaleza** para abrir em Fortaleza também.

---

## Etapa 8 — Testar votação REMOTA (tokens)

Faça login como **Mesário Tatuí** (`mesario.tatui@teste.com`).

1. Acesse **Painel do Mesário** → clique na eleição
2. Tela de presenças abre
3. Clique em **Registrar Presença**
4. Digite um nome (ex: "Eleitor Remoto 1") e salve
5. O sistema exibe o **token gerado** — anote-o (ex: `ABCDE-12345-FGHIJ`)
6. Repita para gerar mais 2 tokens

Agora, **sem fazer login**, acesse em uma aba anônima:

```
http://localhost:8000/votar
```

1. Digite o token gerado
2. A tela de votação abre com as perguntas
3. Selecione 1 candidato em cada pergunta
4. Clique em **Confirmar Voto**
5. Tela de confirmação: *"Seu voto foi registrado com sucesso."*

> Tente usar o mesmo token novamente → o sistema deve bloquear com mensagem de token já utilizado.

---

## Etapa 9 — Testar votação PRESENCIAL (máquina)

Faça login como **Máquina Tatuí 1** (`maquina1@teste.com`).

1. A tela de votação abre diretamente
2. Há um campo para **senha de liberação**
3. Digite a senha de liberação presencial (configurada pelo mesário/responsável)
4. A tela de votação abre
5. Selecione os candidatos e confirme
6. Voto registrado — a tela volta para a senha (pronta para o próximo eleitor)

---

## Etapa 10 — Verificar indicadores no painel

Faça login como **Responsável Tatuí**.

No painel, a eleição deve mostrar:
- **Membros**: total configurado
- **Votos**: quantidade de votos já registrados
- **Participação**: percentual calculado automaticamente

---

## Etapa 11 — Encerrar a votação

### Tatuí
Faça login como **Responsável Tatuí**.

1. Clique em **Encerrar Votação**
2. Digite a senha (`123456`)
3. Preencha a justificativa (ex: *"Todos os membros presentes já votaram."*)
4. Confirme

### Fortaleza
Repita o processo como **Responsável Fortaleza**.

> Somente após **ambas as missões** encerrarem, a eleição passa para o status **Encerrada** e os resultados são liberados.

---

## Etapa 12 — Visualizar resultados

### Como Responsável Local
Faça login como **Responsável Tatuí**.

1. No painel, o botão **Ver Resultados** aparece
2. Acesse e veja:
   - Indicadores de aderência e aproveitamento da missão
   - Placar da pergunta de Aliança (candidatos de Tatuí)
   - Placar da pergunta de Vida (candidatos nacionais)
   - Auditoria por máquina de votação (se houve votos presenciais)
3. Clique em **Imprimir Ata** para ver a ata da missão

### Como Administrador
Faça login como **admin@teste.com**.

1. Acesse **Admin > Eleições** → clique na eleição
2. Clique em **Ver Resultados**
3. Veja:
   - Card nacional com totais consolidados
   - Card por missão com indicadores individuais
   - Perguntas de Aliança: placar separado por missão
   - Perguntas de Vida: placar nacional consolidado
   - Auditoria completa de todas as máquinas
4. Clique em **Imprimir Ata** para a ata nacional

---

## Etapa 13 — Verificar logs de auditoria

Faça login como admin e acesse:

**Admin > Eleições > (clique na eleição) > Logs**

Você deve ver eventos como:
- `eleicao_criada`
- `votacao_aberta` (para Tatuí e Fortaleza)
- `votacao_encerrada` (com justificativa)
- `membros_alterados` (se alterou durante o processo)

---

## Checklist de validação

Ao final do teste, confirme:

- [ ] Eleição criada e configurada com perguntas e candidatos
- [ ] Votação aberta pelo responsável com senha
- [ ] Tokens gerados pelo mesário
- [ ] Votação remota funcionando em `/votar` sem login
- [ ] Token reutilizado foi bloqueado
- [ ] Votação presencial funcionando na máquina
- [ ] Indicadores de participação corretos no painel
- [ ] Votação encerrada pelo responsável com justificativa
- [ ] Resultados liberados após encerramento
- [ ] Aderência e aproveitamento calculados corretamente
- [ ] Placar de Aliança separado por missão
- [ ] Placar de Vida consolidado nacional
- [ ] Ata gerada com dados corretos
- [ ] Logs registrando todas as ações

---

## Dicas de diagnóstico

**"Resultados disponíveis apenas após o encerramento"**
→ A eleição ainda não está encerrada. Todas as missões precisam encerrar.

**"Token inválido ou já utilizado"**
→ O token foi usado ou não existe. Gere um novo pelo mesário.

**"Limite de votantes remotos atingido"**
→ Aumente a quantidade em "Votarão remotamente" no painel do responsável (exige justificativa).

**"Votação não está aberta"**
→ O responsável ainda não abriu a votação para essa missão.

**"Seu acesso expirou"**
→ O prazo de acesso do usuário venceu. O admin precisa editar o usuário e atualizar a data.
