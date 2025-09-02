# -----------------------------
# Stage 1: Python dependencies
# -----------------------------
FROM python:3.9-bullseye AS python-build

# Install system dependencies for building Python packages
RUN apt-get update && apt-get install -y \
    build-essential \
    cmake \
    libglib2.0-dev \
    libsm6 \
    libxext6 \
    libxrender-dev \
    wget \
    && rm -rf /var/lib/apt/lists/*

# Upgrade pip
RUN pip install --upgrade pip

# Copy requirements and install Python packages
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# -----------------------------
# Stage 2: PHP + Apache
# -----------------------------
FROM php:8.0-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Copy your PHP application code
COPY . /var/www/html/
WORKDIR /var/www/html

# Copy prebuilt Python packages from stage 1
COPY --from=python-build /usr/local/lib/python3.9/site-packages /usr/local/lib/python3.9/site-packages
COPY --from=python-build /usr/local/bin /usr/local/bin

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Expose Apache port
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]

