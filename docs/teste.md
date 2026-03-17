Passo a passo para testar do zero
1. Preparar o ambiente

cd assembleia-digital
php artisan serve
Acesse: http://localhost:8000

2. Login como Admin
Use as credenciais do usuário admin já cadastrado.

3. Criar uma Eleição
Admin > Eleições > Nova Eleição

Preencha título, data
Salve
4. Adicionar Perguntas e Candidatos
Dentro da eleição criada:

Crie ao menos 1 pergunta (ex: "Escolha o presidente", qtd_respostas = 1)
Dentro da pergunta, adicione candidatos (Opções) — vincule à cidade correta
5. Criar a Mesa de Votação
Admin > Mesas > Nova Mesa

Nome: Mesa 1
Senha: 1234
Salve
6. Criar usuário do tipo "maquina"
Admin > Usuários > Novo Usuário

Perfil: maquina
Cidade: a mesma da eleição
Salve (anote login/senha)
7. Abrir a Eleição para a Cidade
Faça login como responsavel da cidade e abra a eleição.

(ou via admin se tiver acesso direto)

Testar Votação PRESENCIAL
Faça login com o usuário maquina → vai para /votacao
Como há mesa_id ainda vazio na sessão, a tela mostra campo de token
Clique no link ou acesse /votacao/mesa diretamente
Login na mesa: nome Mesa 1, senha 1234
Dashboard da mesa aparece com botão "Liberar Votação"
Clique em Liberar Votação → tela de votação abre
Selecione os candidatos → Confirmar Voto
Tela de confirmação → redireciona de volta para o dashboard da mesa
Repita do passo 4 para simular outro eleitor
Testar Votação REMOTA
Como mesario, acesse a lista de presenças da eleição
Cadastre um membro → o sistema gera um token (ex: ABCDE-FGHIJ-KLMNO)
Faça login com o usuário maquina (sem sessão de mesa)
Acesse /votacao → tela pede o token
Digite o token gerado → tela de votação abre
Vote → token marcado como usado → não pode votar novamente
Verificar os dados gravados

php artisan tinker

App\Models\Voto::latest()->take(5)->get(['origem', 'mesa_id', 'token_hash', 'created_at']);
Você verá origem = presencial com mesa_id preenchido nos votos da mesa, e origem = remoto com mesa_id = null nos votos por token.