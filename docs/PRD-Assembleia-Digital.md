# PRD — Assembleia Digital

## 1. Visão Geral

**Assembleia Digital** é um sistema web de votação desenvolvido para apoiar processos eleitorais internos de uma comunidade religiosa.

O sistema permitirá realizar votações presenciais e online de forma:

* segura
* auditável
* anônima
* controlada

A votação ocorrerá simultaneamente em **múltiplas cidades**, onde:

* as **perguntas são iguais**
* as **opções de voto são diferentes por cidade**

O sistema deve garantir:

* anonimato dos votos
* controle de presença
* controle de abertura e encerramento
* impossibilidade de visualizar resultados antes do encerramento

---

# 2. Objetivos do Sistema

### Objetivo principal

Permitir a realização de eleições comunitárias com controle e sigilo do voto.

### Objetivos específicos

* registrar presença de eleitores
* garantir que cada membro vote apenas uma vez
* garantir anonimato do voto
* permitir votação presencial e online
* permitir controle local por cidade
* gerar resultados apenas após encerramento
* gerar ata da eleição automaticamente

---

# 3. Tecnologias

Stack definido para o projeto:

Backend

* PHP
* Laravel

Frontend

* Blade
* Bootstrap

Banco de dados

* MySQL

Hospedagem

* Hostinger (plano básico)

Ferramentas de desenvolvimento

* VS Code
* Git

---

# 4. Perfis de Usuário

O sistema terá quatro tipos de usuário.

## 4.1 Administrador

Responsável pela configuração da eleição.

Permissões:

* criar eleições
* cadastrar cidades
* cadastrar perguntas
* cadastrar opções de voto
* importar lista de membros
* definir quantidade de votos obrigatórios
* visualizar resultados após encerramento
* gerar ata da eleição
* visualizar logs

---

## 4.2 Responsável Local

Responsável pela condução da eleição em uma cidade.

Permissões:

* abrir votação
* encerrar votação
* alterar quantidade de votantes
* encerrar votação antecipadamente com justificativa

A abertura e encerramento devem exigir **senha de confirmação**.

---

## 4.3 Mesário

Responsável por acompanhar o fluxo de votação.

Permissões:

* registrar presença de membros
* acompanhar quantidade de votos registrados
* visualizar quantos membros ainda faltam votar

Restrições:

* não pode visualizar votos
* não pode visualizar resultados

---

## 4.4 Máquina de Votação

Perfil utilizado em computadores ou dispositivos utilizados pelos eleitores.

Permissões:

* iniciar sessão de votação
* registrar votos

Restrições:

* não pode visualizar resultados
* não pode visualizar histórico de votos

---

# 5. Estrutura da Eleição

Uma eleição possui:

* título
* data
* cidades participantes
* perguntas
* opções de resposta por cidade

---

# 6. Estrutura de Perguntas

Cada pergunta possui:

* texto da pergunta
* quantidade obrigatória de respostas

Exemplo:

Pergunta:

```
Quem deve representar a cidade na assembleia?
```

Quantidade obrigatória:

```
3 respostas
```

O sistema deve impedir a finalização do voto se a quantidade de respostas selecionadas for diferente da quantidade definida.

---

# 7. Opções de Voto

As opções de voto são **específicas por cidade**.

Exemplo:

Pergunta:

```
Quem deve representar a cidade na assembleia?
```

Cidade A:

* João
* Pedro
* Maria
* Ana

Cidade B:

* Lucas
* Tiago
* Carla
* Renata

Eleitores de uma cidade **não podem visualizar opções de outra cidade**.

---

# 8. Fluxo da Eleição

## Antes da eleição

Administrador:

1. cria a eleição
2. cadastra cidades
3. cadastra perguntas
4. cadastra opções de voto
5. define quantidade de membros votantes
6. importa lista de presença (opcional)

---

## Início da votação

Responsável local realiza:

```
Abrir votação
```

O sistema registra:

* data de abertura
* usuário responsável

---

## Registro de presença

O mesário registra que um membro está presente.

O sistema:

1. gera um token de votação
2. entrega o token para a máquina de votação

---

## Processo de votação

Fluxo:

```
Mesário registra presença
↓
Sistema gera token
↓
Eleitor inicia votação
↓
Eleitor responde perguntas
↓
Sistema valida respostas
↓
Voto é registrado
```

