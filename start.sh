#!/bin/bash

echo "================================="
echo "Iniciando Proyecto Laravel con Docker 🚀"
echo "================================="
echo ""

# =========================
# Variables
# =========================
APP_CONTAINER="reportefallasbackend-php"
NGINX_CONTAINER="reportefallasbackend-nginx"

echo ""
echo ">> Reconstruyendo contenedores y levantando servicios (docker compose up -d)..."
docker compose -f docker-compose.prod.yml up -d --build

# =========================
# Esperar a que los contenedores estén listos
# =========================
wait_for_container() {
    local container_name=$1
    echo ">> Esperando a que el contenedor $container_name esté listo..."
    while ! docker ps --filter "name=$container_name" --format "{{.Names}}" | grep -q "$container_name"; do
        sleep 2
    done
    echo ">> Contenedor $container_name está en ejecución."
}

wait_for_container "$APP_CONTAINER"
wait_for_container "$NGINX_CONTAINER"

echo ""
echo ">> Instalando dependencias Composer y NPM..."
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/public/images /var/www/public/thumbnails /var/www/storage/logs /var/www/storage /var/www/bootstrap/cache
docker exec "$APP_CONTAINER" php artisan optimize:clear
docker exec "$APP_CONTAINER" composer install --ignore-platform-req=ext-gd
docker exec "$APP_CONTAINER" npm install

echo ""
echo ">> Ejecutando migraciones..."
docker exec "$APP_CONTAINER" php artisan migrate

echo ""
echo ">> Instalando passport..."
docker exec -d "$APP_CONTAINER" php artisan passport:install

echo ""
echo ">> Iniciando queue:work..."
docker exec -d "$APP_CONTAINER" php artisan queue:work

echo ""
echo ">> Creando enlace simbólico..."
docker exec -d "$APP_CONTAINER" php artisan storage:link

echo "=========================================="
echo "¡Proyecto listo con Certbot instalado! 🔒🚀"
echo "=========================================="