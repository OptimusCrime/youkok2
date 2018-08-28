.PHONY: all bash build clean down logs restart start status stop tail

SERVER_SERVICE_NAME = server
FRONTEND_SERVICE_NAME = frontend

all: start

bash:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) bash

build:
	@docker-compose build

down:
	@docker-compose down

logs:
	@docker-compose logs -f

restart: stop start

reload:
	@docker-compose restart $(SERVER_SERVICE_NAME)

start:
	@docker-compose up -d

up: start

status:
	@docker-compose ps

stop:
	@docker-compose stop

migrate:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) composer migrate

composer:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) composer install

install: composer migrate

upgrade: build restart

prod-start:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml up -d

prod-build:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml build

prod-upgrade: stop prod-build composer migrate prod-start