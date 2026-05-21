# API Gestion de Boutique & Crédit

API REST Laravel pour gérer une boutique avec un système de crédit client (dettes et paiements).

## Stack
- **Laravel 10** + **Laravel Passport** (OAuth2)
- **MySQL** — données principales
- **MongoDB** — archivage des dettes soldées
- **Cloudinary** — stockage des photos
- **Dompdf** + **Endroid QR Code** — cartes de fidélité PDF

## Fonctionnalités
- Authentification OAuth2 avec rôles (Admin / Boutiquier / Client)
- Gestion des clients, articles, dettes et paiements
- Archivage des dettes soldées vers MongoDB (ou Firebase)
- Génération automatique de QR code et carte de fidélité PDF à l'inscription
- Validation des numéros de téléphone sénégalais

## Architecture
Pattern **Repository / Service** avec interfaces — Routes → Controllers → Services → Repositories → Models

## Installation
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan passport:install
php artisan db:seed
php artisan serve
Rôles
Rôle	Accès
ADMIN	Utilisateurs, archivage/restauration
BOUTIQUIER	Clients, articles, dettes, paiements
CLIENT	À compléter
