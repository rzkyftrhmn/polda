FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash curl git icu-dev libzip-dev oniguruma-dev zlib-dev \
    freetype-dev libpng-dev libjpeg-turbo-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo pdo_mysql mbstring bcmath zip exif pcntl

# Optional image extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Enable opcache
RUN docker-php-ext-install opcache \
    && echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy entire application code first
COPY . .

# Copy composer files first to cache dependency layer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# Nginx
FROM nginx:alpine
COPY --from=app /var/www/html/public /var/www/html/public
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Set permission Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]