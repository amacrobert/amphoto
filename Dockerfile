FROM php:7.4-fpm

RUN apt-get update -y \
    && apt-get install -y \
        vim \
        git \
        zip \
        unzip \
        libxml2-dev

RUN docker-php-ext-install \
    xmlrpc \
    exif \
    pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN echo 'alias ll="ls -lAh"' >> /root/.bashrc

WORKDIR /var/amphoto
