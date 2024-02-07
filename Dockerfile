FROM php:8.1.0-fpm

# Install Additional System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    npm

# Install node
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Check whether .env is existing and create if not.
RUN if [ -f .env ]; then cp .env.example .env; fi
COPY . /var/www/html


WORKDIR /var/www/html

RUN chmod -R 777 storage

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
RUN composer install
RUN npm install

RUN touch database/database.sqlite
RUN php artisan migrate
RUN npm run build

CMD php artisan serve --host 0.0.0.0