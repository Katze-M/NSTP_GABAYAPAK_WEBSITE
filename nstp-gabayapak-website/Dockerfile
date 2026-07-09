# ==========================================
# STAGE 1: Frontend Asset Compilation Layout
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /build

# Copy all source assets into the temporary node layer
COPY . /build

# Install front-end dependencies and build the Vite manifest files securely
RUN npm install && npm run build

# ==========================================
# STAGE 2: Core Production Web Server Setup
# ==========================================
FROM php:8.3-apache

# Install essential core utilities and database drivers for PostgreSQL
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache's mod_rewrite routing configurations
RUN a2enmod rewrite

# Point Apache's document root directly to Laravel's public entry point
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establish the active working directory directory layout
WORKDIR /var/www/html

# Copy the core framework files directly to the server
COPY . /var/www/html

# 🔥 INJECTION: Copy your cleanly compiled Vite assets directly from STAGE 1
COPY --from=frontend-builder /build/public/build /var/www/html/public/build

# Stable Binary Injection: Copy Composer natively from its official core repository
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Run backend framework optimization routines automatically
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Force refresh the system configs in safe mode to map production environment variables
RUN php artisan config:clear --no-plugins && php artisan cache:clear --no-plugins

# Grant the Apache server web user read/write access to your cache and storage directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
