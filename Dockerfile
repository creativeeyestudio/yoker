FROM php:8.1.1-fpm

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
 
RUN apt-get update \
    &amp;&amp; apt-get install -y --no-install-recommends locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;
 
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen &amp;&amp; \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen &amp;&amp; \
    locale-gen
 
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls &amp;&amp; \
   mv composer.phar /usr/local/bin/composer
 
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql gd opcache intl zip calendar dom mbstring zip gd xsl
RUN pecl install apcu &amp;&amp; docker-php-ext-enable apcu
 
WORKDIR /var/www/
VOLUME ["/var/www/html","/var/log/apache2","/etc/apache2/sites-enabled"]