DROP TABLE IF EXISTS tb_pessoas;

CREATE EXTENSION IF NOT EXISTS pg_trgm;

CREATE TABLE tb_pessoas (
  id SERIAL PRIMARY KEY,
  uuid varchar(36),
  apelido varchar(32),
  nome varchar(100),
  nascimento varchar(10),
  stack json DEFAULT NULL
);
CREATE INDEX idx_tb_pessoas_uuid ON tb_pessoas(uuid);
CREATE INDEX idx_tb_pessoas_apelido ON tb_pessoas USING gin (apelido gin_trgm_ops);
CREATE INDEX idx_tb_pessoas_nome ON tb_pessoas USING gin (nome gin_trgm_ops);

SET TIME ZONE 'America/Sao_Paulo';