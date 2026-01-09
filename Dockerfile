# Use PHP 8.2 with Apache or Nginx? For Laravel, better with Nginx + PHP-FPM, but for simplicity, use php:8.2-apache

FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    fontconfig \
    fonts-dejavu \
    fonts-liberation \
    fonts-noto-core \
    fonts-noto-cjk \
    fonts-crosextra-carlito \
    fonts-crosextra-caladea \
    nodejs \
    npm \
    python3 \
    python3-pip \
    libreoffice \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Increase PHP upload limits (your /api/convert-file uploads can exceed defaults)
RUN { \
        echo 'upload_max_filesize=25M'; \
        echo 'post_max_size=30M'; \
        echo 'max_file_uploads=50'; \
        echo 'memory_limit=512M'; \
    } > /usr/local/etc/php/conf.d/99-uploads.ini

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache to serve from public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader

# Install Node dependencies
RUN npm install

# Build Vue assets
RUN npm run build

# Install Python dependencies for the script
RUN pip3 install --break-system-packages comtypes || true  # comtypes is Windows only, but harmless

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/init.sh

# Expose port 80
EXPOSE 80

# Start script
CMD ["./init.sh", "apache2-foreground"]