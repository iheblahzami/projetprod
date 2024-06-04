## Use an official PHP runtime as a parent image
#FROM php:8.0-cli
#
## Set the working directory in the container
#WORKDIR /app
#
## Install system dependencies
#RUN apt-get update && apt-get install -y \
#  git \
#  unzip \
#  libpq-dev \
#  libzip-dev \
#  curl \
#  netcat \
#  && docker-php-ext-install pdo_mysql zip \
#  && rm -rf /var/lib/apt/lists/*
#
## Install Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#
## Copy Composer files and install dependencies
#COPY composer.json composer.lock ./
#RUN composer install --no-scripts --no-autoloader --prefer-dist --no-progress --no-interaction
#
## Copy the rest of the application code
#COPY . /app
#
## Install Composer autoloader
#RUN composer dump-autoload --optimize
#
## Set correct permissions for var and public directories
#RUN mkdir -p var/cache var/log public && \
#    chown -R www-data:www-data var public && \
#    chmod -R 775 var public
#
## Expose port 8000 to the outside world
#EXPOSE 8001
#
## Start PHP's built-in web server
#CMD ["php", "-S", "0.0.0.0:8001", "-t", "public"]
# Use the official PHP image with Apache
FROM php:7.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Enable Apache modules
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www/html
COPY --chown=www-data:www-data . /var/www/html

# Ensure proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/var

# Install Symfony PHP framework dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

# Optimize Symfony
RUN php bin/console cache:warmup

# Expose port 8001
CMD ["apache2ctl", "-D", "FOREGROUND", "-e", "info", "-D", "PORT=8001"]
