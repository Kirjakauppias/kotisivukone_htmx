FROM php:8.2-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y libmariadb-dev
# Ota käyttöön Apache mod_rewrite
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli

ENV APACHE_RUN_PORT=8080
ENV PORT=8080
EXPOSE 8080

CMD ["apache2ctl", "-D", "FOREGROUND"]

# **TÄRKEÄ: Kopioi sivuston tiedostot konttiin!**
COPY ./src/ /var/www/html/
COPY --chown=www-data:www-data ./vendor /var/www/html/vendor
RUN ls -lah /var/www/html
# **TÄRKEÄ: Aseta Apache käyttämään index.php tai index.html**
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/custom-index.conf && \
    a2enconf custom-index
RUN chmod -R 755 /var/www/html && chown -R www-data:www-data /var/www/html
