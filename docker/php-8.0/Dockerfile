FROM php:8.0-apache

# Install 'mysql' client which is required by WP CLI
RUN apt-get update && \
    apt-get install -y less mariadb-client

RUN set -ex; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg-dev \
        libmagickwand-dev \
        libpng-dev \
        libzip-dev \
    ; \
    \
    docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    ; \
    docker-php-ext-install -j "$(nproc)" \
        bcmath \
        exif \
        gd \
        mysqli \
        opcache \
        pdo_mysql \
        zip \
    ; \
    pecl install xdebug \
    ; \
    docker-php-ext-enable opcache \
    ; \
    docker-php-ext-enable xdebug \
    ;

# Install WP CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod a+x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp && \
    mkdir -p /var/www/.wp-cli && \
    chown -R www-data:www-data /var/www/.wp-cli && \
    chmod -R ug+wx /var/www/.wp-cli

# Install Composer
RUN curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copy installer
COPY /docker/install/ /var/www/install/
RUN chmod -R ug+wx /var/www/install && \
    chown -R www-data:www-data /var/www/install && \
    a2enmod headers rewrite ssl

RUN mkdir -p /var/www/html/wp-content/plugins/borlabs-cookie && \
    chown -R www-data:www-data /var/www/html/wp-content && \
    chown -R www-data:www-data /var/www/html/wp-content/plugins

# Supress Apache2 notice message
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY /docker/php-8.0/entrypoint.sh /var/www/

ENTRYPOINT ["/var/www/entrypoint.sh"]
