FROM php:8.0.0-apache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get update && apt-get install -y git libzip-dev unzip \
    && docker-php-ext-install zip \
    && a2enmod rewrite headers 

COPY . /var/www/html

COPY ./config/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN cd /etc/apache2/sites-available/ && a2ensite 000-default.conf

WORKDIR /var/www/html/

RUN composer install --no-plugins --no-scripts