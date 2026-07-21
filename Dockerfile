FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    curl \
    libcurl4 \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && docker-php-ext-install json \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

RUN chmod -R 777 /var/www/html/storage

EXPOSE 80
CMD ["apache2-foreground"]
