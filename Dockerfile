# Stage 1: Install Composer dependencies
FROM composer:2 AS composer_stage
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# Stage 2: Build frontend assets with pnpm
FROM node:20-alpine AS frontend
WORKDIR /app

# Install pnpm globally
RUN npm install -g pnpm

# Install frontend dependencies using pnpm
COPY package.json pnpm-lock.yaml* pnpm-workspace.yaml* ./
RUN pnpm install

# Copy vendor from composer_stage (required for Filament theme.css and Laravel Tailwind scanning)
COPY --from=composer_stage /app/vendor /app/vendor

# Copy application source files needed for Vite compilation and Tailwind class detection
COPY resources/ resources/
COPY app/ app/
COPY vite.config.js ./

# Compile assets with pnpm
RUN pnpm run build

# Stage 3: Production PHP runtime with FrankenPHP
FROM dunglas/frankenphp:1-php8.3-alpine

WORKDIR /app

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

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

# Remove Linux capabilities from FrankenPHP binary to allow execution under Render's no-new-privileges security policy
RUN apk add --no-cache libcap && \
    setcap -r /usr/local/bin/frankenphp && \
    apk del libcap

# Copy Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy vendor from composer_stage
COPY --from=composer_stage /app/vendor ./vendor

# Copy application files
COPY . .

# Copy compiled frontend assets from frontend stage
COPY --from=frontend /app/public/build ./public/build

# Finish composer autoload dump with classmap optimization
RUN composer dump-autoload --no-dev --optimize

# Configure Caddy and startup entrypoint
COPY Caddyfile /etc/caddy/Caddyfile
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure storage directories and Caddy configuration cache exist with write permissions
RUN mkdir -p storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database \
    /data \
    /config \
    && chmod -R 775 storage bootstrap/cache database public \
    && chmod -R 777 /data /config

EXPOSE 8000 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
