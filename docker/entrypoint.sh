#!/bin/sh
# EventFlow — entrypoint: aguarda o banco, prepara o app e sobe o Apache.
set -e

cd /var/www/html

wait_for_mysql() {
    echo ">> Aguardando MySQL em ${DB_HOST}:${DB_PORT}..."
    for i in $(seq 1 60); do
        if mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent 2>/dev/null; then
            echo ">> MySQL disponível."
            return 0
        fi
        sleep 2
    done
    echo "!! MySQL não respondeu a tempo." >&2
    exit 1
}

# APP_KEY ausente? Gera uma (válida só para este ambiente).
if [ -z "$APP_KEY" ]; then
    echo ">> APP_KEY vazia — gerando chave..."
    export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
    echo "!! Defina APP_KEY no .env para produção." >&2
fi

wait_for_mysql

echo ">> Preparando aplicação..."
php artisan storage:link --force 2>/dev/null || true

if [ "${MIGRATE_ON_BOOT:-true}" = "true" ]; then
    echo ">> Rodando migrations..."
    php artisan migrate --force
fi

if [ "${SEED_ON_BOOT:-false}" = "true" ]; then
    echo ">> Rodando seeders..."
    php artisan db:seed --force
fi

echo ">> Otimizando caches..."
php artisan optimize

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo ">> Iniciando: $*"
exec "$@"
