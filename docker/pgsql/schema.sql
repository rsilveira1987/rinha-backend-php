DROP TABLE IF EXISTS tb_pessoas;

CREATE EXTENSION IF NOT EXISTS pg_trgm;

CREATE TABLE tb_pessoas (
  id SERIAL PRIMARY KEY,
  uuid varchar(36) NOT NULL,
  apelido varchar(32) NOT NULL,
  nome varchar(100) NOT NULL,
  nascimento varchar(10) NOT NULL,
  stack json DEFAULT NULL,
  UNIQUE(apelido)
);
CREATE INDEX idx_tb_pessoas_uuid ON tb_pessoas(uuid);
CREATE INDEX idx_tb_pessoas_apelido ON tb_pessoas USING gin (apelido gin_trgm_ops);
CREATE INDEX idx_tb_pessoas_nome ON tb_pessoas USING gin (nome gin_trgm_ops);

SET TIME ZONE 'America/Sao_Paulo';