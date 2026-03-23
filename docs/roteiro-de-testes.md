# Assembleia Digital — Roteiro de Teste do Zero

> Este roteiro cobre um teste completo: configuração da eleição, votação de Aliança e Vida, presencial e remota, encerramento, reabertura (admin) e apuração de resultados.

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

No painel do responsável ou **Admin > Eleições > missão > Alterar Membros**:

**Tatuí:**
- Eleitores aptos: 10
- Membros Vida (remoto): 2
- Votarão presencialmente (aliança): 2
- Votarão remotamente (aliança): 2

**Fortaleza:**
- Eleitores aptos: 8
- Membros Vida (remoto): 2
- Votarão presencialmente (aliança): 1
- Votarão remotamente (aliança): 2

> **Vida** representa os membros desta missão que votarão nas perguntas de Vida.
> **Aliança** (presencial + remoto) representa os membros que votarão nas perguntas de Aliança.

---

## Etapa 7 — Abrir as votações

### 7a — Abrir Votação Aliança em Tatuí

Faça login como **Responsável Tatuí** (`resp.tatui@teste.com`).

1. No painel, localize a eleição → bloco **Realidade de Aliança**
2. Clique em **Abrir Aliança**
3. Confirme com sua senha (`123456`)
4. Status do bloco muda para **Aberta**

Repita logando como **Responsável Fortaleza** para abrir aliança em Fortaleza.

### 7b — Abrir Votação Vida (global)

Faça login como qualquer **Responsável** (ou admin).

1. No painel, localize a eleição → bloco **Realidade de Vida**
2. Clique em **Abrir Vida**
3. Confirme com sua senha
4. Status do bloco muda para **Aberta** — válido para **todas as missões**

> ✅ Verificar: após a abertura, a eleição muda para status **Em andamento**.

---

## Etapa 8 — Registrar presenças e gerar tokens

Faça login como **Mesário Tatuí** (`mesario.tatui@teste.com`).

1. Acesse **Painel do Mesário** → clique na eleição
2. A tela de presenças abre

### Tokens de Aliança

1. Selecione o tipo **Realidade de Aliança**
2. Digite o nome do membro (ex: "Eleitor Aliança 1") e clique **Registrar**
3. O sistema exibe o **token** — anote-o
4. Repita para gerar mais 1 token de aliança

### Tokens de Vida

1. Selecione o tipo **Realidade de Vida**
2. Digite o nome (ex: "Eleitor Vida 1") e clique **Registrar**
3. Anote o token gerado
4. Repita para gerar mais 1 token de vida

> ✅ Verificar: a lista de presenças mostra badge **Aliança** ou **Vida** para cada membro.

---

## Etapa 9 — Testar votação REMOTA (tokens)

Sem fazer login, acesse em uma aba anônima:

```
http://localhost:8000/votar
```

### Votação com token de Aliança

1. Digite um dos tokens de aliança gerados
2. A tela de votação abre **apenas com a pergunta de Aliança** (representante da missão)
3. Selecione um candidato e confirme
4. Tela de confirmação aparece

### Votação com token de Vida

1. Digite um dos tokens de vida gerados
2. A tela de votação abre **apenas com a pergunta de Vida** (coordenador nacional)
3. Selecione um candidato e confirme

> ✅ Verificar: token de aliança não mostra pergunta de vida, e vice-versa.
>
> ✅ Verificar: tentar reutilizar token → mensagem de token já utilizado.

---

## Etapa 10 — Testar votação PRESENCIAL (máquina)

Faça login como **Máquina Tatuí 1** (`maquina1@teste.com`).

### Voto Presencial Aliança

1. A tela da máquina exibe opções de tipo de votante
2. Selecione **Realidade de Aliança**
3. Digite a senha da máquina (`123456`)
4. A tela de votação abre com a pergunta de aliança
5. Selecione candidato e confirme
6. Tela retorna para a senha (pronta para o próximo eleitor)

### Voto Presencial Vida

1. Na mesma máquina, selecione **Realidade de Vida**
2. Digite a senha (`123456`)
3. A tela de votação abre com a pergunta de vida
4. Selecione candidato e confirme

> ✅ Verificar: cada escopo mostra apenas suas próprias perguntas.

---

## Etapa 11 — Verificar indicadores no painel

Faça login como **Responsável Tatuí**.

No painel, o bloco Aliança da eleição deve mostrar:
- **Vida (limite)**: 2
- **Aliança (membros)**: 4 (2 presencial + 2 remoto)
- **Votos Aliança**: quantidade registrada
- **Participação**: percentual calculado

---

## Etapa 12 — Encerrar as votações

### Encerrar Aliança

**Tatuí**: Login como Responsável Tatuí → Encerrar Aliança → senha + justificativa.

**Fortaleza**: Repita como Responsável Fortaleza.

### Encerrar Vida

Login como qualquer Responsável (ou admin) → Encerrar Vida → senha + justificativa.

> ✅ Verificar: a eleição passa para **Encerrada** apenas quando **todas** as aliança e a vida estão fechadas.

