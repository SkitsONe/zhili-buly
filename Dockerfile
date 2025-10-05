FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание пользователя для приложения
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Сначала копируем приложение
COPY --chown=www:www . /var/www

# ПОТОМ создаем папки и настраиваем права
RUN mkdir -p /var/www/bootstrap/cache /var/www/storage/framework/sessions \
    /var/www/storage/framework/views /var/www/storage/framework/cache \
    && chown -R www:www /var/www/bootstrap /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache /var/www/storage

# Смена на пользователя www
USER www

# Рабочая директория
WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]
