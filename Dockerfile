FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Install Node.js & NPM (so Vite can build your Tailwind CSS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install PHP extensions required by Laravel
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite (required for Laravel routing)
RUN a2enmod rewrite

# Allow Laravel .htaccess rewrite rules inside /public
RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Update Apache Document Root to Laravel's /public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy your application code into the server
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and bundle your assets
RUN npm install
RUN npm run build

# Create a blank SQLite database file so Laravel doesn't crash on boot
RUN mkdir -p database && touch database/database.sqlite

# Run the migrations to actually build the tables (LIKE THE SESSIONS TABLE!)
RUN php artisan migrate --force

# Give the server permission to write to storage and database folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose port 80 for Render to route traffic
EXPOSE 80