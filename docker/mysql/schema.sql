CREATE TABLE tb_pessoas (
  id varchar(36),
  apelido varchar(32),
  nome varchar(100),
  nascimento varchar(10),
  stack json DEFAULT NULL
);