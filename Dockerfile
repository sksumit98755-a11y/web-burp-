FROM php:8.2-apache

RUN apt-get update && apt-get install -y curl \
    && docker-php-ext-install curl json \
    && a2enmod rewrite

COPY . /var/www/html/

RUN chmod -R 777 /var/www/html/storage

EXPOSE 80
CMD ["apache2-foreground"]
