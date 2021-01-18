FROM php:7.4-apache

RUN apt update

RUN apt install -y \
        libicu-dev \
        wget \
        zip

RUN docker-php-ext-install intl pdo_mysql

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet

RUN a2enmod rewrite
