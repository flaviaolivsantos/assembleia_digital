Table users {
  id bigint [pk, increment]
  nome varchar
  email varchar [unique]
  password varchar
  perfil varchar
  cidade_id bigint
  created_at timestamp
  updated_at timestamp
}

Table cidades {
  id bigint [pk, increment]
  nome varchar
  created_at timestamp
  updated_at timestamp
}

Table eleicoes {
  id bigint [pk, increment]
  titulo varchar
  data_eleicao date
  status varchar
  created_at timestamp
  updated_at timestamp
}

Table eleicao_cidades {
  id bigint [pk, increment]
  eleicao_id bigint
  cidade_id bigint
  qtd_membros int
  votos_registrados int
  aberta boolean
  data_abertura timestamp
  data_encerramento timestamp
  aberta_por bigint
  encerrada_por bigint
  created_at timestamp
  updated_at timestamp
}

Table perguntas {
  id bigint [pk, increment]
  eleicao_id bigint
  pergunta text
  qtd_respostas int
  ordem int
  created_at timestamp
  updated_at timestamp
}

Table opcoes {
  id bigint [pk, increment]
  pergunta_id bigint
  cidade_id bigint
  nome varchar
  foto varchar
  ordem int
  created_at timestamp
  updated_at timestamp
}

Table presencas {
  id bigint [pk, increment]
  eleicao_id bigint
  cidade_id bigint
  nome varchar
  votou boolean
  created_at timestamp
  updated_at timestamp
}

Table tokens_votacao {
  id bigint [pk, increment]
  token_hash varchar
  eleicao_id bigint
  cidade_id bigint
  usado boolean
  created_at timestamp
  updated_at timestamp
}

Table votos {
  id bigint [pk, increment]
  token_hash varchar
  pergunta_id bigint
  opcao_id bigint
  created_at timestamp
}

Table logs_eleicao {
  id bigint [pk, increment]
  eleicao_id bigint
  usuario_id bigint
  acao varchar
  descricao text
  created_at timestamp
}

Ref: users.cidade_id > cidades.id

Ref: eleicao_cidades.eleicao_id > eleicoes.id
Ref: eleicao_cidades.cidade_id > cidades.id
Ref: eleicao_cidades.aberta_por > users.id
Ref: eleicao_cidades.encerrada_por > users.id

Ref: perguntas.eleicao_id > eleicoes.id

Ref: opcoes.pergunta_id > perguntas.id
Ref: opcoes.cidade_id > cidades.id

Ref: presencas.eleicao_id > eleicoes.id
Ref: presencas.cidade_id > cidades.id

Ref: tokens_votacao.eleicao_id > eleicoes.id
Ref: tokens_votacao.cidade_id > cidades.id

Ref: votos.pergunta_id > perguntas.id
Ref: votos.opcao_id > opcoes.id

Ref: logs_eleicao.eleicao_id > eleicoes.id
Ref: logs_eleicao.usuario_id > users.id