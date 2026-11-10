# ==========================================
# Tahap 1: Install Composer Dependencies
# ==========================================
FROM composer:2 AS composer-builder

WORKDIR /app

# Salin source code untuk download dependensi & dump autoload
COPY . .

# Install dependensi PHP tanpa dev packages
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --ignore-platform-reqs

# ==========================================
# Tahap 2: Build Frontend Assets (Vite & Tailwind)
# ==========================================
FROM node:22-alpine AS frontend-builder

WORKDIR /app

# Salin manifest dependensi Node
COPY package*.json ./

# Install dependensi frontend
RUN npm ci || npm install

# Salin seluruh file proyek
COPY . .

# Salin folder vendor dari composer-builder agar Livewire Flux CSS & Blade templates tersedia
COPY --from=composer-builder /app/vendor ./vendor

# Build aset frontend ke public/build
RUN npm run build

# ==========================================
# Tahap 3: Runtime PHP 8.4 & Apache
# ==========================================
# Proyek mengunci dependensi Symfony 8 (composer.lock) yang membutuhkan PHP 8.4+ (Property Hooks).
FROM php:8.4-apache

WORKDIR /var/www/html

# Install dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan modul mod_rewrite Apache
RUN a2enmod rewrite

# Salin konfigurasi Apache, PHP, dan script entrypoint
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint

# Pastikan entrypoint executable dan bebas karakter CRLF Windows
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint \
    && chmod +x /usr/local/bin/docker-entrypoint

# Salin seluruh kode aplikasi Laravel ke working directory
COPY . /var/www/html

# Salin vendor yang sudah dioptimalkan dari Tahap 1
COPY --from=composer-builder /app/vendor /var/www/html/vendor

# Salin aset frontend hasil build dari Tahap 2
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Siapkan direktori storage dan cache dengan izin pengguna www-data
RUN mkdir -p /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Port default 80 (otomatis disesuaikan secara dinamis oleh entrypoint ke port server)
EXPOSE 80

ENTRYPOINT ["docker-entrypoint"]
CMD ["apache2-foreground"]
