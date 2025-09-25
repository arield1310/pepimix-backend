FROM php:8.1-apache

# Instalar extensión de PostgreSQL para PHP
RUN docker-php-ext-install pgsql pdo pdo_pgsql

# Copiar archivos de la app
COPY . /var/www/html/
