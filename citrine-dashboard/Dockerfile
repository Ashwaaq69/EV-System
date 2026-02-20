# Start with PHP 8.2 FPM
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    gnupg

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js (needed for Vite/npm run build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader
RUN npm install
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 8000 and start php-fpm server
EXPOSE 8000

# Entrypoint script to handle migrations and server start
RUN echo '#!/bin/bash\n\
php artisan migrate --force\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /usr/local/bin/start-app.sh && chmod +x /usr/local/bin/start-app.sh

CMD ["/usr/local/bin/start-app.sh"]
