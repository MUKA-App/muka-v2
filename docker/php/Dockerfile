FROM php:7.4.9-fpm-alpine

RUN apk update --no-cache && \
    apk add --no-cache \
    curl \
    zlib-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    freetype-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    bzip2-dev \
    icu-dev \
    jpegoptim \
    pngquant \
    optipng \
    git \
    libmemcached-dev

RUN docker-php-ext-configure gd \
        --with-webp=/usr/include/ \
        --with-jpeg=/usr/include/ \
        --with-xpm=/usr/include/ \
        --with-freetype=/usr/include/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    gd \
    xml \
    zip \
    bz2 \
    json \
    intl

RUN docker-php-source extract \
    && git clone --branch v3.1.5 https://github.com/php-memcached-dev/php-memcached /usr/src/php/ext/memcached/ \
    && docker-php-ext-install memcached \
    && docker-php-source delete
