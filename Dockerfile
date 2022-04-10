FROM php:8.0-cli-buster

RUN apt-get update && \
	apt-get install -y autoconf pkg-config && \
    pecl channel-update pecl.php.net && \
    pecl install apcu && \
	docker-php-ext-enable apcu opcache

RUN echo '\
apc.enable_cli=On\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini

RUN echo '\
display_errors=On\n\
error_reporting=E_ALL\n\
date.timezone=UTC\n\
' >> /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && \
	apt-get install unzip && \
    curl -s https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet && \
    mv composer.phar /usr/local/bin/composer
