# Sử dụng PHP 8.2 với Apache
FROM php:8.2-apache

# Cài đặt các thư viện cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Đặt thư mục làm việc trong container
WORKDIR /var/www/html

# Copy toàn bộ project vào container
COPY . .

# Cấp quyền cho storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Kích hoạt mod_rewrite trong Apache
RUN a2enmod rewrite

# Khởi động Apache
CMD ["apache2-foreground"]
