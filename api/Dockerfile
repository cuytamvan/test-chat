FROM php:8.1-fpm

RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        zip \
        unzip \
        curl \
        libonig-dev \
        libxml2-dev \
        libmcrypt-dev \
        libmagickwand-dev \
        libssl-dev \
        libcurl4-openssl-dev \
        pkg-config \
        && \
    pecl install mcrypt-1.0.3 && \
    docker-php-ext-install pdo_pgsql zip mbstring exif pcntl bcmath opcache sockets xmlrpc && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    docker-php-ext-install curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 777 /var/www/html/bootstrap/cache

WORKDIR /var/www/html

RUN composer install
RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan migrate --seed

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]