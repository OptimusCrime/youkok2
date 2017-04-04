.PHONY: all bash build clean down logs restart start status stop tail

SERVER_SERVICE_NAME = server

all:
	build
	start

bash:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) bash

build:
	@docker-compose build

clean:
	stop
	@docker-compose rm --force

down:
	@docker-compose down

logs:
	@docker-compose logs -f

restart: 
	stop 
	start

start:
	@docker-compose up -d

status:
	@docker-compose ps

stop:
	@docker-compose stop

tail:
	@docker-compose logs $(OW4_MAKE_TARGET)

migrate:
	@docker-compose run --rm $(SERVER_SERVICE_NAME) php 
