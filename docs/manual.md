# Assembleia Digital — Manual do Sistema

> Versão atual · Laravel 10 · Bootstrap 5

---

## O que é o Assembleia Digital?

O **Assembleia Digital** é um sistema web de votação desenvolvido para comunidades religiosas que precisam conduzir eleições internas com segurança, controle e sigilo. Ele permite realizar votações **presenciais** (em máquinas físicas no local) e **remotas** (via link com token enviado ao eleitor), com suporte a múltiplas missões participando simultaneamente.

### Princípios que guiam o sistema

| Princípio | Como é garantido |
|-----------|-----------------|
| **Anonimato** | O voto nunca é associado ao nome do eleitor. O token é convertido em hash antes de salvar. |
| **Unicidade** | Cada token só pode ser usado uma vez. Tentativas repetidas são bloqueadas. |
| **Sigilo** | Resultados ficam bloqueados até o encerramento oficial da votação. |
| **Auditoria** | Toda ação relevante (abertura, encerramento, alteração de membros) gera um log rastreável. |
| **Controle local** | Cada missão tem seu próprio responsável, que abre e encerra a votação de forma independente. |

---

## Estrutura do sistema

### Missões

Uma **missão** é a unidade territorial do sistema (equivale a uma cidade ou unidade da comunidade). Cada eleição pode envolver uma ou mais missões. As missões são cadastradas pelo administrador e, por padrão, o sistema já vem com **Tatuí** e **Fortaleza** pré-configuradas.

### Eleições

Uma **eleição** é o evento de votação. Ela é composta por:

- **Título** e **data**
- **Missões participantes**
- **Perguntas** (as questões votadas)
- **Opções** (os candidatos ou alternativas de cada pergunta)

Uma eleição passa pelos seguintes estados:

```
Rascunho → Aberta → Encerrada
```

- **Rascunho**: em configuração pelo admin. Ninguém vota ainda.
- **Aberta**: pelo menos uma missão iniciou a votação.
- **Encerrada**: todas as missões encerraram. Resultados liberados.

### Perguntas e escopo

Cada pergunta tem um **escopo** que define como os candidatos são organizados:

| Escopo | Nome | Comportamento |
|--------|------|---------------|
| `alianca` | Realidade de Aliança | Cada missão tem seus próprios candidatos. Um eleitor de Tatuí vê apenas os candidatos de Tatuí. |
| `vida` | Realidade de Vida | Lista única nacional. Todos os eleitores, de todas as missões, votam nos mesmos candidatos. |

Cada pergunta também define a **quantidade obrigatória de respostas**. O eleitor só consegue confirmar o voto se selecionar exatamente o número exigido.

---

## Perfis de usuário

O sistema possui quatro perfis com **hierarquia de acesso**:

```
Administrador
    └── acessa tudo de todas as missões

Responsável Local
    └── acessa funções de responsável + funções de mesário
    └── limitado à sua missão

Mesário
    └── acessa apenas funções de mesário
    └── limitado à sua missão

Máquina de Votação
    └── acesso exclusivo à tela de votação presencial
    └── isolado — não acessa outras funcionalidades
```

> O administrador pode acessar o painel de qualquer missão sem restrição de cidade.

### Expiração de acesso

Ao criar ou editar um usuário, o administrador pode definir uma **data e hora de expiração**. Após esse prazo, o sistema desloga o usuário automaticamente na próxima ação e exibe a mensagem:

> *"Seu acesso expirou em DD/MM/AAAA às HH:MM. Entre em contato com o administrador."*

Se deixado em branco, o acesso é ilimitado.

---

## Funcionalidades por perfil

### Administrador

#### Missões
- Cadastrar, editar e remover missões
- As missões Tatuí e Fortaleza são criadas automaticamente na instalação

#### Eleições
- Criar eleição com título, data e missões participantes
- Definir perguntas (com escopo aliança ou vida) e candidatos
- Visualizar logs de auditoria de cada eleição
- Ver resultados e imprimir ata após encerramento
- Acessar resultados de qualquer missão

#### Usuários
- Criar usuários com perfil, missão e prazo de acesso
- Editar e remover usuários
- Visualizar na listagem se o acesso está ativo, expirado ou ilimitado

---

### Responsável Local

O responsável gerencia a votação da **sua missão**. O administrador tem acesso às funções de responsável de todas as missões.

#### Painel
Exibe todas as eleições disponíveis para a missão, com status (Aguardando / Em andamento / Finalizada) e indicadores de participação.

#### Alterar membros
Antes ou durante a votação (enquanto não encerrada), o responsável pode ajustar:
- **Eleitores aptos**: total do eleitorado (usado para calcular aderência)
- **Votarão presencialmente**: slots liberados nas máquinas
- **Votarão remotamente**: limite de tokens a serem gerados

> Qualquer alteração nessas quantidades **exige justificativa**, que fica registrada no log.

#### Abrir votação
Confirma com a **senha pessoal** do responsável. Registra data/hora e usuário responsável no log.