---

## Etapa 13 — Testar reabertura (admin)

> Simula o cenário: responsável encerrou a votação por engano antes de todos votarem.

Faça login como **admin**.

### Reabrir Aliança de Tatuí

1. No painel, localize o bloco Aliança de Tatuí
2. Clique em **Reabrir Aliança** (botão amarelo, visível só para admin)
3. Preencha o motivo (ex: *"Encerramento antecipado por engano — 1 eleitor ainda não havia votado."*)
4. Confirme com senha (`123456`)
5. A votação aliança de Tatuí volta ao status **Aberta**
6. A eleição volta ao status **Em andamento**

### Reabrir Vida

1. Localize o bloco Realidade de Vida
2. Clique em **Reabrir Vida**
3. Preencha o motivo e confirme com senha
4. Votação Vida volta a **Aberta**

> ✅ Verificar: votos registrados anteriormente **permanecem intactos**.
>
> ✅ Verificar: o motivo aparece nos logs da eleição.

Após verificar, encerre novamente para liberar os resultados.

---

## Etapa 14 — Visualizar resultados

### Como Responsável Local

Faça login como **Responsável Tatuí**.

1. No painel, o botão **Ver Resultados** aparece no bloco aliança
2. Acesse e veja:
   - Indicadores de aderência e aproveitamento da missão
   - Placar da pergunta de Aliança (candidatos de Tatuí)
   - Placar da pergunta de Vida (candidatos nacionais)
   - Auditoria por máquina (votos presenciais)
3. Clique em **Imprimir Ata** para ver a ata da missão

### Como Administrador

Faça login como **admin@teste.com**.

1. Acesse **Admin > Eleições** → clique na eleição
2. Clique em **Ver Resultados**
3. Veja:
   - Totais consolidados nacionais
   - Perguntas de Aliança: placar separado por missão
   - Pergunta de Vida: placar nacional consolidado
   - Auditoria completa de todas as máquinas
4. Clique em **Imprimir Ata** para a ata nacional

---

## Etapa 15 — Verificar logs de auditoria

Faça login como admin e acesse:

**Admin > Eleições > (clique na eleição) > Logs**

Você deve ver eventos como:

| Evento esperado |
|----------------|
| `votacao_aberta` — aliança Tatuí e Fortaleza |
| `vida_aberta` |
| `votacao_encerrada` — com justificativa |
| `vida_encerrada` — com justificativa |
| `votacao_reaberta` — com motivo do admin |
| `vida_reaberta` — com motivo do admin |
| `membros_alterados` — se alterou durante o processo |

---

## Checklist de validação

Ao final do teste, confirme:

- [ ] Eleição criada com perguntas de escopo aliança e vida
- [ ] Membros configurados com qtd_vida separado de aliança
- [ ] Votação aliança aberta por cidade com senha
- [ ] Votação vida aberta globalmente com senha
- [ ] Tokens de aliança gerados com badge correto
- [ ] Tokens de vida gerados com badge correto
- [ ] Votação remota aliança mostra apenas perguntas de aliança
- [ ] Votação remota vida mostra apenas perguntas de vida
- [ ] Votação presencial seleciona escopo antes de liberar
- [ ] Token reutilizado foi bloqueado
- [ ] Indicadores de participação corretos no painel
- [ ] Encerramento parcial não libera resultados
- [ ] Eleição encerra apenas quando aliança (todas) + vida estão fechadas
- [ ] Admin consegue reabrir aliança com senha + justificativa
- [ ] Admin consegue reabrir vida com senha + justificativa
- [ ] Votos anteriores intactos após reabertura
- [ ] Reabertura registrada no log com motivo
- [ ] Resultados liberados após encerramento total
- [ ] Placar de Aliança separado por missão
- [ ] Placar de Vida consolidado nacional
- [ ] Ata gerada com auditoria (abertura/encerramento/responsável)
- [ ] Logs registrando todas as ações com justificativas

---

## Dicas de diagnóstico

**"Resultados disponíveis apenas após o encerramento"**
→ A eleição ainda não está totalmente encerrada. Todas as missões aliança + vida precisam encerrar.

**"Token inválido ou já utilizado"**
→ O token foi usado ou não existe. Gere um novo pelo mesário.

**"A votação Realidade de Vida não está aberta no momento"**
→ O responsável ainda não abriu a votação vida. Acesse o painel e clique em "Abrir Vida".

**"Limite de votantes remotos aliança atingido"**
→ Aumente `qtd_remoto` em "Alterar Membros" (exige justificativa se já aberta).

**"Limite de votantes remotos vida atingido"**
→ Aumente `qtd_vida` em "Alterar Membros".

**"Votação não está aberta"**
→ O responsável ainda não abriu a votação aliança para essa missão.

**"Seu acesso expirou"**
→ O prazo de acesso do usuário venceu. O admin precisa editar o usuário e atualizar a data.

**Botão "Reabrir" não aparece**
→ O botão é visível apenas para usuários com perfil **admin**. Responsável local não tem essa opção.
