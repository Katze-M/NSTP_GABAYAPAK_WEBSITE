# 1. Use the standard, verified PHP 8.3 production image with Apache built-in
FROM php:8.3-apache

# 2. Install essential Linux utilities and database drivers for PostgreSQL
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# 3. Enable Apache's mod_rewrite module (this is required for Laravel's routing to work)
RUN a2enmod rewrite

# 4. Point Apache's document root to Laravel's secure /public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Set the active workspace directory inside the container
WORKDIR /var/www/html

# 6. Copy your Laravel source code files securely into the server 
COPY . /var/www/html

# 7. Stable Binary Injection: Copy Composer directly from the official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. Run your framework composer dependencies automatically
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 9. Grant the Apache server web user read/write access to your cache folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Increase upload file size limits inside the PHP configuration
RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini
