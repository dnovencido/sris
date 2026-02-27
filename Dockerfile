FROM php:8.0-apache

WORKDIR /var/www/html

# Install required system libraries
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli \
    && docker-php-ext-enable mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
    
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini