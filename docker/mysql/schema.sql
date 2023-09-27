CREATE DATABASE IF NOT EXISTS backend;

USE backend;

CREATE TABLE tb_pessoas (
  id varchar(36),
  apelido varchar(32),
  nome varchar(100),
  nascimento varchar(10),
  stack text
);

CREATE FULLTEXT INDEX id_ftsidx ON tb_pessoas ( id );
CREATE FULLTEXT INDEX apelido_ftsidx ON tb_pessoas ( apelido );
CREATE FULLTEXT INDEX nome_ftsidx ON tb_pessoas ( nome );
CREATE FULLTEXT INDEX stack_ftsidx ON tb_pessoas ( stack );