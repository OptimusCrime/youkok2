.PHONY: all build start p stop down restart status logs migrate composer phpunit phpcs bash-php bash-nginx bash-db

PHP_SERVICE_NAME = youkok2-php
NGINX_SERVICE_NAME = youkok2-nginx
DB_SERVICE_NAME = youkok2-db

#####################################################################################################
#                                                DEV                                                #
#####################################################################################################

build:
	@docker-compose build

start:
	@docker-compose up -d

up: dev-start

stop:
	@docker-compose stop

down:
	@docker-compose down

restart: stop start

status:
	@docker-compose ps

logs:
	@docker-compose logs -f

migrate:
	@docker-compose run --rm $(PHP_SERVICE_NAME) composer migrate

composer:
	@docker-compose run --rm $(PHP_SERVICE_NAME) composer install

phpunit:
	@docker-compose run --rm $(PHP_SERVICE_NAME) composer phpunit

phpcs:
	@docker-compose run --rm $(PHP_SERVICE_NAME) composer phpcs

bash-php:
	@docker-compose run --rm $(PHP_SERVICE_NAME) bash

bash-nginx:
	@docker-compose run --rm $(NGINX_SERVICE_NAME) sh

bash-db:
	@docker-compose run --rm $(DB_SERVICE_NAME) sh

install: composer migrate

upgrade: build restart
