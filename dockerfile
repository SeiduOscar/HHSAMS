# Use the official PHP image as the base
FROM php:8.0-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy your PHP application code
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html

# Install Python and required Python packages
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    build-essential \
    cmake \
    && pip3 install --no-cache-dir -r requirements.txt

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 for the Apache web server
EXPOSE 80

# Start the Apache web server
CMD ["apache2-foreground"]