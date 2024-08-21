
# Use a imagem base do PHP com Apache
FROM php:7.0-apache

# Atualize e instale pacotes adicionais se necessário
RUN apt-get update && apt-get install -y \
    vim \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Adicione a configuração ServerName
RUN echo "ServerName laravel-app.local" >> /etc/apache2/apache2.conf

# Copie o código do seu aplicativo para o contêiner
COPY src/ /var/www/html/

# Exponha a porta 80
EXPOSE 80

# Comando para iniciar o Apache em primeiro plano
CMD ["apache2-foreground"]