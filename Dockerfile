FROM php:8-apache
RUN a2enmod rewrite
COPY .htaccess /var/www/html/
COPY index.php /var/www/html/