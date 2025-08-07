web: chmod +x start.sh && ./start.sh
release: php artisan migrate --force && php artisan db:seed --class=AdminUserSeeder --force && php artisan config:cache
