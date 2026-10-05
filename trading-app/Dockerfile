FROM php:8.2-apache

RUN docker-php-ext-install mysqli

COPY . /var/www/html/

EXPOSE 80
git add Dockerfile
git commit -m "Add Dockerfile"
git push
