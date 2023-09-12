# Use an official PHP runtime as a parent image
FROM php:7.4-apache

# Copy your PHP application files to the container
COPY ./site/ /var/www/html/

# Change ownership and permissions of a directory to root
RUN chmod -R 777 /var/www/html/
RUN chmod -R 777 /var/www/html/log/

# Installez l'extension MySQLi
RUN docker-php-ext-install mysqli

# Set environment variable to "prod"
ENV APP_ENV=prod

# # Configure PHP settings for production
# RUN echo "error_reporting = E_ERROR | E_PARSE" > /usr/local/etc/php/php.ini \
#     && echo "display_errors = Off" >> /usr/local/etc/php/php.ini \
#     && echo "log_errors = On" >> /usr/local/etc/php/php.ini \
#     && echo "error_log = /var/log/php/php_error.log" >> /usr/local/etc/php/php.ini


# Expose port 80 for the Apache web server
EXPOSE 80

# Start the Apache web server
CMD ["apache2-foreground"]
