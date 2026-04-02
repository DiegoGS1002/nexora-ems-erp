FROM php:8.2-apache

# Instalar dependências
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar projeto
COPY . /var/www/html

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html

# Ativar mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html
