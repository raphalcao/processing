PATH  := $(PATH):$(PWD)/bin:
SHELL := /bin/bash

.PHONY: help clone enter copy-env up access-container install-deps generate-key migrate seed
.DEFAULT_GOAL = help

phpmd := vendor/bin/phpmd
phpcs := vendor/bin/phpcs
phpcbf := vendor/bin/phpcbf
phpunit := vendor/bin/phpunit

CONTAINER := php
PATH_CONTAINER := /var/www/html
COMPOSE_DEV := docker-compose.yml

VERSION ?= v0.0.1
REGISTRY ?= raphalcao
DOCKERFILE_PATH := .infra/docker/Dockerfile
DOCKERIGNORE_FILE := ".infra/docker/.dockerignore"

## —— Inicia o Projeto 🚀  ————————————————————————————————————————————————————
start: ## Inicia o projeto SEM recriar o MySQL
	make start-php

start-php: ## Inicia apenas o PHP se ele não estiver rodando
	@docker ps -q --filter "name=php" | grep -q . && echo "✅ PHP já está rodando." || docker start php
	@printf "\033[32mPHP iniciado com sucesso!\033[0m\n"

restart-php: ## Reinicia o PHP se já estiver rodando
	@docker ps -q --filter "name=php" | grep -q . && docker restart php || echo "⚠️ O container PHP não está rodando."
	@printf "\033[32mPHP reiniciado com sucesso!\033[0m\n"

## —— Comandos ⚙️  ————————————————————————————————————————————————————————————
copy-env: ## Copia o arquivo .env.example para .env se ele não existir
	@if [ ! -f .env ]; then cp .env.example .env; fi
	@printf "\033[32mArquivo .env criado.\033[0m\n"

up: ## Inicia apenas o PHP (sem MySQL)
	docker compose -f $(COMPOSE_DEV) up -d --no-deps php
	@printf "\033[32mDocker iniciado com sucesso!\033[0m\n"

set-container-php-name: ## Define a variável CONTAINER com o nome do container da aplicação PHP
	@$(eval CONTAINER=$(shell \
		container_id=$$(docker-compose ps -q php); \
		container_name=$$(docker inspect --format '{{.Name}}' $${container_id} | sed 's/^.\(.*\)/\1/'); \
		echo $${container_name} \
	)) \
	echo "Nome do container PHP: $(CONTAINER)"

install-deps: ## Instala as dependências do projeto
	docker exec -it $(CONTAINER) composer install
	@printf "\033[32mComplementos instalados com sucesso!\033[0m\n"

generate-key: ## Cria uma chave para a aplicação
	docker exec -it $(CONTAINER) php artisan key:generate
	@printf "\033[32mChave gerada com sucesso!\033[0m\n"

access-container: ## Acessa o container da aplicação
	docker exec -it $(CONTAINER) bash
	@printf "\033[32mAcesso ao container realizado com sucesso!\033[0m\n"

clean: ## Remove apenas containers e arquivos (sem tocar no MySQL)
	@printf "\033[5;1m\033[33m\033[41mLimpando!\033[0m\n"
	@docker compose -f $(COMPOSE_DEV) down --remove-orphans
	@docker image prune -a -f
	@docker volume prune -f
	@docker network prune -f
	@printf "\033[32mProjeto limpo com sucesso (MySQL não foi removido)!\033[0m\n"

restart: ## Reinicia apenas o PHP (sem MySQL)
	@docker compose -f $(COMPOSE_DEV) restart php
	@printf "\033[32mContainers reiniciados com sucesso!\033[0m\n"

## —— Docker 🐳  ———————————————————————————————————————————————————————————————
docker-start: ## Iniciar apenas o PHP
	docker compose -f $(COMPOSE_DEV) up -d --no-deps php

docker-build: ## Iniciar PHP com build (sem MySQL)
	docker compose -f $(COMPOSE_DEV) up -d --build --no-deps php

docker-stop: ## Desligar apenas o PHP
	docker compose -f $(COMPOSE_DEV) down

docker-shell: ## Acessar container do php
	docker exec -it $(CONTAINER) sh

docker-rebuild-all: ## Rebuild apenas o PHP (sem MySQL)
	make docker-stop docker-build

## —— Mensagens 📝  ————————————————————————————————————————————————————————————
msg_success: ## Mensagem de sucesso
	@printf "\033[32mProjeto iniciado com sucesso!\033[0m\n"

msg_error: ## Mensagem de erro
	@printf "\033[31mOcorreu um erro!\033[0m\n"

## —— Ajuda 🛠️️  —————————————————————————————————————————————————————————————
help: ## Mostra os comandos disponíveis:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-24s\033[0m %s\n", $$1, $$2}' \
	| sed -e 's/\[32m## /[33m/' && printf "\n"
