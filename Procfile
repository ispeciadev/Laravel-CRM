web: php -S 0.0.0.0:$PORT -t public/ router.php
release: touch storage/installed && php artisan migrate --force && php artisan storage:link
