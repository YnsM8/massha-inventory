# Usamos PHP 8.2 como base, que es lo que requiere Laravel 11
FROM php:8.2-fpm

# Instalamos las dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpiamos la caché del gestor de paquetes para reducir el tamaño de la imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalamos las extensiones de PHP que necesita Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalamos y habilitamos Xdebug para el Code Coverage
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configuramos Xdebug para que solo funcione en modo cobertura (más rápido)
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Instalamos Composer (el gestor de dependencias de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecemos el directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Copiamos todos los archivos del proyecto al contenedor
COPY . /var/www

# Damos permisos al usuario del servidor web para escribir en las carpetas necesarias
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache