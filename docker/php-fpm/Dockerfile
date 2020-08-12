FROM php:7.2-fpm
# PHP extensions
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libcurl4-openssl-dev\
        pkg-config \
        libssl-dev \
        #php-intl \
        libicu-dev\
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    #&& php7.2-mbstring \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Install Composer
RUN curl -sL https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer
RUN composer --version

# ICU - intl requirements for Symfony
# Debian is out of date, and Symfony expects the latest - so build from source, unless a better alternative exists(?)
#RUN curl -sS -o /tmp/icu.tar.gz -L http://download.icu-project.org/files/icu4c/58.2/icu4c-58_2-src.tgz \
#    && tar -zxf /tmp/icu.tar.gz -C /tmp \
#    && cd /tmp/icu/source \
#    && ./configure --prefix=/usr/local \
#    && make \
#    && make install
#
#RUN docker-php-ext-configure intl --with-icu-dir=/usr/local \
#    && docker-php-ext-install intl

WORKDIR /var/www/app
