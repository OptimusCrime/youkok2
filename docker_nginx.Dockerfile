FROM nginx:1.19.3-alpine

COPY ./_docker/site.conf /etc/nginx/conf.d/site.conf
COPY ./static /static

RUN chmod 644 /etc/nginx/conf.d/site.conf \
    && rm /etc/nginx/conf.d/default.conf

RUN if [ $ENV = "prod" ] ; then \
    sed -i 's/youkok2-php/youkok2-prod-php/g' /etc/nginx/conf.d/default.conf ; \
fi ;
