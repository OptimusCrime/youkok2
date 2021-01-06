FROM mysql:8.0.20

COPY _docker/youkok2.cnf /etc/mysql/conf.d/youkok2.cnf

RUN chmod 644 /etc/mysql/conf.d/youkok2.cnf
