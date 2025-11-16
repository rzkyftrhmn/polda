FROM php:8.3-fpm-alpine AS app

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

# Copy application source first (prevents composer script errors that need app files)
COPY . .

# Install dependencies (production)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Ensure writable permissions for storage and cache
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

# -----------------------------
# Nginx stage with bundled config
# -----------------------------
FROM nginx:alpine AS nginx

# Copy only public assets into nginx image
COPY --from=app /var/www/html/public /var/www/html/public

# Bundle nginx configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf