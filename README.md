# moveUP Backend

Laravel API backend for the moveUP mobile application.

## Базовые команды

- Инициализировать локальное окружение: `cp .env.example .env`
- Сгенерировать ключ приложения: `docker exec laravel-api php artisan key:generate`
- Сгенерировать отдельный JWT secret: `docker exec laravel-api php artisan jwt:secret`
- Пересобрать и поднять API-контейнер: `docker compose up --build -d server`
- Пересоздать контейнер после изменения env/compose: `docker compose up -d --force-recreate server`
- Поднять optional phpMyAdmin: `docker compose --profile tools up -d phpmyadmin`
- Создать миграцию: `docker exec laravel-api php artisan make:migration <name>`
- Запустить миграции: `docker exec laravel-api php artisan migrate`
- Сбросить БД и сиды: `docker exec laravel-api php artisan migrate:fresh --seed`
- Проверить маршруты: `docker exec laravel-api php artisan route:list`

## Swagger

- Открыть Swagger UI: `http://localhost:8000/api/documentation`
- Сгенерировать документацию из контейнера: `docker exec laravel-api php artisan l5-swagger:generate`
- Альтернатива внутри контейнера: `composer docs:generate`
- После любого изменения маршрутов, контроллеров или OpenAPI-аннотаций нужно запускать `l5-swagger:generate` и коммитить изменения аннотаций вместе с обновленным `storage/api-docs/api-docs.json`.
- `storage/api-docs/api-docs.json` не редактируется вручную.

## Планировщик

- Очередь писем и фоновых jobs обрабатывается `queue:work` внутри `laravel-api` через Supervisor
- После изменения `MAIL_*`, `QUEUE_*` или `supervisord.conf` нужно выполнить `docker compose up -d --force-recreate server`
- Запустить планировщик в foreground: `docker exec -it laravel-api php artisan schedule:work`
- Запустить планировщик в фоне: `docker exec -d laravel-api php artisan schedule:work`
- Выполнить один проход расписания: `docker exec laravel-api php artisan schedule:run`
