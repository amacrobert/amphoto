FROM php:8.1-fpm

RUN apt-get update -y \
    && apt-get install -y \
        vim \
        git \
        zip \
        unzip \
        libxml2-dev

RUN docker-php-ext-install \
    exif \
    pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN echo 'alias ll="ls -lAh"' >> /root/.bashrc

# Symfony server
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony \
    && symfony server:ca:install

WORKDIR /var/amphoto
