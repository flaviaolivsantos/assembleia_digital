Preciso alterar a lógica de votação do sistema para suportar um modelo híbrido de votação: presencial e remoto.

Atualmente o sistema utiliza tokens individuais para todos os eleitores, mas isso precisa mudar.

O novo modelo será:

- Votação presencial controlada nas máquinas de votação
- Votação remota utilizando tokens individuais

A implementação deve preservar o restante do sistema já existente (eleições, candidatos, painel administrativo e apuração).

--------------------------------

MODELO DE VOTAÇÃO PRESENCIAL

A votação presencial será realizada em máquinas de votação.

Fluxo:

2. O responsável faz login com o perfil máquina de votação.
3. Quando um eleitor chegar para votar, o responsável pelo acesso deve digitar sua senha para liberar a votação para aquele eleitor.
4. Ao digitar a senha, o sistema libera a tela de votação para aquele eleitor.
5. O eleitor realiza o voto normalmente.
6. Após o envio do voto, a votação é finalizada e o sistema volta para o estado de espera aguardando nova liberação para um novo eleitor.

Regras importantes:

- Cada liberação por senha deve permitir apenas UM voto.
- Após o voto ser registrado, a tela deve voltar ao estado inicial aguardando nova autorização.
- O voto presencial deve registrar qual Máquina de votação foi utilizada.

--------------------------------

MODELO DE VOTAÇÃO REMOTA

A votação remota continuará utilizando tokens individuais, porém, será liberado pelo perfil administrador ou Responsável ou mesário.

Fluxo:

1. O usuário de algum dos perfis citados anteriormente importa ou cadastra os membros que irão votar remotamente.
2. Para cada membro remoto, o sistema gera automaticamente um token de votação.
3. O membro acessa o portal de votação e informa seu token.
4. O sistema valida o token.
5. Se o token for válido e ainda não tiver sido utilizado, o eleitor pode votar.
6. Após o voto, o token é marcado como utilizado e não pode mais ser reutilizado.

Regras importantes:

- Cada token deve permitir apenas um voto.
- Tokens devem ser automaticamente invalidados após o uso.
- Tokens não devem permitir identificar o voto do eleitor (manter anonimato).