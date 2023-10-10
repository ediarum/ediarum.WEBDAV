ARG DB_DATABASE
ARG DIRECTORY=.

FROM node:16 as frontend
ARG DIRECTORY
WORKDIR /app
COPY $DIRECTORY .
RUN npm install
RUN npm run build


FROM php:8.1.12-fpm as main

ARG DB_DATABASE
ARG DIRECTORY

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update -y && apt-get install -y openssl zip unzip git libxslt1-dev
RUN docker-php-ext-install xsl
RUN echo 'pm.max_children = 25' >> /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /app
COPY $DIRECTORY .
COPY --from=frontend /app/public ./public
RUN composer install
RUN chown www-data:www-data /app /app/users.sqlite
RUN chown -R www-data:www-data /app/storage

