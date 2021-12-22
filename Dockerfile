FROM php:8.0-apache

WORKDIR /var/www/html

RUN apt-get update

RUN apt-get update && apt-get install -y \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        zip \
        curl \
        unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-source delete

COPY web.conf /etc/apache2/sites-available/

RUN chgrp -R www-data /var/www/html

RUN a2dissite 000-default.conf; \
    a2ensite web.conf; \
    a2enmod rewrite

COPY . .