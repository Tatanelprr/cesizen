#!/bin/sh
set -e

# Railway injecte $PORT dynamiquement ; fallback à 8080
export PORT="${PORT:-8080}"

# Injecte $PORT dans la config nginx (uniquement cette variable)
envsubst '${PORT}' < /etc/nginx/conf.d/app.conf.template > /etc/nginx/http.d/default.conf

# Construit DATABASE_URL depuis les variables individuelles Railway
# (évite les problèmes de résolution des références ${{...}} imbriquées)
if [ -n "${DB_HOST}" ]; then
    export DATABASE_URL="mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}?serverVersion=8.0&charset=utf8mb4"
fi

# Migrations Doctrine
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod

# Setup du transport Messenger (doctrine) — ignore l'erreur si déjà fait
php bin/console messenger:setup-transports --env=prod 2>/dev/null || true

# Permissions sur var/ (sécurité si volume monté)
chown -R www-data:www-data var

exec supervisord -c /etc/supervisord.conf
