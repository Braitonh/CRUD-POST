UID=$(shell id -u)
GID=$(shell id -g)
DOCKER_PHP_SERVICE=app

start: erase cache-folders build composer-install up

erase:
		docker-compose down -v

build:
		docker-compose build --no-cache && \
		docker-compose pull

cache-folders:
		mkdir -p ~/.composer && chown ${UID}:${GID} ~/.composer

composer-install:
		docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} composer install

up:
		docker-compose up

stop:
		docker-compose stop

bash:
	docker compose exec -it --user ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh

ssh-be:
		docker compose exec -it --user ${UID}:${GID} -e HOME=/var/www ${DOCKER_PHP_SERVICE} bash	

logs:
		docker-compose logs -f ${DOCKER_PHP_SERVICE}