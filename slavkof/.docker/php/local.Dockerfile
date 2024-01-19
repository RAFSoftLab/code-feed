FROM php:8.1.2-fpm

# Opcache configurations.
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="40000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="2048" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

WORKDIR /var/www/html

# Getting the dependencies
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
       apt-utils \
       autoconf \
       build-essential \
       git \
       libc-dev \
       libfreetype6-dev \
       libjpeg-dev \
       libjpeg62-turbo-dev \
       libonig-dev \
       libpng-dev \
       libreadline-dev \
       libssl-dev \
       libzip-dev \
       pkg-config \
       unzip \
       zlib1g-dev \
       openssh-client \
       cron \
    && docker-php-ext-install gd \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath

# Turn off annoying fpm access logging
RUN perl -pi -e 's#^(?=access\.log\b)#;#' /usr/local/etc/php-fpm.d/docker.conf

# Override with custom opcache settings
COPY .docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
# Copy php.ini
COPY .docker/php/php.ini /usr/local/etc/php/php.ini
COPY .docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Install composer
RUN  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install node
RUN curl -sL https://deb.nodesource.com/setup_16.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt install -y nodejs \
    && rm nodesource_setup.sh
