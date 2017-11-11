.PHONY: all bash build clean down logs restart start status stop tail

SERVER_SERVICE_NAME = server

all: start

bash:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml run --rm $(SERVER_SERVICE_NAME) bash

build:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml build

down:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml down

restart: stop start

reload:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml restart $(SERVER_SERVICE_NAME)

start:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml up -d

up: start

status:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml ps

stop:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml stop

migrate:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml run --rm $(SERVER_SERVICE_NAME) vendor/bin/phinx migrate -c docker/phinx.yml

composer:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml run --rm $(SERVER_SERVICE_NAME) composer install

install: composer migrate

upgrade: build restart
