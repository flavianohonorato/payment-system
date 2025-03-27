FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    git \
    && docker-php-ext-install zip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN  docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./backend .

RUN composer install

RUN chown -R www-data:www-data /var/www/html/storage

EXPOSE 9000

CMD [ "php-fpm" ]