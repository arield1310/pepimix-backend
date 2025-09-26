# Imagen base con Apache + PHP 8.2
FROM php:8.2-apache

# Instalar dependencias necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql

# Copiar archivos del proyecto al contenedor
COPY . /var/www/html/

# Exponer puerto
EXPOSE 80
