up:
	docker compose up -d

down:
	docker compose down

stop:
	docker compose stop

clean:
	docker compose down -v --rmi all

build:
	docker compose build --no-cache up -d

.PHONY: up down stop clean