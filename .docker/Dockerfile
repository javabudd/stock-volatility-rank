FROM php:7.4-apache

RUN apt update

RUN apt install -y \
        libicu-dev \
        zip

RUN docker-php-ext-install intl
