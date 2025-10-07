#!/bin/sh

# Esperar a MySQL
while ! mysqladmin ping -h"mysql" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
  sleep 1
done

# Configurar permisos dinámicos
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Migraciones y optimización
php artisan migrate --force
php artisan optimize:clear
php artisan optimize

exec php-fpm
