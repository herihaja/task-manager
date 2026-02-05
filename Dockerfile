FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Expose port
EXPOSE 8000

CMD ["bash", "-c", "php artisan serve --host=0.0.0.0 --port=8000"]
