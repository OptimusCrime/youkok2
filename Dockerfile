FROM php:7.3.5-apache

ENV TZ=Europe/Oslo
ENV PHPREDIS_VERSION 3.1.4

ARG YOUKOK_ENV=dev

COPY ./docker/config/${YOUKOK_ENV}/default.conf /etc/apache2/sites-enabled/default.conf
COPY ./docker/cron_job /usr/local/bin/cron_job

RUN mkdir /volume_data \
    && usermod -u 1000 www-data \
    && groupmod -g 1000 www-data \
    && usermod -a -G www-data root \
    && usermod -a -G root www-data \
    && usermod -a -G staff www-data \
    && usermod -a -G staff root \
    && chown -R www-data:www-data /volume_data \
    && chown -R www-data:www-data /var/www/html

COPY ./youkok2 /var/www/html

RUN if [ $YOUKOK_ENV = "prod" ] ; then \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
fi ;

RUN cd /usr/src \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone \
    && a2enmod rewrite \
    && a2enmod headers \
    && a2enmod expires \
    && a2dissite 000-default \
    && apt-get update \
    && apt-get install -y --fix-missing apt-utils gnupg \
    && echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list \
    && echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list \
    && curl -sS --insecure https://www.dotdeb.org/dotdeb.gpg | apt-key add - \
    && apt-get update \
    && apt-get install -y zlib1g-dev libzip-dev zip \
    && docker-php-ext-install zip pdo_mysql \
    && mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis \
    && service apache2 restart \
    && chmod u+x /usr/local/bin/cron_job

RUN if [ $YOUKOK_ENV = "dev" ] ; then \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
fi ;
