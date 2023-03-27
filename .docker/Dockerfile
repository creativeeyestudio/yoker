FROM php:8.2-apache

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_pgsql pdo_mysql intl zip mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --disable_tls --install-dir=/usr/local/bin --filename=composer

RUN apt update && apt install -yqq nodejs npm

COPY . /var/www/

COPY ./.docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN cd /var/www && \
    composer install && \
    npm install --force
    
WORKDIR /var/www/

ENTRYPOINT ["bash", "./.docker/docker.sh"]

EXPOSE 80