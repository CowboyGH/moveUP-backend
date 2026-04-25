# moveUP Backend

Laravel API backend for the moveUP mobile application.

## Базовые команды

- Создать миграцию: `docker exec laravel-api php artisan make:migration <name>`
- Запустить миграции: `docker exec laravel-api php artisan migrate`
- Сбросить БД и сиды: `docker exec laravel-api php artisan migrate:fresh --seed`
- Проверить маршруты: `docker exec laravel-api php artisan route:list`

## Swagger

- Сгенерировать документацию: `docker exec -it laravel-api php artisan l5-swagger:generate`

## Планировщик

- Запустить планировщик в foreground: `docker exec -it laravel-api php artisan schedule:work`
- Запустить планировщик в фоне: `docker exec -d laravel-api php artisan schedule:work`
- Выполнить один проход расписания: `docker exec laravel-api php artisan schedule:run`
