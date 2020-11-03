ARG PHP_VERSION=7.3

FROM php:${PHP_VERSION}-cli-alpine

ARG XDEBUG_VERSION=2.9.8

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer --version

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache --virtual .runtime-deps git libzip-dev \
    && docker-php-source extract \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && mkdir -p /usr/src/php/ext/xdebug \
    && curl -fsSL https://github.com/xdebug/xdebug/archive/$XDEBUG_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/xdebug --strip 1 \
    && docker-php-ext-install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "xdebug.max_nesting_level=15000" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "xdebug.remote_enable=true" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "xdebug.remote_host=localhost" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "xdebug.idekey=PHPSTORM" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "xdebug.remote_handler=dbgp" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "xdebug.remote_autostart=1" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && echo "xdebug.remote_connect_back=0" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" \
    && docker-php-source delete \
    && apk del .build-deps
