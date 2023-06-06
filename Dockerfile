FROM php:8.1-apache

RUN apt-get update -y
RUN apt-get install -y libmcrypt-dev zlib1g-dev libicu-dev g++ libzip-dev unzip

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl pdo pdo_mysql zip opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY . /var/www/html

RUN composer install

EXPOSE 8000

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
CMD symfony server:start
