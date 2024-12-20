FROM php:8.2-apache

# Ajouter le script d'installation des extensions PHP
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Installer les extensions PHP et les outils nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && install-php-extensions \
    pdo_pgsql \
    intl \
    zip

# Copier le fichier de configuration VirtualHost d'Apache
COPY Docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Activer les modules Apache nécessaires
RUN a2enmod rewrite

# Nettoyer les fichiers temporaires
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
