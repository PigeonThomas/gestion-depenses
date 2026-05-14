# Gestion Dépenses

Application web permettant de gérer les dépenses d'une famille ou d'une organisation. Suivez vos dépenses par catégorie, magasin et véhicule avec une interface intuitive.

## Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Architecture](#architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Dépannage](#dépannage)

## Fonctionnalités

- 📊 Gestion des dépenses par catégorie
- 🏪 Suivi des magasins (en ligne et physiques)
- 🚗 Gestion des véhicules
- 👥 Gestion des utilisateurs
- 📈 Visualisation et rapports des dépenses
- 🔐 Authentification sécurisée

## Architecture

Le projet est construit avec les technologies suivantes :

- **Framework** : Symfony 5+
- **Base de données** : MySQL 8.0
- **ORM** : Doctrine ORM
- **Serveur web** : Apache 2.4 avec PHP 8.2
- **Containerisation** : Docker & Docker Compose
- **Template** : Twig

### Structure du projet

```
gestion-depenses/
├── src/
│   ├── Controller/       # Contrôleurs Symfony
│   ├── Entity/          # Entités Doctrine
│   ├── Repository/      # Dépôts de données
│   └── DataFixtures/    # Données de test
├── config/              # Configuration Symfony
├── templates/           # Templates Twig
├── migrations/          # Migrations Doctrine
├── docker/              # Configuration Docker
│   ├── php/            # Dockerfile pour PHP/Apache
│   └── apache/         # Configuration Apache
├── public/             # Point d'entrée web (index.php)
└── var/               # Cache et logs (généré)
```

## Installation

### Prérequis

- Docker et Docker Compose 3.8+
- Git

### Étapes d'installation

1. **Cloner le projet**

    ```bash
    git clone <repository-url>
    cd gestion-depenses
    ```

2. **Construire les images Docker**

    ```bash
    docker-compose build
    ```

3. **Lancer les conteneurs**

    ```bash
    docker-compose up -d
    ```

4. **Installer les dépendances PHP**

    ```bash
    docker-compose exec php composer install
    ```

5. **Créer la base de données**

    ```bash
    docker-compose exec php php bin/console doctrine:database:create
    ```

6. **Exécuter les migrations**

    ```bash
    docker-compose exec php php bin/console doctrine:migrations:migrate
    ```

7. **Charger les données de test**
    ```bash
    docker-compose exec php php bin/console doctrine:fixtures:load
    ```

## Configuration

### Variables d'environnement

Le fichier `.env` contient les configurations principales :

```env
# Symfony
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=<votre-secret>

# Base de données
DATABASE_URL="mysql://app:app@db:3306/gestion_depenses?serverVersion=8.0&charset=utf8mb4"
```

### Accès à l'application

- **URL** : http://localhost:8080
- **Base de données** : localhost:3308
    - Utilisateur : `app`
    - Mot de passe : `app`
    - Base : `gestion_depenses`

### Services Docker

- **app** : Conteneur PHP/Apache (port 8080)
- **db** : Conteneur MySQL (port 3308)

## Utilisation

### Commandes utiles

```bash
# Accéder au conteneur PHP
docker-compose exec php bash

# Afficher les logs
docker-compose logs -f php
docker-compose logs -f db

# Arrêter les conteneurs
docker-compose down

# Reconstruire sans cache
docker-compose build --no-cache
```

### Gestion de la base de données

```bash
# Créer les migrations
docker-compose exec php php bin/console make:migration

# Exécuter les migrations
docker-compose exec php php bin/console doctrine:migrations:migrate

# Charger les fixtures de test
docker-compose exec php php bin/console doctrine:fixtures:load

# Afficher la structure de la BD
docker-compose exec php php bin/console doctrine:schema:update --dump-sql
```

## Dépannage

### Le conteneur PHP ne démarre pas

Vérifiez les logs :

```bash
docker-compose logs php
```

Assurez-vous que le port 8080 n'est pas déjà utilisé.

### Erreur de connexion à la base de données

1. Vérifiez que le conteneur MySQL est bien lancé : `docker-compose ps`
2. Attendez quelques secondes que MySQL finisse son initialisation
3. Testez la connexion :
    ```bash
    docker-compose exec php php bin/console doctrine:query:sql "SELECT 1"
    ```

### Les données de test ne se chargent pas

Assurez-vous que la base de données existe et que les migrations ont été appliquées avant de charger les fixtures.
