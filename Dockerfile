FROM httpd:alpine3.20@sha256:741553a657df26d0adb4e6403c0da1700fbb0dd4e0544a8e01eeea3e7a4c592b

USER root

# Update the system
RUN apk update

# Extract PHP source and install extensions
RUN apk add --no-cache \
    php82 \
    php82-curl \
    php82-ctype \
    php82-iconv \
    php82-xml  \
    php82-simplexml \
    php82-dom \
    php82-fileinfo \
    php82-mbstring \
    php82-intl \
    php82-pdo \
    php82-pdo_pgsql \
    php82-openssl \
    php82-session \
    php82-tokenizer \
    php82-apache2 \
    php82-xmlwriter \
    php82-phar \
    git \
    openrc \
    supervisor \
    libpq-dev \
    libzip-dev \
    curl


RUN mv /usr/bin/php82 /usr/bin/php

# Set the timezone
ENV TZ=UTC+1
RUN apk add --no-cache tzdata && ln -sf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY setup-files/php.ini /etc/php82/php.ini

# Club application installation
RUN git clone https://github.com/SteveHoareau18/mhbc-club.git /var/www/html/mhbc

RUN mkdir /usr/tmp
RUN cd /usr/tmp/

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/bin/composer


# Set the working directory
WORKDIR /var/www/html/mhbc

COPY setup-files/httpd.conf /usr/local/apache2/conf/httpd.conf
RUN chmod 644 /usr/local/apache2/conf/httpd.conf


WORKDIR /var/www/html/mhbc

RUN composer install

RUN composer require --dev symfony/maker-bundle
RUN chmod +x bin/console
RUN chmod -R 775 var/
# Clean up Composer
#RUN rm -f /usr/local/bin/composer

# Set permissions
RUN chown -R root:apache /var/www/html/mhbc

EXPOSE 80