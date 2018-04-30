FROM php:7.2-apache

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

COPY --chown=www-data:www-data . /var/www/blog/
WORKDIR /var/www/blog

COPY --chown=www-data:www-data install/mostUsedWords.txt.dist fileStorage/mostUsedWords.txt
COPY --chown=www-data:www-data install/posts.txt.dist fileStorage/posts.txt

RUN apt-get update
RUN apt-get install -y libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libxpm-dev \
    libvpx-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd

RUN cp install/blog_test_vhost.conf /etc/apache2/sites-available/blog_test_vhost.conf
RUN sed -e "s?%BLOG_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/blog_test_vhost.conf
RUN a2ensite blog_test_vhost.conf
RUN a2dissite 000-default.conf
RUN a2dissite default-ssl.conf
RUN a2enmod rewrite
RUN service apache2 restart

RUN composer install

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
