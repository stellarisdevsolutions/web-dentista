# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* pnpm-lock.yaml* ./
RUN npm install
COPY resources/ resources/
COPY vite.config.js ./
RUN npm run build

# Stage 2: Production PHP runtime with FrankenPHP
FROM dunglas/frankenphp:1-php8.3-alpine

WORKDIR /app

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    FRANKENPHP_CONFIG="worker ./public/index.php"

# Install PHP extensions required by Laravel, Filament & Intervention Image
RUN install-php-extensions \
    pdo_sqlite \
    sqlite3 \
    gd \
    intl \
    zip \
    bcmath \
    pcntl \
    opcache \
    exif

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies first to leverage Docker layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# Copy the rest of the application
COPY . .

# Copy compiled frontend assets from frontend stage
COPY --from=frontend /app/public/build ./public/build

# Finish composer autoload dump
RUN composer dump-autoload --no-dev --optimize

# Configure Caddy and startup entrypoint
COPY Caddyfile /etc/caddy/Caddyfile
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure storage directories exist with write permissions
RUN mkdir -p storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database \
    && chmod -R 775 storage bootstrap/cache database

EXPOSE 8000 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
