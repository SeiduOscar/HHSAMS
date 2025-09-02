# Stage 1: Python build with prebuilt dlib/face_recognition
FROM python:3.9-slim AS python-build

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    cmake \
    libglib2.0-dev \
    libsm6 \
    libxext6 \
    libxrender-dev \
    && rm -rf /var/lib/apt/lists/*

# Copy and install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir --only-binary :all: -r requirements.txt

# Stage 2: PHP web server
FROM php:8.0-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy your PHP code
COPY . /var/www/html/
WORKDIR /var/www/html

# Copy the prebuilt Python packages from stage 1
COPY --from=python-build /usr/local/lib/python3.9/site-packages /usr/local/lib/python3.9/site-packages

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
