# HELP
# This will output the help for each task
# thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
.PHONY: help

help: ## This help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

start: ## Iniciar containers
	docker-compose up -d

stop: ## Parar containers
	docker-compose down || true
	docker volume rm rinha-backend-php_data || true

clean-all: ## Remove todos os containers/volumes!
	docker-compose down --rmi all --volumes --remove-orphans

pgsql-sh: ## Postgres sh
	docker exec -it pgsql_container psql -U postgres

mysql-sh: ## Mysql sh
	docker exec -it mysql_container /bin/bash

build: ## Build rinha-backend-php image
	docker rmi rinha-backend-php:latest || true
	docker build -t rinha-backend-php . || true
