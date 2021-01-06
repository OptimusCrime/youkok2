FROM php:7.4.11-fpm

ENV TZ=Europe/Oslo
ENV PHPREDIS_VERSION=5.3.1

ARG ENV=prod

RUN if [ $ENV = "prod" ] ; then \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
fi ;

RUN cd /usr/src \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone \
    && apt-get update \
    && apt-get install -y --fix-missing apt-utils gnupg \
    && echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list \
    && echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list \
    && curl -sS --insecure https://www.dotdeb.org/dotdeb.gpg | apt-key add - \
    && apt-get update \
    && apt-get install -y zlib1g-dev libzip-dev zip cron \
    && docker-php-ext-install zip pdo_mysql \
    && mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

# Override the entrypoint file
COPY ./_docker/docker-php-entrypoint /usr/local/bin/docker-php-entrypoint

COPY ./youkok2 /code/site
COPY ./_docker/cronjob /etc/cron.d/cronjob
COPY ./_docker/cache /code/cache
COPY ./_docker/files /code/files

RUN chmod 775 /usr/local/bin/docker-php-entrypoint
