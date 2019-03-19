FROM php:5.6-fpm
MAINTAINER joewen "wennjoe@163.com"

# set timezome
ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install package and PHP Core extensions
RUN apt-get update && apt-get install -y \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
		libmcrypt-dev \
        libpng-dev \
		libssl-dev \
		libpcre3-dev \
		curl \
		libxml2-dev \
		cron \
		dos2unix \
		python2.7 \
		supervisor \
		&& docker-php-ext-install -j$(nproc) iconv mcrypt \
		&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
		&& docker-php-ext-install -j$(nproc) gd \
        && docker-php-ext-install zip \
        && docker-php-ext-install pdo_mysql \
        && docker-php-ext-install opcache \
        && docker-php-ext-install mysqli \
		&& docker-php-ext-install bcmath \
        && rm -r /var/lib/apt/lists/*

# Copy extensions had downloaded
COPY ./pkg/redis.tgz /home/redis.tgz

# Copy crontab to container
COPY ./start.sh /usr/local/bin/
RUN chmod 755 /usr/local/bin/start.sh \
	&& dos2unix /usr/local/bin/start.sh

# Install PECL extensions (Redis)
RUN pecl install /home/redis.tgz && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

# Install Composer
ENV COMPOSER_HOME /root/composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV PATH $COMPOSER_HOME/vendor/bin:$PATH

RUN rm -f /home/redis.tgz 

WORKDIR /data

# Write Permission
RUN usermod -u 8888 www-data
