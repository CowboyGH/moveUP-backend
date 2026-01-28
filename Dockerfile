# server/Dockerfile
FROM webdevops/php:8.2-alpine

# Установим необходимые расширения PHP
RUN apk add --no-cache \
    libzip-dev \
    mariadb-client \
    && docker-php-ext-install zip pdo_mysql

WORKDIR /var/www/html

# 1. Копируем только composer-файлы
COPY composer.json composer.lock ./

# 2. Устанавливаем зависимости
RUN composer install --no-interaction --no-scripts

# 3. Копируем ВСЁ остальное (но НЕ vendor!)
COPY . .

# 4. Устанавливаем права
RUN chown -R application:application /var/www/html

EXPOSE 8000

USER application

# Запускаем Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
