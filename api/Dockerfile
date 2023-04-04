FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        zip \
    && pecl install \
        xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install
RUN php artisan migrate --seed
RUN php artisan optimize

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]