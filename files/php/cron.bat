@echo off
cd D:\yunzhong-dockerfiles\files\php\
d:
docker-compose run --rm php-fpm php /data/www/addons/yun_shop/artisan cron:run