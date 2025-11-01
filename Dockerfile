# Empezar desde la imagen oficial de PHP 8.1 con Apache
FROM php:8.1-apache

# Instalar las extensiones de PHP que tu app probablemente necesita (MySQL)
# y herramientas como git y zip.
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
&& docker-php-ext-install pdo pdo_mysql

# Instalar Composer (el manejador de paquetes de PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar todos los archivos de tu proyecto al directorio web de Apache
COPY . /var/www/html/

# Movernos a ese directorio
WORKDIR /var/www/html

# Correr composer install para descargar "phpdotenv" (de tu composer.json)
RUN composer install --no-dev --optimize-autoloader

# Habilitar mod_rewrite (para URL amigables) Y mod_headers (PARA CORS)
RUN a2enmod rewrite
RUN a2enmod headers

# === ¡EL ARREGLO FINAL! ===
# FORZAR (-f) el "acceso directo" para que SOBREESCRIBA el .env de GitHub
RUN ln -sf /etc/secrets/.env /var/www/html/.env
