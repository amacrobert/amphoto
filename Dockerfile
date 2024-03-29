FROM php:8.2-fpm

RUN apt-get update -y \
    && apt-get install -y \
        vim git zip unzip libxml2-dev imagemagick libicu-dev

RUN docker-php-ext-configure intl
RUN docker-php-ext-install exif pdo_mysql intl
RUN echo 'memory_limit = 2G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN echo 'alias ll="ls -lAh"' >> /root/.bashrc

# Symfony server
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt install -y symfony-cli

RUN symfony server:ca:install

WORKDIR /var/amphoto
