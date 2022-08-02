FROM node:6-alpine
RUN npm install

# COMPOSER
#--------------------------------------------------------------------------
FROM composer:2 as composer_stage

RUN rm -rf /var/www && mkdir -p /var/www/html
WORKDIR /var/www/html

COPY composer.json composer.lock symfony.lock .env ./
COPY public public/

RUN composer install --ignore-platform-reqs --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction --no-dev --no-autoloader

RUN composer dump-autoload --optimize --apcu --no-dev

COPY bin bin/
COPY config config/
COPY src src/

RUN composer run-script $NODEV post-install-cmd; \
    chmod +x bin/console;


# NPM
#--------------------------------------------------------------------------
FROM node:12 as npm_builder

COPY --from=composer_stage /var/www/html /var/www/html

WORKDIR /var/www/html
COPY yarn.lock package.json webpack.config.js ./
COPY assets ./assets

RUN yarn install
RUN yarn encore prod


# SYMFONY
#--------------------------------------------------------------------------
FROM php:8.1-apache

RUN apt-get -y update && apt-get upgrade -y

COPY --from=npm_builder /var/www/html /var/www/html

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Enable apache modules
RUN a2enmod rewrite headers

EXPOSE 80

ENTRYPOINT ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]