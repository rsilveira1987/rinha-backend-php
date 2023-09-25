# HELP
# This will output the help for each task
# thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
.PHONY: help
.IGNORE

help: ## This help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

start: ## Iniciar containers
	docker-compose up -d

stop: ## Parar containers
	docker-compose down
	docker volume rm rinha-backend-php_postgresql_data

clean: ## Limpar o banco de dados
	docker volume rm rinha-backend-php_postgresql_data

clean-all: ## Remove todos os containers/volumes!
	docker-compose down --rmi all --volumes --remove-orphans

pgsql-sh: ## Postgres sh
	docker exec -it pgsql_container psql -U postgres

build: .IGNORE ## Build rinha-backend-php image
	docker rmi rinha-backend-php:latest
	docker build -t rinha-backend-php .
