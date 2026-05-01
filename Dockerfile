# Stage 1: Build dependencies and assets
FROM php:8.1-fpm AS builder

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    zip \
    bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code first
COPY . .

# Create bootstrap cache directory
RUN mkdir -p bootstrap/cache

# Install PHP dependencies
RUN composer install \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts

# Install Node.js for asset building (use LTS version)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y --no-install-recommends nodejs && \
    rm -rf /var/lib/apt/lists/*

# Install Node dependencies
RUN npm ci

# Build frontend assets
RUN npm run build

# Stage 2: Runtime image
FROM php:8.1-fpm AS runtime

WORKDIR /app

# Install runtime dependencies only
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq5 \
    libpq-dev \
    libzip-dev \
    libzip5 \
    nginx \
    supervisor \
    curl \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    zip \
    bcmath

# Copy PHP config
COPY docker/php/php.ini /usr/local/etc/php/
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/

# Copy Nginx config
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/runtime.conf.template /etc/nginx/templates/default.conf.template

# Copy Supervisor config
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy built app from builder stage
COPY --from=builder /app /app

# Create necessary directories
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} && \
    chown -R www-data:www-data /app && \
    chmod -R 755 storage bootstrap/cache

# Create PHP-FPM entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -fsS "http://localhost:${PORT:-80}/health" || exit 1

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
