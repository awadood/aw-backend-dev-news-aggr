#!/bin/bash

cd /var/www/html
composer install --no-interaction --optimize-autoloader
mv .env.example .env >> /dev/null 2>&1
rm .env.testing >> /dev/null 2>&1
php artisan key:generate --ansi
php artisan migrate:fresh --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
php-fpm

while [ true ]; do
	php artisan schedule:run --verbose --no-interaction;
	sleep 60;
done
