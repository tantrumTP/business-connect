FROM richarvey/nginx-php-fpm:latest

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create symbolic link for storage
RUN php artisan storage:link

# Run migrations
RUN php artisan migrate --force

# Expose port 80 for NGINX
EXPOSE 80

CMD ["/start.sh"]
