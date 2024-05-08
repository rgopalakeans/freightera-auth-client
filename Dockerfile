FROM php:7.2.34-alpine

ENV PHP_MEMORY_LIMIT=1G
ENV COMPOSER_ALLOW_SUPERUSER=1

MAINTAINER "Kirill Treshchenkov <kirill@freightera.com>"

WORKDIR /var/www/html

RUN apk update && apk add --no-cache \
    linux-headers \
    autoconf \
    bash \
    shadow \
    automake \
    make \
    gcc \
    g++ \
    pkgconfig \
    libtool \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    zlib-dev \
    libxslt-dev \
    libzip-dev \
    openssh

# Installing bash
RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install -j$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) exif bcmath xsl zip opcache pcntl sockets
