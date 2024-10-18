up:
	docker compose up -d

down:
	docker compose down

stop:
	docker compose stop

clean:
	docker compose down -v --rmi all

build:
	docker compose build --no-cache

composer-install:
	docker compose exec php-fpm composer install

migrate:
	docker compose exec php-fpm php bin/console doctrine:migrations:migrate

exec-php:
	docker compose exec php-fpm bash

consume:
	docker compose exec php-fpm php bin/console messenger:consume async

ui-rabbitmq:
	symfony open:local:rabbitmq # guest guest

#############################################
# Run application commands in the container
#############################################

# json | csv
order-report-%:
	docker compose exec php-fpm php bin/console app:generate-order-report "last_month" --fileType=$*


.PHONY: up down stop clean