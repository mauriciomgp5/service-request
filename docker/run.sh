#!/bin/sh

cd /var/www

php artisan cache:clear
php artisan route:cache

/usr/bin/supervisord