#### Encerrar votação
Confirma com **senha** + **justificativa obrigatória** (mínimo 10 caracteres). A eleição só muda para o status "encerrada" quando **todas as missões** tiverem encerrado.

#### Resultados
Disponíveis após encerramento da votação da missão. Exibe:
- Indicadores de aderência e aproveitamento
- Placar por pergunta (candidatos da missão para aliança; placar nacional para vida)
- Auditoria de votos por máquina presencial
- Opção de imprimir ata

---

### Mesário

O mesário gerencia o **registro de presença** e a **geração de tokens** para votação remota.

#### Registrar presença individual
- Informa o nome do membro
- O sistema gera automaticamente um token de votação (ex: `ABCDE-12345-FGHIJ`)
- A tela seguinte exibe o token para ser anotado ou enviado ao eleitor

#### Importar lista (CSV)
- Upload de arquivo `.csv` com uma coluna `nome`
- O sistema gera um token para cada linha
- Exibe a lista completa com todos os tokens gerados

> O limite de tokens gerados respeita a quantidade configurada em "votarão remotamente". Se o limite for atingido, nenhum novo token é gerado.

#### Acompanhar presenças
- Lista todos os membros registrados
- Indica quantos já votaram e quantos tokens foram gerados

---

### Máquina de Votação

Perfil usado em computadores ou tablets no local da votação presencial. Após o login, a tela de votação é aberta diretamente.

#### Liberar voto presencial
- O operador digita a **senha da máquina** (liberada pelo mesário ou responsável)
- A tela de votação abre para o eleitor
- Após confirmar, o voto é registrado e a tela volta para a senha

#### Votação por token (presencial)
- Também é possível usar um token na máquina (mesma tela)
- Útil quando o eleitor está presente mas votará por token

---

## Votação remota (sem login)

Eleitores remotos acessam **`/votar`** — página pública, sem necessidade de criar conta.

Fluxo:
1. Eleitor acessa `/votar` e digita o token recebido
2. O sistema valida o token (verifica se é válido, não usado, e se a votação está aberta)
3. A tela de votação é exibida com as perguntas e candidatos
4. Eleitor seleciona as opções e confirma
5. Tela de confirmação é exibida. O token é marcado como usado.

> Tokens usados não podem ser reaproveitados. Tentativas de reuso são bloqueadas.

---

## Indicadores de participação

O sistema calcula dois indicadores para cada missão:

| Indicador | Fórmula | Significado |
|-----------|---------|-------------|
| **Aderência** | Compareceram ÷ Eleitores aptos | Quantos do eleitorado total apareceram |
| **Aproveitamento** | Votaram ÷ Compareceram | Dos que apareceram, quantos efetivamente votaram |

Esses indicadores aparecem nos painéis de resultados (responsável e admin) e na ata.

---

## Resultados e ata

### Resultados (admin)
- Card nacional com totais consolidados de todas as missões
- Cards por missão com indicadores individuais
- Placar por pergunta: Aliança mostra por missão; Vida mostra placar consolidado nacional
- Tabela de auditoria por máquina de votação (total de votos presenciais por dispositivo)

### Resultados (responsável)
- Resumo da missão (aptos / compareceram / votaram)
- Barras de aderência e aproveitamento
- Placar por pergunta da missão
- Para perguntas de Vida: placar nacional + tabela de participação por missão
- Auditoria das máquinas desta missão

### Ata
Gerada automaticamente após encerramento. Contém:
- Dados da eleição e da missão
- Tabela de participação (aptos, compareceram, votaram, aderência, aproveitamento)
- Resultado de cada pergunta com votação detalhada

---

## Logs de auditoria

Cada eleição possui um histórico de eventos acessível pelo administrador em **Admin > Eleições > Logs**. São registrados automaticamente:

| Evento | Quando ocorre |
|--------|--------------|
| `eleicao_criada` | Ao criar a eleição |
| `votacao_aberta` | Ao abrir a votação em uma missão |
| `votacao_encerrada` | Ao encerrar, com justificativa |
| `membros_alterados` | Ao alterar quantidades, com justificativa |

---

## Segurança

- **Autenticação obrigatória** para todas as funcionalidades internas
- **CSRF** em todos os formulários
- **Tokens de votação** são hashes SHA-256 — o token original não é armazenado
- **Votos são anônimos** — não existe forma de associar um voto a um eleitor
- **Resultados bloqueados** até encerramento oficial
- **Expiração de acesso** por data/hora configurável por usuário
- **Validação de cidade** nas opções de voto — eleitor nunca vê candidatos de outra missão

---

## Requisitos técnicos

| Item | Requisito |
|------|-----------|
| PHP | 8.1 ou superior |
| Framework | Laravel 10 |
| Banco de dados | MySQL 8 |
| Navegador | Chrome, Firefox, Edge (versões modernas) |
| Hospedagem | Qualquer servidor com suporte a PHP/Laravel |
