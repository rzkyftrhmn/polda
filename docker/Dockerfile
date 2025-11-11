FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache bash curl git icu-dev libzip-dev oniguruma-dev zlib-dev

# PHP extensions commonly needed by Laravel
RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath zip exif

# Enable Opcache (optional)
RUN docker-php-ext-install opcache \
    && { echo "opcache.enable=1"; echo "opcache.enable_cli=1"; } > /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# You can uncomment the following lines to build dependencies during image build
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
# COPY . .

CMD ["php-fpm"]