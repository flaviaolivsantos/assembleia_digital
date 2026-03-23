# PRD — Assembleia Digital

## 1. Visão Geral

**Assembleia Digital** é um sistema web de votação desenvolvido para apoiar processos eleitorais internos de uma comunidade religiosa.

O sistema permite realizar votações presenciais e online de forma:

* segura
* auditável
* anônima
* controlada

A votação ocorre simultaneamente em **múltiplas cidades (missões)**, com dois escopos distintos:

| Escopo | Candidatos | Abertura |
|--------|-----------|----------|
| **Realidade de Aliança** | Por missão — candidatos específicos de cada cidade | Por cidade, controlada pelo Responsável Local |
| **Realidade de Vida** | Nacionais — mesma lista para todas as cidades | Única abertura global, válida para todas as missões |

O sistema garante:

* anonimato dos votos
* controle de presença por escopo (vida ou aliança)
* controle de abertura e encerramento por escopo
* impossibilidade de visualizar resultados antes do encerramento
* capacidade de reabertura administrativa com justificativa registrada

---

# 2. Objetivos do Sistema

### Objetivo principal

Permitir a realização de eleições comunitárias com controle e sigilo do voto, separando a votação da Realidade de Vida (escopo nacional) da Realidade de Aliança (escopo por cidade).

### Objetivos específicos

* registrar presença de eleitores com identificação de escopo
* garantir que cada membro vote apenas uma vez no seu escopo
* garantir anonimato do voto
* permitir votação presencial e online
* permitir controle local por cidade (aliança) e controle nacional (vida)
* gerar resultados apenas após encerramento
* gerar ata da eleição automaticamente
* permitir reabertura administrativa em caso de erro operacional

---

# 3. Tecnologias

**Backend**
* PHP 8.2
* Laravel 10

**Frontend**
* Blade + Bootstrap 5
* Bootstrap Icons
* Chart.js 4 (gráficos de resultados)

**Banco de dados**
* MySQL

**Hospedagem**
* Railway (auto-deploy via GitHub)

**Sessões**
* Driver `database` — persistência garantida mesmo em ambientes de filesystem efêmero

**Ferramentas de desenvolvimento**
* VS Code + Claude Code
* Git / GitHub

---

# 4. Perfis de Usuário

O sistema possui quatro tipos de usuário, todos com campo `acesso_ate` para expiração de acesso.

## 4.1 Administrador

Responsável pela configuração da eleição e supervisão geral.

Permissões:

* criar e editar eleições
* cadastrar cidades
* cadastrar perguntas (com escopo: vida ou aliança)
* cadastrar opções de voto
* gerenciar usuários
* definir quantidade de membros por missão (vida + aliança)
* visualizar resultados após encerramento
* visualizar ata da eleição
* visualizar logs de auditoria
* **reabrir votação aliança de uma missão** (com senha + justificativa obrigatória)
* **reabrir votação Realidade de Vida** (com senha + justificativa obrigatória)
* editar membros mesmo após encerramento (para correção antes de reabertura)

---

## 4.2 Responsável Local

Responsável pela condução da votação em sua cidade.

Permissões:

* abrir votação aliança da sua cidade (com senha)
* encerrar votação aliança da sua cidade (com senha + justificativa)
* **abrir votação Realidade de Vida** (abertura única, válida para todas as missões)
* **encerrar votação Realidade de Vida** (com senha + justificativa)
* alterar quantidade de votantes (presencial + remoto aliança e vida)
* visualizar resultados após encerramento

A abertura e encerramento exigem **senha de confirmação**.

---

## 4.3 Mesário

Responsável por acompanhar o fluxo de presença.

Permissões:

* registrar presença de membros (com seleção de escopo: **Aliança** ou **Vida**)
* importar lista de membros via CSV (com escopo)
* acompanhar quantidade de votos registrados
* visualizar quantos membros ainda faltam votar

Restrições:

* não pode visualizar votos
* não pode visualizar resultados
* não pode abrir ou encerrar votação

---

## 4.4 Máquina de Votação

Perfil para dispositivos de votação presencial.

Permissões:

* selecionar escopo do eleitor (**Aliança** ou **Vida**) antes de liberar voto
* liberar sessão de votação com senha
* registrar votos presenciais

Restrições:

* não pode visualizar resultados
* não pode visualizar histórico de votos

---

# 5. Estrutura da Eleição

Uma eleição possui:

* título
* data
* status: `rascunho` → `aberta` → `encerrada`
* cidades participantes (com configuração individual de membros)
* perguntas (cada uma com escopo definido)
* opções de resposta (nacionais para vida, por cidade para aliança)

Campos de controle vida na eleição:

* `aberta_vida` — se a votação vida está aberta
* `data_abertura_vida` / `data_encerramento_vida`
* `aberta_por_vida` / `encerrada_por_vida`

---

# 6. Estrutura de Perguntas

Cada pergunta possui:

* texto da pergunta
* quantidade obrigatória de respostas
* **escopo**: `alianca` ou `vida`

O escopo determina:
* quais eleitores veem a pergunta (apenas quem tem token/sessão do mesmo escopo)
* como as opções são filtradas (por cidade para aliança, nacionais para vida)

---

# 7. Opções de Voto

As opções de voto para **Realidade de Aliança** são específicas por cidade.

As opções de voto para **Realidade de Vida** são nacionais — todos os eleitores veem os mesmos candidatos, independente da missão.

---

# 8. Fluxo da Eleição

## Configuração (Administrador)

1. Cria a eleição (título + data)
2. Adiciona cidades participantes
3. Cria perguntas com escopo (aliança ou vida)
4. Cadastra opções (por cidade para aliança; nacionais para vida)
5. Define membros por cidade: `qtd_vida`, `qtd_presencial` (aliança), `qtd_remoto` (aliança), `qtd_eleitorado`

---

## Abertura da Votação

**Aliança** (por cidade, controlada pelo Responsável Local ou Admin):
* Abre individualmente para cada missão
* Registra `data_abertura`, `aberta_por`

**Vida** (global, controlada por qualquer Responsável ou Admin):
* Abertura única para todas as cidades simultaneamente
* Registra `data_abertura_vida`, `aberta_por_vida`

A eleição muda para status `aberta` na primeira abertura (aliança ou vida).

---

## Registro de Presença

O mesário seleciona o **escopo** (Aliança ou Vida) antes de registrar o membro.

O sistema:
1. Valida se a votação do escopo está aberta
2. Verifica limite de tokens para o escopo
3. Gera token com escopo marcado
4. Cria presença com escopo marcado

---

## Processo de Votação

```
Mesário seleciona escopo + registra presença
↓
Sistema gera token com escopo
↓
Eleitor inicia votação (token remoto ou senha presencial)
↓
Sistema filtra perguntas pelo escopo do eleitor
↓
Eleitor responde apenas as perguntas do seu escopo
↓
Voto registrado anonimamente
```

---

## Encerramento

**Aliança**: por cidade, com senha + justificativa.
**Vida**: global, com senha + justificativa.

A eleição muda para `encerrada` quando **todas as cidades aliança estão fechadas E a vida está fechada**.

---

## Reabertura (Admin only)

Em caso de erro operacional, o administrador pode reabrir:
* **Aliança** de uma cidade específica
* **Vida** globalmente

Requisitos obrigatórios: senha do admin + justificativa detalhada (mínimo 10 caracteres). Tudo registrado em log.

Os votos já registrados **não são alterados** pela reabertura.

---

# 9. Sistema de Anonimato

O voto é **totalmente anônimo**.

Regras:
* o voto não pode ser associado ao nome do eleitor
* a lista de presença não revela ordem de votação
* o voto é armazenado com token hash

Processo:
1. sistema gera token aleatório de 6 dígitos
2. token é entregue ao eleitor pelo mesário
3. eleitor usa o token para votar
4. token é convertido em SHA-256 antes de salvar
5. token original não é armazenado após votação

---

# 10. Controle de Presença

O sistema suporta dois modos de registro:

## Modo individual
Mesário registra cada membro pelo nome e seleciona o escopo. Token gerado e exibido na tela para envio por WhatsApp.

## Modo lista (CSV)
Mesário importa arquivo `.csv` com nomes, selecionando o escopo do lote. Tokens são gerados em massa.

Cada token carrega o escopo (`vida` ou `alianca`), determinando quais perguntas o eleitor verá.

---

# 11. Encerramento da Votação

## Encerramento automático (Aliança)

Quando `votos_registrados >= qtd_membros` na cidade. A votação vida nunca fecha automaticamente.

## Encerramento manual

Responsável encerra com senha + justificativa.

A eleição passa para `encerrada` somente quando ambos os encerramentos (todas as cidades aliança + vida) ocorreram.

---

# 12. Resultados

Resultados disponíveis apenas após encerramento da votação.

O sistema exibe:
* total de votos por opção
* total de votantes vs total de membros
* aderência (votantes / eleitores aptos)
* aproveitamento (votos / presenças registradas)
* resultados de Aliança separados por missão
* resultado de Vida consolidado nacional
* auditoria por máquina de votação

---

# 13. Ata da Eleição

Após encerramento, o sistema gera ata contendo:

