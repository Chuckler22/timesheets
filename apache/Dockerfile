FROM php:8.0-apache
WORKDIR /var/www/html

# COPY my-apache-site.conf /etc/apache2/sites-available/my-apache-site.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf &&\
    a2enmod rewrite &&\
    # a2dissite 000-default &&\
    # a2ensite my-apache-site &&\
    service apache2 restart
# COPY . src

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt update && apt install -y libldap2-dev && docker-php-ext-install ldap && rm -rf /var/lib/apt/lists/*
COPY composer.sh /usr/bin/composer.sh
RUN chmod +x /usr/bin/composer.sh
RUN apt update && apt install -y git


EXPOSE 80