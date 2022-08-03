# COMPOSER
#--------------------------------------------------------------------------
FROM composer:2 as composer_stage

WORKDIR /var/www

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

COPY --from=composer_stage /var/www /var/www

WORKDIR /var/www
COPY yarn.lock package.json webpack.config.js ./
COPY assets ./assets

RUN yarn install
RUN yarn encore dev

RUN npm install


# SYMFONY
#--------------------------------------------------------------------------
FROM php:8.1-apache

RUN a2enmod rewrite
 
RUN apt-get update \
  && apt-get install -y libzip-dev git wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
 
RUN docker-php-ext-install pdo mysqli pdo_mysql zip;

COPY --from=npm_builder /var/www /var/www
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY . /var/www

WORKDIR /var/www

EXPOSE 80

CMD ["apache2-foreground", "yarn", "composer", "npm"]