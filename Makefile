.PHONY: all bash build clean down logs restart start status stop tail

SERVER_SERVICE_NAME = server

#####################################################################################################
#                                                DEV                                                #
#####################################################################################################

dev-build:
	@docker-compose build

dev-start:
	@docker-compose up -d

dev-up: dev-start

dev-stop:
	@docker-compose stop

dev-down:
	@docker-compose down

dev-restart: dev-stop dev-start

dev-status:
	@docker-compose ps

dev-logs:
	@docker-compose logs -f

dev-reload:
	@docker-compose restart $(SERVER_SERVICE_NAME)

dev-bash:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) bash

dev-migrate:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) composer migrate

dev-composer:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) composer install

dev-phpunit:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) composer phpunit

dev-phpcs:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) composer phpcs

dev-install: dev-composer dev-migrate

dev-upgrade: dev-build dev-restart


#####################################################################################################
#                                                PROD                                               #
#####################################################################################################

prod-build:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml build

prod-start:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml up -d

prod-up: prod-start

prod-stop:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml stop

prod-down:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml down

prod-restart: prod-stop prod-start

prod-status:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml ps

prod-logs:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml logs -f

prod-reload:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml restart $(SERVER_SERVICE_NAME)

prod-bash:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml run --rm $(SERVER_SERVICE_NAME) bash

prod-migrate:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml run --rm $(SERVER_SERVICE_NAME) composer migrate

prod-composer:
	@docker-compose -f docker-compose.yml -f docker-compose-production.yml run --rm $(SERVER_SERVICE_NAME) composer install

prod-install: prod-composer prod-migrate

prod-upgrade: prod-build prod-composer prod-migrate prod-restart
