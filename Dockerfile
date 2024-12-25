# Base image: PHP 8.2 with Apache web server
FROM php:8.2-apache

# Install required system libraries and tools:
# - libfreetype6-dev, libjpeg62-turbo-dev, libpng-dev: For image processing (GD extension)
# - libzip-dev: For ZIP file handling
# - imagemagick & libmagickwand-dev: For advanced image manipulation
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    imagemagick \
    libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure and install essential PHP extensions:
# - GD: For image processing
# - mysqli: For MySQL database connectivity
# - zip: For handling ZIP files
# - exif: For reading EXIF metadata from images
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    zip \
    exif

# Install and enable ImageMagick PHP extension for advanced image operations
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Enable Apache's URL rewriting module for clean URLs
RUN a2enmod rewrite

# Configure PHP settings for better performance and file handling:
# - Increase upload file size limits
# - Set higher memory limits
# - Extend execution timeouts for large operations
RUN { \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 300'; \
    echo 'max_input_time = 300'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Set web server directory permissions to Apache user
RUN chown -R www-data:www-data /var/www/html

# Set Apache server name to avoid startup warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf 