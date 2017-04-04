FROM php:7.1.3-apache

ADD . /var/www/html

# Install composer
RUN cd /usr/src && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable mod_rewrite
RUN a2enmod rewrite

# Install zlib (required for Composer stuff)
RUN apt-get update && apt-get install -y zlib1g-dev

# Rewrite Apache document root location
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Install PHP extensions
RUN docker-php-ext-install zip pdo pdo_mysql

# Install dependencies
RUN cd /var/www/html && composer install

# Run migrations
#RUN php /var/www/html && php vendor/bin/phinx migrate -c config/example-phinx.yml

# Change owner of Apache root directory
RUN chown -R www-data:www-data /var/www/html