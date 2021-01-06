FROM nginx:1.19.3-alpine

COPY ./_docker/site.conf /etc/nginx/conf.d/site.conf

COPY ./static /static

RUN chmod 644 /etc/nginx/conf.d/site.conf

RUN rm /etc/nginx/conf.d/default.conf
