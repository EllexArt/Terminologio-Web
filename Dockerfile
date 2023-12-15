FROM php:8.3-apache
RUN apt-get -y update && apt-get -y install \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get -y autoremove
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt install symfony-cli
RUN docker-php-ext-install pdo_mysql opcache \
    && docker-php-ext-enable pdo_mysql opcache \
    && echo ServerName 0.0.0.0 >> /etc/apache2/apache2.conf
WORKDIR /app
COPY . /app
RUN bash -c "cd /app && mv composer.phar /usr/local/bin/composer  \
    && composer install  \
    && symfony server:ca:install \
    && mkdir /save \
    && mkdir /save/vendor && mkdir /save/var \
    && mv vendor/* /save/vendor \
    && mv var/* /save/var"
###> recipes ###
###< recipes ###
EXPOSE 8000