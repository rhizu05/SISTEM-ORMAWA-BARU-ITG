# ==============================================================================
# TAHAP 1: Build Aset Frontend (Node.js & Vite)
# ==============================================================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources/ ./resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public/ ./public/

RUN npm run build

# ==============================================================================
# TAHAP 2: Image Aplikasi Utama (PHP-FPM Alpine)
# ==============================================================================
FROM php:8.2-fpm-alpine AS app

# Pasang dependensi sistem yang dibutuhkan untuk ekstensi PHP & DomPDF
RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    $PHPIZE_DEPS

# Konfigurasi dan instalasi ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        opcache

# Pasang ekstensi Redis via PECL
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# Bersihkan build dependencies untuk memperkecil ukuran image
RUN apk del $PHPIZE_DEPS

# Pasang Composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Salin konfigurasi PHP dan OPcache
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www

# Salin seluruh kode proyek
COPY . /var/www

# Salin aset frontend hasil kompilasi dari tahap 1
COPY --from=frontend-builder /app/public/build /var/www/public/build

# Pasang dependensi backend PHP (Production: tanpa dependensi dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Siapkan skrip entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Atur hak akses direktori storage dan cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
