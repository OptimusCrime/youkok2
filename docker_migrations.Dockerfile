FROM php:7.4.11-cli

ENV TZ=Europe/Oslo

ARG ENV=prod

RUN if [ $ENV = "prod" ] ; then \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
fi ;

RUN cd /usr/src \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone \
    && echo "date.timezone = $TZ" > /usr/local/etc/php/conf.d/timezone.ini \
    && docker-php-ext-install pdo_mysql

COPY ./youkok2 /code/site

# https://github.com/ufoscout/docker-compose-wait
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.7.3/wait /wait
RUN chmod +x /wait

CMD /wait && /code/site/vendor/bin/phinx migrate -c /code/site/phinx/config.php
