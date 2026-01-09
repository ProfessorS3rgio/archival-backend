# FROM php:8.2-apache
FROM php:8.2-apache-bookworm
# Install system dependencies & fonts
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
    ttf-mscorefonts-installer \
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

# Optional custom licensed fonts (DO NOT COMMIT)
COPY docker/fonts/ /usr/local/share/fonts/custom/

# Rebuild font cache
RUN fc-cache -f -v

# LibreOffice stable profile (important)
RUN mkdir -p /var/lib/libreoffice-profile \
    && chown -R www-data:www-data /var/lib/libreoffice-profile

# PHP upload limits
RUN { \
        echo 'upload_max_filesize=25M'; \
        echo 'post_max_size=30M'; \
        echo 'max_file_uploads=50'; \
        echo 'memory_limit=512M'; \
    } > /usr/local/etc/php/conf.d/99-uploads.ini

# Enable Apache rewrite
RUN a2enmod rewrite

# Apache public root
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader
RUN npm install && npm run build

RUN pip3 install --break-system-packages comtypes || true

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/init.sh

EXPOSE 80

CMD ["./init.sh", "apache2-foreground"]
