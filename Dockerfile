FROM webdevops/php:8.2-alpine

WORKDIR /var/www/html

#Копируем код
COPY . .

#Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#Устанавливаем зависимости
RUN composer install --no-interaction --no-scripts

#Настройка прав
RUN chown -R application:application /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
