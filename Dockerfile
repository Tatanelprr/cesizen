# ── Stage 1 : dépendances PHP (production) ────────────────────────────────────
FROM composer:2 AS composer-builder
WORKDIR /var/www/html

COPY composer.json composer.lock symfony.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# ── Stage 2 : image de production ────────────────────────────────────────────
FROM php:8.4-fpm-alpine AS production

# Dépendances système + nginx + supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    gettext \
    curl

# Extensions PHP requises
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    intl \
    xml \
    zip \
    opcache

# Opcache production (preload désactivé — free tier RAM)
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.interned_strings_buffer=8"; \
    echo "opcache.max_accelerated_files=10000"; \
    echo "opcache.validate_timestamps=0"; \
    echo "opcache.preload="; \
} > /usr/local/etc/php/conf.d/opcache.ini

ENV APP_ENV=prod
ENV APP_DEBUG=0

WORKDIR /var/www/html

# Vendor depuis le builder
COPY --from=composer-builder /var/www/html/vendor ./vendor

# Code source
COPY . .

# AssetMapper : télécharge les packages JS + installe les assets des bundles
RUN php bin/console importmap:install --no-interaction
RUN php bin/console assets:install --env=prod --no-debug

# Config nginx (template — PORT substitué au démarrage)
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/conf.d/app.conf /etc/nginx/conf.d/app.conf.template
RUN rm -f /etc/nginx/http.d/default.conf

# PHP-FPM pool réduit (500 MB RAM)
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Supervisor + script de démarrage
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Répertoires Symfony + permissions
RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data var public && \
    chmod -R 775 var

EXPOSE 8080

CMD ["/bin/sh", "/start.sh"]
