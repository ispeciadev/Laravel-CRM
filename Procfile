web: vendor/bin/heroku-php-apache2 public/
release: composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-gd && php artisan migrate --force && php artisan storage:link
