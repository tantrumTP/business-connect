FROM richarvey/nginx-php-fpm:latest

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create symbolic link for storage
RUN php artisan storage:link

# Set the web root to the public directory
ENV WEBROOT /var/www/html/public

# Expose port 80 for NGINX
EXPOSE 80

CMD ["/start.sh"]
