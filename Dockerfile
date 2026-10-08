FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    && docker-php-ext-install zip pdo pdo_sqlite pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Setup SQLite database and permissions
RUN touch database/database.sqlite && chmod -R 777 storage bootstrap/cache database

# Render passes the port via $PORT environment variable (default 10000)
ENV PORT=10000
EXPOSE 10000

# Run migration with seed and start Laravel
CMD php artisan migrate --seed --force && php artisan serve --host=0.0.0.0 --port=${PORT}
