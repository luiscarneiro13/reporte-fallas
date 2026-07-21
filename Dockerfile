FROM carneiroluis2/laravel10:latest

WORKDIR /var/www

COPY . .

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/zz-uploads.ini

EXPOSE 9000
