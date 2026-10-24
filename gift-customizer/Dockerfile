FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy all project files to Apache root
COPY . /var/www/html/

# Expose port 80
EXPOSE 80

# Configure Apache port if PORT env variable is passed
CMD sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground
