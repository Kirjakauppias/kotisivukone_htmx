FROM php:8.2-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y curl unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd
RUN docker-php-ext-install exif && docker-php-ext-enable exif
# Ota käyttöön Apache mod_rewrite
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli

ENV APACHE_RUN_PORT=8080
ENV PORT=8080
EXPOSE 8080

CMD ["apache2ctl", "-D", "FOREGROUND"]

# **TÄRKEÄ: Kopioi sivuston tiedostot konttiin!**
COPY ./src/ /var/www/html/
COPY composer.json /var/www/html/
# Kopioidaan oma php.ini asetustiedosto konttiin
COPY config/php.ini /usr/local/etc/php/conf.d/custom.ini
RUN composer install --no-dev --optimize-autoloader
RUN ls -lah /var/www/html
# **TÄRKEÄ: Aseta Apache käyttämään index.php tai index.html**
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/custom-index.conf && \
    a2enconf custom-index
RUN chmod -R 755 /var/www/html && chown -R www-data:www-data /var/www/html
