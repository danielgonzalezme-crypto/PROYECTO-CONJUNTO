#!/bin/sh
set -e

# Si no se definió APP_KEY, se genera una para esta ejecución
if [ -z "$APP_KEY" ]; then
  APP_KEY="$(php artisan key:generate --show)"
  export APP_KEY
fi

# Crea o actualiza las tablas en administrativo_db
php artisan migrate --force

# Crea el usuario administrador de demostración si aún no existe
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port=8000
