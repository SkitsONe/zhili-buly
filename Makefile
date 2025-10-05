.PHONY: up down build shell-app shell-db composer artisan migrate fresh logs init

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

shell-app:
	docker compose exec app bash

shell-db:
	docker compose exec db mysql -u root -psecret laravel

composer:
	docker compose exec app composer $(filter-out $@,$(MAKECMDGOALS))

artisan:
	docker compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

migrate:
	docker compose exec app php artisan migrate

fresh:
	docker compose exec app php artisan migrate:fresh --seed

logs:
	docker compose logs -f

pma:
	xdg-open http://localhost:8080 || open http://localhost:8080

init:
	docker compose exec app composer install
	docker compose exec app cp .env.docker .env
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate
	echo "✅ Проект инициализирован! Откройте http://localhost:8000"

%:
	@:
