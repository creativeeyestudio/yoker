# Guide d'installation de Dream Site

Ce guide vous aidera à installer Dream Site sur votre machine locale en utilisant Symfony et Composer.

## Prérequis
Assurez-vous d'avoir les éléments suivants installés sur votre machine :

- [Composer](https://getcomposer.org/) - Gestionnaire de dépendances PHP
- [NPM](https://www.npmjs.com/) ou [Yarn](https://yarnpkg.com/) - Gestionnaire de paquets JavaScript

## Instructions d'Installation

Récupérer le projet avec Composer depuis cette ligne de commande 

`composer create-project creative-eye/cms-project nom-du-projet`

## Installation des dépendances

Lancez les lignes de commandes ci-dessous pour installer les dépendances
- `cd nom-du-projet`
- `composer install`
- `npm install` ou `yarn install`

## Configuration de la base de données

### Configurer les paramètres de la base de données

Ouvrez le fichier .env dans un éditeur de texte et modifiez les paramètres de connexion à votre base de données MySQL : `DATABASE_URL=mysql://user:password@localhost:3306/nom_de_la_base_de_donnees?serverVersion=5.7.40&charset=utf8mb4`

Remplacez `user` par le nom d'utilisateur de votre base de données MySQL, `password` par le mot de passe de votre base de données MySQL, et `nom_de_la_base_de_donnees` par le nom de la base de données que vous souhaitez utiliser.

### Création de la base de données
Une fois que vous avez configuré correctement le fichier .env, exécutez la commande Symfony pour créer la base de données à partir de la configuration du fichier.

`php bin/console doctrine:database:create`

### Création des tables
Créez ensuite les tables de la base de données

`php bin/console make:migration`

`php bin/console doctrine:migrations:migrate`

## Création de l'utilisateur Admin
Lancez la commande : `php bin/console app:create-user` et suivez les instructions demandées

## Accès à l'interface
Lancez `symfony serve`

Vous pouvez désormais accéder à l'interface d'utilisation pour commencer à gérer les contenus du site internet depuis cette URL : https://127.0.0.1/admin
