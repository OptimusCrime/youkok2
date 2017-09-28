.PHONY: all bash build clean down logs restart start status stop tail

SERVER_SERVICE_NAME = server

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

start:
	@docker-compose up -d

status:
	@docker-compose ps

stop:
	@docker-compose stop

tail:
	@docker-compose logs $(SERVER_SERVICE_NAME)

migrate:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) vendor/bin/phinx migrate -c docker/docker-phinx.yml

composer:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) composer install

install: composer migrate