---

# 9. Sistema de Anonimato

O voto deve ser **totalmente anônimo**.

Regras:

* o voto não pode ser associado ao nome do eleitor
* a lista de presença não pode revelar ordem de votação
* o voto deve ser armazenado com token anônimo

Processo:

1. sistema gera um token aleatório
2. token é utilizado durante a votação
3. token é convertido em hash antes de salvar

Exemplo:

```
token original:
XJ92K-AB22L-KLL91

hash armazenado:
a84b3c9d21e...
```

O token original não deve ser armazenado após a votação.

---

# 10. Controle de Presença

O sistema permite dois modos.

## Modo simples

Administrador informa apenas a quantidade de membros votantes.

Exemplo:

```
Total de membros: 40
```

---

## Modo lista

Administrador importa lista de membros via:

* CSV
* XLS

Tabela de presença registra:

* nome
* status de voto

Importante:

O voto não deve ser associado ao nome do membro.

---

# 11. Encerramento da Votação

A votação pode encerrar de duas formas.

## Encerramento automático

Quando:

```
total de votos = total de membros
```

---

## Encerramento manual

Responsável local pode encerrar votação antecipadamente.

Requisitos:

* senha de confirmação
* justificativa registrada

---

# 12. Resultados

Resultados só podem ser visualizados após o encerramento da votação.

O sistema deve mostrar:

* total de votos por opção
* total de votantes
* total de membros
* resultados por cidade

---

# 13. Ata da Eleição

Após encerramento, o sistema deve gerar automaticamente uma ata contendo:

* nome da eleição
* data
* cidade
* número de membros
* número de votos registrados
* resultados finais

Exportação:

* PDF
* Excel (opcional)

---

# 14. Segurança

Regras obrigatórias de segurança:

* autenticação obrigatória
* proteção CSRF
* validação de cidade nas opções de voto
* bloqueio de visualização de resultados antes do encerramento
* token de votação de uso único

---

# 15. Logs de Auditoria

O sistema deve registrar logs das seguintes ações:

* abertura da votação
* encerramento da votação
* alteração da quantidade de membros
* criação de eleição
* alteração de perguntas
* alteração de opções de voto

Cada log deve registrar:

* usuário
* data
* ação executada

---

# 16. Estrutura de Módulos

O sistema será dividido em módulos:

1. Autenticação
2. Gestão de usuários
3. Gestão de cidades
4. Gestão de eleições
5. Gestão de perguntas
6. Gestão de opções de voto
7. Controle de presença
8. Geração de token de votação
9. Registro de votos
10. Encerramento da eleição
11. Resultados
12. Logs
13. Geração de ata

---

# 17. Requisitos Não Funcionais

O sistema deve:

* funcionar em navegadores modernos
* funcionar em dispositivos móveis
* suportar votação simultânea em múltiplas máquinas
* possuir interface simples e intuitiva

---

# 18. Escalabilidade

O sistema deve suportar:

* múltiplas eleições futuras
* múltiplas cidades
* múltiplas perguntas por eleição

---

# 19. Interface Esperada

Principais telas:

Administrador

* dashboard
* cadastro de eleição
* cadastro de perguntas
* cadastro de opções
* importação de membros
* visualização de resultados

Responsável

* abrir votação
* encerrar votação

Mesário

* registrar presença
* acompanhar votos registrados

Máquina de votação

* iniciar votação
* responder perguntas

---

# 20. Estrutura inicial do banco de dados

Principais entidades:

* users
* cidades
* eleicoes
* eleicao_cidades
* perguntas
* opcoes
* presencas
* tokens_votacao
* votos
* logs_eleicao

---

# 21. Critérios de Aceitação

O sistema será considerado funcional quando:

* permitir cadastro de eleição
* permitir cadastro de perguntas
* permitir cadastro de opções por cidade
* permitir registrar presença
* permitir votação anônima
* impedir visualização de resultados antes do encerramento
* gerar resultados após encerramento
* registrar logs das ações principais

---

# 22. Futuras Melhorias (não obrigatórias)

Possíveis evoluções:

* QR Code para iniciar votação
* auditoria criptográfica
* exportação completa de dados
* autenticação em dois fatores
* modo offline
