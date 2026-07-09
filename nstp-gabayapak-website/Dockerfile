# 1. Use the official high-performance FrankenPHP production image
FROM dunglas/frankenphp:1-php8.3

# 2. Install essential Linux utilities and database drivers for PostgreSQL
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# 3. Enable the core production web configurations
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# 4. Copy your Laravel source code files securely into the server 
COPY . /app

# 5. Tell the server to point explicitly to Laravel's secure /public directory
ENV FRANKENPHP_DOCUMENT_ROOT=/app/public

# 6. Install your composer dependencies automatically
RUN curl -sS https://getcomposer.org | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# 7. Grant the server read/write access to your cache directories
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
