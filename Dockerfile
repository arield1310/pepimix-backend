# Imagen base con Apache + PHP 8.2
FROM php:8.2-apache

# Instalar extensiones necesarias para PostgreSQL
RUN docker-php-ext-install pgsql pdo pdo_pgsql

# Copiar archivos del proyecto al contenedor
COPY . /var/www/html/

# Exponer el puerto 80
EXPOSE 80
