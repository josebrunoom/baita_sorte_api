# Use a imagem oficial do PHP
FROM php:8.2-apache

# 2. apache configs + document root
RUN echo "ServerName laravel-app.local" >> /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

# 4. start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Atualize os pacotes e instale as extensões necessárias do PHP
RUN apt-get update

# 1. development packages
RUN apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libzip-dev \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++ \
    wget

RUN apt-get install -y \
    libaio1 \
    libaio-dev

RUN docker-php-ext-install pdo pdo_mysql zip gd

# Configure a diretiva de data.timezone no php.ini
RUN echo "date.timezone = America/Sao_Paulo" > /usr/local/etc/php/conf.d/timezone.ini

# Configure o caminho da aplicação no container
WORKDIR /var/www/html

# Copie o código da aplicação para o container
COPY . .

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install oracle instantclient and php extensions
RUN mkdir /opt/oracle
ADD infra/docker/oracle /opt/oracle

# Install oracle instantclient
RUN cd /opt/oracle && \
    wget https://conecta-santa-sdk.s3.amazonaws.com/instantclient-basic-linuxx64.zip

RUN  unzip /opt/oracle/instantclient-sdk-linuxx64.zip -d /opt/oracle \
    &&  unzip /opt/oracle/instantclient-basic-linuxx64.zip -d /opt/oracle \
    &&  rm -rf /opt/oracle/*.zip

ENV LD_LIBRARY_PATH /opt/oracle/instantclient_23_4:${LD_LIBRARY_PATH}

# Install oracle php extensions
RUN echo 'instantclient,/opt/oracle/instantclient_23_4' | pecl install oci8-3.3.0 && docker-php-ext-enable oci8 && \
        docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_23_4,23.4 \
        && docker-php-ext-install pdo_oci

# Instale as dependências usando o Composer
RUN composer install

# 6. we need a user with the same UID/GID with host user
# so when we execute CLI commands, all the host file's permissions and ownership remains intact
# otherwise command from inside container will create root-owned files and directories
RUN useradd -G www-data,root -u 1000 -d /home/devuser devuser
RUN mkdir -p /home/devuser/.composer && \
    chown -R devuser:devuser /home/devuser

COPY --chown=www-data:www-data . /var/www/html/

RUN chmod -R 777 /var/www/html/storage/logs/

EXPOSE 80