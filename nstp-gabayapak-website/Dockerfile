# 1. Use the standard, verified PHP 8.3 production image with Apache built-in
FROM php:8.3-apache

# 2. Install core utilities, create the keyrings path directory, and add Node.js 20 GPG keys manually
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    git \
    curl \
    gnupg \
    ca-certificates \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://nodesource.com | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://nodesource.com nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# 3. Enable Apache's mod_rewrite module
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

# 9. Compile your frontend CSS/JS assets natively using Vite
RUN npm install && npm run build

# 10. FIXED: Clear configurations in safe-mode to bypass database validation blocks entirely
RUN php artisan config:clear --no-plugins && php artisan cache:clear --no-plugins

# 11. Grant the Apache server web user read/write access to your cache and storage directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
