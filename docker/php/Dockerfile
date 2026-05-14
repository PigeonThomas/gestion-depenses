# Image de base : PHP 8.2 avec Apache
# Apache = serveur web qui sert les fichiers PHP
FROM php:8.2-apache

# Installer les dépendances système nécessaires
# mise à jour des paquets
RUN apt-get update && apt-get install -y \
    # installation de git (nécessaire pour Composer, le gestionnaire de dépendances PHP)
    git \          
    # installation de unzip (nécessaire pour Composer)
    unzip \        
    # installation de curl (pour télécharger des outils)
    curl \         
    # installation de libicu-dev (pour l'extension intl, internationalisation Symfony)
    libicu-dev \
    # nettoyer pour alléger l'image   
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP requises par Symfony
RUN docker-php-ext-install \
    # connexion à MySQL
    pdo_mysql \
    # gestion des langues/formats de date
    intl           

# Active le module Apache "rewrite" — indispensable pour Symfony
RUN a2enmod rewrite

# Copie de la config Apache dans le conteneur
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Installer Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le dossier de travail dans le conteneur
WORKDIR /var/www/html