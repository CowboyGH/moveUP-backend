# server/Dockerfile
FROM webdevops/php:8.2-alpine

RUN apk add --no-cache \
    libzip-dev \
    mariadb-client \
    php82-pecl-redis \
    && docker-php-ext-install zip pdo_mysql

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-scripts --prefer-dist

COPY . .

RUN chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
