FROM php:8.2.7-apache

COPY ./src/ /usr/src/myapp

WORKDIR /usr/src/myapp

# install app dependencies
RUN apt-get update && apt-get upgrade -y
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli