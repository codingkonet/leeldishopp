FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev unzip \
    && docker-php-ext-install zip \
    && a2enmod headers rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/plugins /var/www/html/themes \
    && mkdir -p /var/www/html/assets/uploads \
    && chmod -R 775 /var/www/html/plugins /var/www/html/themes /var/www/html/assets/uploads

EXPOSE 80
