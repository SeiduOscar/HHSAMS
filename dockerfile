# PHP + Apache Base
FROM php:8.0-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy PHP code and entire project (including your Python environment)
COPY . /var/www/html/

# Copy your prebuilt Python environment
# Replace 'venv' with the actual name of your environment folder
COPY ./venv /usr/local/venv

# Set environment variables so container uses your Python environment
ENV PATH="/usr/local/venv/bin:$PATH"
ENV PYTHONPATH="/usr/local/venv/lib/python3.9/site-packages:$PYTHONPATH"

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
