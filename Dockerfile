# ==========================================
# Tahap 1: Build Frontend Assets (Vite & Tailwind)
# ==========================================
FROM node:22-alpine AS frontend-builder

WORKDIR /app

# Salin manifest dependensi Node
COPY package*.json ./

# Install dependensi frontend
RUN npm ci || npm install

# Salin seluruh file proyek agar Tailwind dapat memindai kelas di Blade & script
COPY . .

# Build aset frontend ke folder public/build
RUN npm run build

# ==========================================
# Tahap 2: Runtime PHP 8.3 & Apache
# ==========================================
# Catatan: Proyek menggunakan composer.json dengan require "php": "^8.3".
# Oleh karena itu, image php:8.3-apache digunakan untuk menjamin kompatibilitas.
FROM php:8.3-apache

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

# Salin Composer binary dari image resmi Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Salin konfigurasi Apache, PHP, dan script entrypoint
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint

# Pastikan entrypoint executable dan bebas karakter CRLF Windows
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint \
    && chmod +x /usr/local/bin/docker-entrypoint

# Salin seluruh kode aplikasi Laravel ke working directory
COPY . /var/www/html

# Salin aset frontend hasil build dari Tahap 1
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Install dependensi Composer untuk produksi (tanpa paket development)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Siapkan direktori storage dan cache dengan izin pengguna www-data
RUN mkdir -p /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Port default 80 (akan otomatis disesuaikan secara dinamis oleh entrypoint ke port Render $PORT)
EXPOSE 80

ENTRYPOINT ["docker-entrypoint"]
CMD ["apache2-foreground"]
