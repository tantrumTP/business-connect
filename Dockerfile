FROM richarvey/nginx-php-fpm:latest

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy deployment script
COPY scripts/deploy.sh /usr/local/bin/deploy.sh

# Make the deployment script executable
RUN chmod +x /usr/local/bin/deploy.sh

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set the web root to the public directory
ENV WEBROOT /var/www/html/public

# Expose port 80 for NGINX
EXPOSE 80

# Run the deployment script during container startup
CMD ["/usr/local/bin/deploy.sh"]
