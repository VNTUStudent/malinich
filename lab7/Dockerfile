FROM php:8-apache

ENV TZ=Europe/Kyiv

COPY site/ /var/www/html/

RUN echo "date.timezone = Europe/Kyiv" > /usr/local/etc/php/conf.d/tz.ini \
    && mkdir -p /var/www/html/survey /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/survey /var/www/html/data

EXPOSE 80
