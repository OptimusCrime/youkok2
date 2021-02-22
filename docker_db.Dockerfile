FROM mysql:8.0.20

ENV TZ=Europe/Oslo

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone

COPY _docker/youkok2.cnf /etc/mysql/conf.d/youkok2.cnf

RUN chmod 644 /etc/mysql/conf.d/youkok2.cnf