* nome da eleição e data
* cidade (ou "Nacional" para vida)
* horário de abertura e encerramento
* responsável pela abertura e encerramento
* número de membros esperados vs realizados
* resultados finais por pergunta

Exportação via impressão do navegador (`window.print()`) com layout A4 otimizado para "Salvar como PDF".

---

# 14. Segurança

* autenticação obrigatória (exceto votação remota pública em `/votar`)
* proteção CSRF em todos os formulários
* validação de cidade nas opções de voto (aliança)
* bloqueio de resultados antes do encerramento
* tokens de votação de uso único (SHA-256)
* sessões persistidas em banco de dados (Railway-safe)
* reabertura exige senha + justificativa + log imutável

---

# 15. Logs de Auditoria

O sistema registra logs das seguintes ações:

| Evento | Descrição |
|--------|-----------|
| `eleicao_criada` | Eleição cadastrada |
| `votacao_aberta` | Votação aliança aberta em cidade |
| `votacao_encerrada` | Votação aliança encerrada em cidade (com justificativa) |
| `votacao_reaberta` | Votação aliança reaberta pelo admin (com motivo) |
| `vida_aberta` | Votação Realidade de Vida aberta |
| `vida_encerrada` | Votação Realidade de Vida encerrada (com justificativa) |
| `vida_reaberta` | Votação Realidade de Vida reaberta pelo admin (com motivo) |
| `membros_alterados` | Quantidade de membros alterada (com justificativa) |

Cada log registra: usuário, data/hora, descrição da ação.

---

# 16. Estrutura de Módulos

1. Autenticação e controle de acesso (com expiração)
2. Gestão de usuários
3. Gestão de cidades
4. Gestão de eleições
5. Gestão de perguntas (com escopo)
6. Gestão de opções de voto
7. Controle de presença (com escopo)
8. Geração de token de votação (com escopo)
9. Registro de votos (filtrado por escopo)
10. Abertura/encerramento aliança por cidade
11. Abertura/encerramento vida (global)
12. Reabertura administrativa
13. Resultados e ata
14. Logs de auditoria

---

# 17. Banco de Dados — Principais Entidades

| Tabela | Campos relevantes |
|--------|------------------|
| `users` | perfil, cidade_id, acesso_ate, remember_token |
| `cidades` | nome |
| `eleicaos` | titulo, data_eleicao, status, aberta_vida, data_abertura_vida, data_encerramento_vida, aberta_por_vida, encerrada_por_vida |
| `eleicao_cidades` | eleicao_id, cidade_id, qtd_eleitorado, qtd_vida, qtd_presencial, qtd_remoto, qtd_membros, votos_registrados, votos_presenciais, aberta, data_abertura, data_encerramento, aberta_por, encerrada_por |
| `perguntas` | eleicao_id, pergunta, qtd_respostas, **escopo** ('vida'\|'alianca'), ordem |
| `opcoes` | pergunta_id, nome, cidade_id (null para vida) |
| `presencas` | eleicao_id, cidade_id, nome, token, votou, **escopo** |
| `token_votacaos` | token_hash, eleicao_id, cidade_id, usado, **escopo** |
| `votos` | token_hash, pergunta_id, opcao_id, origem, maquina_id |
| `sessions` | id, user_id, payload, last_activity |
| `log_eleicaos` | eleicao_id, user_id, tipo, descricao |

---

# 18. Requisitos Não Funcionais

* funcionar em navegadores modernos (Chrome, Edge, Firefox, Safari)
* funcionar em dispositivos móveis
* suportar votação simultânea em múltiplas máquinas
* sessões persistentes em banco de dados (não filesystem)
* deploy automático via Railway ao fazer push no GitHub
* interface responsiva e intuitiva

---

# 19. Critérios de Aceitação

O sistema é considerado funcional quando:

* permite configuração completa da eleição com perguntas de escopo vida e aliança
* separa corretamente tokens e presences por escopo
* eleitor de aliança vê apenas perguntas de aliança; eleitor de vida vê apenas perguntas de vida
* votação aliança abre/fecha por cidade; votação vida abre/fecha globalmente
* resultados bloqueados antes do encerramento total
* admin pode reabrir votação com justificativa sem alterar votos já registrados
* todas as ações de abertura/encerramento/reabertura registradas em log com justificativa

---

# 20. Futuras Melhorias (não obrigatórias)

* QR Code para distribuição de tokens
* auditoria criptográfica (assinatura digital de votos)
* exportação PDF server-side (wkhtmltopdf / Puppeteer)
* autenticação em dois fatores para admin
* modo offline com sincronização posterior
* painel de acompanhamento em tempo real (WebSockets)
