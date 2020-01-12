FROM php:7.2-fpm
# PHP extensions
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    #&& php7.2-mbstring \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb 

# Install Composer
RUN curl -sL https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer
RUN composer --version

WORKDIR /var/www/app
