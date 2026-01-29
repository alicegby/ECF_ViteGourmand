# VITE & GOURMAND

## Sommaire
- [Description](#description)
- [Technologies utilisées](#technologies-utilisées)
- [Environnement de travail](#environnement-de-travail)
- [Architecture du projet](#architecture-du-projet)
- [Fonctionnalités](#fonctionnalités)
- [Installation](#installation-du-projet)
- [Tests](#tests)
- [Branches Git / Workflow](#branches-git--workflow)
- [Structure du projet](#structure-du-projet)
- [Aperçu](#aperçu)
- [Roadmap / Améliorations futures](#roadmap--améliorations-futures)
- [Auteur](#auteur)

---

## Description
**Vite & Gourmand** est un projet réalisé dans le cadre de mon Examen en Cours de Formation (*ECF*), lors de ma formation de *Graduate Développeur Web et Web Mobile* chez ***Studi***. 
Il s'agit d'une application web full stack, pour le compte du traiteur **Vite & Gourmand**. L’objectif est de permettre aux clients de commander les menus plus facilement tout en augmentant la visibilité de l’entreprise pour attirer de nouveaux clients.

---

## Technologies utilisées

### Outils
- Docker / Composer
- Git / GitHub
- Symfony CLI

### Backend
- PHP 8.4.14
- Symfony 5.16.1
- Doctrine ORM
- Twig
- MySQL / MariaDB

### Frontend
- HTML
- CSS
- JavaScript

---

## Environnement de travail
- Système d'exploitation : macOS Sequoia 15.6.1
- Serveur local : Symfony CLI
- IDE : VS Code
- Navigateur de test : Chrome

---

## Architecture du projet

### Backend
Le backend est développé avec Symfony et gère toutes les fonctionnalités liées à l'administration et à la base de données : 
- CRUD des employés
- Gestion des données avec Doctrine ORM
- Chargement de données de test via fixtures
- Contrôleurs et routes pour les différentes fonctionnalités
- Templates Twig pour les pages administratives

### Frontend
Le frontend fournit l'interface utilisateur accessible aux clients, employés et administrateurs : 
- Pages listant les menus, plats et options (fromages, boissons, matériel et personnel)
- Formulaire de commande en ligne (panier) et modification des commandes
- Dashboard client
- Dashboard employés et administrateurs
- Page de contact
- Page d'inscription et de connexion
- Pages Footer (CGV, Politique de Confidentialité, FAQ, Avis, Mentions Légales)
- Application web responsive, adaptée aux mobiles et tablettes

---

## Fonctionnalités

### Backend
- Gérer les employés (Créer, Lire, Modifier, Supprimer), par les administrateurs
- Interface d'administration pour consulter et modifier les données (menus, plats, horaires...), par les employés et administrateurs
- Gestion des rôles ou permissions, par les administrateurs 
- Gestion de la base de données via Doctrine
- Validation des formulaires 
- Chargement de données de test via fixtures

### Frontend
- Afficher les menus, plats et options aux clients
- Permettre la prise de commande en ligne (sans paiement en ligne implémenté) ainsi que la modification de commande si son statut est "En attente"
- Interface utilisateur (client), employés et administrateurs
- Interface intuitive et responsive

---

## Installation du projet
1. Cloner le dépôt
    git clone https://github.com/alicegby/ECF_ViteGourmand.git 

2. Se déplacer dans le dossier du projet
    cd /Applications/MAMP/htdocs/ECF

3. Installation des dépendances avec Composer
    composer install

4. Configure le fichier .env.local
    - Renseigner les informations de connexion à la base de données relationnelle (DATABASE_URL), à MongoDB (MONGODB_URL) et à l'envoie de mail automatique (MAILER_DSN)
    DEFAULT_URI=http://localhost
    DATABASE_URL="mysql://root:root@vite_gourmand_mysql:3306/vite_gourmand?serverVersion=8.0"
    MAILER_DSN="smtp://viteetgourmand%40gmail.com:uehaqsggktnxulvd@smtp.gmail.com:465?encryption=ssl&auth_mode=login"
    MONGODB_URL="mongodb://admin:admin123@mongo:27017"  

5. Création de la base de données
    php bin/console doctrine:database:create

6. Mettre à jour le schéma de la base de données
    php bin/console doctrine:migrations:migrate

7. Charger les entités
    php bin/console doctrine:schema:update --force

8. Charger les fixtures 
    php bin/console doctrine:fixtures:load (utilisation de --append si besoin de recharger de nouvelles données, sans écraser les précédentes)

9. Accéder au projet 
    - Lancer le serveur Symfony CLI : symfony server:start
    - Puis accéder au projet dans le navigateur : http://127.0.0.1:8000
    - Pour visionner la base de données sur phpMyAdmin via MAMP : http://localhost:8888/

---
## Tests
- Cypress : pour tester le parcours utilisateur complet : pages, formulaires et commandes
- Commandes pour lancer les tests : 
    - npx cypress open (lancer l'interface interactive)
    - npx cypress run (lancer tous les test en CLI)

---

## Branches Git

### Branche main
- Version stable du code

### Branche develop
- Branche de modification du code
- Une fois validé, migration vers la branche main

---

## Structure du projet

### Backend
- src/Controller : contrôleurs Symfony qui gèrent les requêtes et la logique métier
- src/Entity : entités Doctrine (modèles de données)
- src/Repository : classes pour interagir avec la base de données
- src/Form : formulaires Symfony pour la saisie et la validation des données
- templates/ : vues Twig pour les pages administratives
- config/ : configuration du projet (routes, services, sécurité, paramètres)
- public/ : fichiers accessibles publiquement (visuels)

### Frontend
- public/ : contenus visuels, styles CSS, scripts JS
- templates/ : vues Twig côté client, employés et administrateurs

--- 

## Aperçu 

### Page d'accueil
- [Page d'accueil](assets/screenshots/home-page.png)

### Menus
#### Liste des Menus
- [Liste des Menus](assets/screenshots/menu-list.png)

#### Détail d'un Menu
- [Détail Menu](assets/screenshots/menu-detail.png)

### Dashboard utilisateur (client)
#### Dashboard
- [Dashboard Utilisateur](assets/screenshots/dashboard-user.png)

#### Commande Gamifiée
- [Commande Gamifiée Utilisateur](assets/screenshots/commande-gamifiee.png)

### Dashboard administrateur / employé
- [Dashboard Adminstrateur / Employé](assets/screenshots/dashboard-admin.png)

--- 

## Roadmap / Améliorations potentielles si projet poussé dans le futur 
- Implémenter le paiement en ligne
- Tableau de bord statistique plus complet (fonctionnement total et interactif du chart, ajout des comparaisons de plats choisis, et options en quantités commandées et CA)
- Amélioration de l'interface mobile

--- 

## Auteur
Projet réalisé dans le cadre de mon ECF - Graduate Développeur Web & Web Mobile.
Auteur : Alice Gruby
Date : 19/02/2026