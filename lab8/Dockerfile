FROM php:8.4-apache
ENV TZ=Europe/Kyiv LAB7_FUNCTIONS=/opt/lab7/functions.php
COPY lab7/site/functions.php /opt/lab7/functions.php
COPY lab8/site/ /var/www/html/
RUN printf 'date.timezone = Europe/Kyiv\ndisplay_errors = Off\nlog_errors = On\n' > /usr/local/etc/php/conf.d/lab8.ini
EXPOSE 80
