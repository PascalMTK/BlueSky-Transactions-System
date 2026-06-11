FROM php:8.2-cli

# System deps + PHP extensions (MySQL local + PostgreSQL production)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libxml2-dev libzip-dev libonig-dev libicu-dev libpq-dev \
    && docker-php-ext-install \
        pdo_mysql pdo_pgsql mbstring xml bcmath zip fileinfo intl opcache pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy Laravel app from subdirectory
COPY bluesky-transactions/ .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --quiet \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD sh -c "\
  php artisan config:clear && \
  php artisan migrate --force && \
  php artisan storage:link 2>/dev/null || true && \
  php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
