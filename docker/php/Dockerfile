# Image de base : PHP 8.2 avec PHP-FPM (FastCGI Process Manager)
# FPM = PHP qui tourne en arrière-plan et attend les requêtes de Nginx
FROM php:8.2-fpm

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

# Installer Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le dossier de travail dans le conteneur
WORKDIR /var/www/html