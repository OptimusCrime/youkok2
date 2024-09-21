FROM php:8.3.11-fpm

ENV TZ=Europe/Oslo
ENV PHPREDIS_VERSION=6.0.2

ARG ENV=prod

RUN cd /usr/src \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone \
    && echo "date.timezone = $TZ" > /usr/local/etc/php/conf.d/timezone.ini \
    && apt-get update \
    && apt-get install -y zlib1g-dev libzip-dev zip cron libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql \
    && mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis \
    && sed -i 's/#EXTRA_OPTS=""/EXTRA_OPTS="-l"/g' /etc/default/cron

RUN if [ $ENV = "prod" ] ; then \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
fi ;

# Override the entrypoint file
COPY ./_docker/docker-php-entrypoint /usr/local/bin/docker-php-entrypoint

COPY ./youkok2 /code/site
COPY ./_docker/cronjob /etc/cron.d/cronjob
COPY ./_docker/files /code/files

RUN chmod 775 /usr/local/bin/docker-php-entrypoint
