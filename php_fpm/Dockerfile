FROM php:8.2-fpm

# Install Postgres extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /var/www/html

# Docker file expects to be run with in parent dir context
COPY src /var/www/html
