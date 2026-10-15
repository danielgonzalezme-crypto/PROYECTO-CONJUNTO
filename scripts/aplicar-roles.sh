#!/bin/bash
# Crea los roles de MySQL (rol_admin, rol_escritura, rol_lectura) y un usuario de prueba
# por rol en las tres bases del proyecto. Es repetible: se puede ejecutar varias veces.
# Uso, desde cualquier carpeta, con los contenedores encendidos:
#   bash scripts/aplicar-roles.sh [clave]
# Sin clave, genera una al azar y la muestra al final (no uses / ni & en la clave).
set -u
cd "$(dirname "$0")/.."

DOCKER="docker"
if ! docker ps >/dev/null 2>&1; then DOCKER="sudo docker"; fi

CLAVE=${1:-$(openssl rand -hex 8)}

for svc in residentes-db administrativo-db propietarios-db; do
  DB=$($DOCKER compose exec -T "$svc" printenv MYSQL_DATABASE | tr -d '\r')
  echo "== $svc ($DB) =="
  sed -e "s/__DB__/$DB/g" -e "s/__CLAVE__/$CLAVE/g" scripts/roles.sql \
    | $DOCKER compose exec -T "$svc" sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD"' 2>&1 \
    | grep -v "Using a password"
done

echo "Usuarios: usr_admin, usr_escritura, usr_lectura"
echo "Clave:    $CLAVE"
