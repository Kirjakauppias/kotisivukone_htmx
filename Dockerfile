FROM php:8.0-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y libmariadb-dev
RUN docker-php-ext-install mysqli

# **TÄRKEÄ: Kopioi sivuston tiedostot konttiin!**
COPY . /var/www/html/

# **TÄRKEÄ: Aseta Apache käyttämään index.php tai index.html**
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/custom-index.conf && \
    a2enconf custom-index