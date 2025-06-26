FROM php:8.2-fpm

# Dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libxml2-dev \
    libonig-dev \
    libpq-dev

# PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    intl \
    gd \
    opcache \
    mbstring \
    xml

RUN pecl install redis \
apcu
RUN docker-php-ext-enable redis
RUN docker-php-ext-enable apcu

COPY apcu.ini /usr/local/etc/php/conf.d/
RUN echo "extension=apcu.so" > /usr/local/etc/php/php.ini

# Composer instalation
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
# RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN chmod +x /usr/local/bin/symfony

WORKDIR /var/www/html

# Zwiększenie limitów dla PHP
RUN echo "memory_limit=1024M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/memory-limit.ini

CMD ["php-fpm"]

EXPOSE 9000