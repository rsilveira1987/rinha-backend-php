FROM php:8.2

WORKDIR /var/www/html

COPY ./app /var/www/html

# RUN apt-get update && apt-get install -y \
#     libapache2-mod-fcgid

# RUN apk add --update --no-cache libgd libpng-dev libjpeg-turbo-dev freetype-dev

RUN apt-get update && apt-get install -y \
        libzip-dev \
        libonig-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpq-dev \
        vim \
        git \
    && rm -rf /var/lib/apt/lists/*



# RUN pecl install redis && docker-php-ext-enable redis
# RUN docker-php-ext-configure gd \
#         --with-freetype=/usr/include/ \
#         --with-jpeg=/usr/include/

# configure extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

# PHP SWOOLE
RUN pecl install swoole

# Add and Enable PHP-PDO Extenstions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    mbstring

RUN docker-php-ext-enable pdo pdo_pgsql mbstring zip swoole

# Use the default production configuration
# RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
#COPY ./session.ini /usr/local/etc/php/conf.d/
#COPY ./php.ini /usr/local/etc/php/conf.d/

# Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove Cache
RUN rm -rf /var/cache/apk/*

# # Copy conf files
# #COPY ./ports.conf /etc/apache2/ports.conf
# COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# # Install apache
# RUN apt-get update && apt-get install -y \
#         libapache2-mod-fcgid \
#         apache2 \
#     && rm -rf /var/lib/apt/lists/*

# # Configure apache
# ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
# ENV APACHE_RUN_USER=www-data
# ENV APACHE_RUN_GROUP=www-data
# ENV APACHE_LOG_DIR=/var/log/apache2
# RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
# RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# RUN a2enmod rewrite
# RUN chown -R www-data:www-data /var/www

# Update composer
# RUN composer --working-dir ./public update --no-interaction
# RUN composer update --no-interaction

# Add UID '1000' to www-data
# RUN usermod -u 1000 www-data

# # Change current user to www
# USER www-data

# CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
CMD ["php", "/var/www/html/public/index.php", "start"]

  