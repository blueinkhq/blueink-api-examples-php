FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends git libzip-dev unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip \
    && a2enmod rewrite headers \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html

COPY ./config/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN cd /etc/apache2/sites-available/ && a2ensite 000-default.conf

WORKDIR /var/www/html/

RUN composer install --no-plugins --no-scripts
