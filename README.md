# 🛍️ API Gestion de Boutique / Crédit

API REST Laravel pour la gestion d'une boutique avec système de crédit (dettes clients), paiements, archivage MongoDB/Firebase, génération de QR codes et cartes de fidélité PDF.

---

## 📚 Table des matières

- [Stack technique](#stack-technique)
- [Architecture](#architecture)
- [Base de données](#base-de-données)
- [Installation](#installation)
- [Authentification](#authentification)
- [Rôles et permissions](#rôles-et-permissions)
- [Endpoints API](#endpoints-api)
- [Fonctionnalités avancées](#fonctionnalités-avancées)
- [Archivage](#archivage)
- [Bugs connus et corrections](#bugs-connus-et-corrections)
- [Structure des fichiers](#structure-des-fichiers)

---

## 🛠️ Stack technique

| Outil | Rôle |
|---|---|
| Laravel 10.x | Framework principal |
| Laravel Passport | Authentification OAuth2 |
| MySQL | Base de données principale |
| MongoDB | Archivage des dettes (`mongodb/laravel-mongodb`) |
| Firebase (optionnel) | Alternative d'archivage |
| Cloudinary | Stockage photos utilisateurs |
| Endroid QR Code | Génération de QR codes |
| Dompdf | Génération de cartes de fidélité PDF |

---

## 🏗️ Architecture

```
app/
├── Http/
│   ├── Controllers/          # Reçoit les requêtes HTTP, délègue aux services
│   ├── Requests/             # Validation (Form Requests)
│   └── Middleware/
├── Models/                   # Eloquent ORM
├── Services/
│   ├── Interfaces/           # Contrats (ArticleService, DetteService, ArchivageService...)
│   └── *Impl.php             # Implémentations concrètes
├── Repositories/
│   ├── Interfaces/           # Contrats Repository
│   └── *Impl.php
├── Policies/                 # Autorisations par rôle (Laravel Gates)
├── Facades/                  # ClientServiceFacade, ClientRepositoryFacade
├── Events/                   # ClientCreated
├── Listeners/                # UploadPhotoListener, GenerateQrCodeAndLoyaltyCardListener
├── Rules/                    # TelephoneRule, CustumPasswordRule
├── Traits/                   # RestResponseTrait
└── Providers/                # Bindings, Policies, Events
```

**Pattern :** Routes → Controllers → Services (via interfaces) → Repositories → Models

Toutes les actions des controllers passent par `$this->authorize()` (Laravel Policies).

---

## 🗄️ Base de données

### Schéma des tables

```
users
  id | nom | prenom | login (unique) | role_id (FK→roles) | password
  email | photo | deleted_at | created_at | updated_at

roles
  id | name (unique) | created_at | updated_at

clients
  id | surname (unique) | telephone (unique, 9 car.) | adresse (nullable)
  user_id (nullable, FK→users SET NULL) | created_at | updated_at

articles
  id | libelle (unique) | prix | qteStock | created_at | updated_at

dettes
  id | client_id (FK→clients) | montant | deleted_at | created_at | updated_at

detail_dette  [pivot]
  id | dette_id (FK) | article_id (FK) | qteVente | prixVente | timestamps

paiements
  id | dette_id (FK→dettes) | montant | deleted_at | created_at | updated_at

oauth_* (tables Passport)
```

### Relations Eloquent

```
User        ──hasOne──►        Client
User        ──belongsTo──►     Role
Client      ──belongsTo──►     User
Client      ──hasMany──►       Dette
Dette       ──belongsTo──►     Client
Dette       ──belongsToMany──► Article  (via detail_dette: qteVente, prixVente)
Dette       ──hasMany──►       Paiement
Paiement    ──belongsTo──►     Dette
```

---

## ⚙️ Installation

```bash
# 1. Cloner le projet
git clone <repo-url> && cd <projet>

# 2. Installer les dépendances PHP
composer install

# 3. Configurer l'environnement
cp .env.example .env
# → Remplir les variables (voir section ci-dessous)

# 4. Générer la clé applicative
php artisan key:generate

# 5. Lancer les migrations
php artisan migrate

# 6. Installer Passport (génère les clés OAuth)
php artisan passport:install

# 7. Créer le lien symbolique pour le storage
php artisan storage:link

# 8. Seeder les rôles
php artisan db:seed

# 9. Lancer le serveur
php artisan serve
```

### Variables .env importantes

```env
APP_NAME=BoutiqueAPI
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_base
DB_USERNAME=root
DB_PASSWORD=

# Passport
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=

# MongoDB (archivage dettes)
MONGODB_URI=mongodb+srv://<user>:<password>@<cluster>.mongodb.net/<dbname>?retryWrites=true&w=majority
MONGODB_DATABASE=maboutique

# Firebase (archivage alternatif)
FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
FIREBASE_DATABASE_URL=https://<project>.firebaseio.com

# Cloudinary (photos)
CLOUDINARY_URL=cloudinary://<api_key>:<api_secret>@<cloud_name>

# Mail
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
```

> ⚠️ Ne jamais commiter le `.env`. Les credentials MongoDB/Firebase/Cloudinary
> doivent toujours être dans `.env`, jamais en dur dans le code source.

---

## 🔐 Authentification

Basée sur **Laravel Passport** (OAuth2, Personal Access Token).

Toutes les routes (sauf `POST /login`) sont protégées par `auth:api`.
Le token est à envoyer dans chaque requête :

```
Authorization: Bearer <token>
```

### Login

```http
POST /api/v1/login
Content-Type: application/json

{
  "login": "admin",
  "password": "Secret1@"
}
```

Réponse :
```json
{
  "status": 200,
  "data": { "accessToken": { ... } },
  "message": "Login réussi"
}
```

### Logout

```http
GET /api/v1/logout
Authorization: Bearer <token>
```

Révoque tous les tokens de l'utilisateur authentifié.

---

## 🔒 Rôles et permissions

Les rôles sont stockés en base (`roles`) et comparés directement via `$user->role` dans les Policies.

| Rôle | Description |
|---|---|
| `ADMIN` | Gestion des utilisateurs, archivage/restauration des dettes |
| `BOUTIQUIER` | Gestion clients, articles, dettes, paiements |
| `CLIENT` | Rôle prévu — policies à compléter |

### Récapitulatif des accès par policy

| Action | ADMIN | BOUTIQUIER | CLIENT |
|---|---|---|---|
| Créer / lister utilisateurs | ✅ | ❌ | ❌ |
| Gérer clients | ❌ | ✅ | ❌ |
| Gérer articles | ❌ | ✅ | ❌ |
| Créer / voir dettes | ❌ | ✅ | ❌ |
| Ajouter paiement | ❌ | ✅ | ❌ |
| Archiver / restaurer dette | ✅ | ❌ | ❌ |

---

## 📡 Endpoints API

Base URL : `/api/v1`

### 👤 Auth

| Méthode | Route | Rôle | Description |
|---|---|---|---|
| POST | `/login` | — | Connexion |
| GET | `/logout` | Authentifié | Déconnexion |
| POST | `/register` | ADMIN | Créer un user et l'associer à un client existant |

### 👥 Utilisateurs

| Méthode | Route | Rôle | Description |
|---|---|---|---|
| POST | `/users` | ADMIN | Créer un utilisateur |
| GET | `/users` | ADMIN | Lister (`?role=`, `?active=oui/non`) |
| DELETE | `/users/{id}` | BOUTIQUIER | Soft-delete d'un compte |

### 🧑‍💼 Clients

| Méthode | Route | Rôle | Description |
|---|---|---|---|
| GET | `/clients` | BOUTIQUIER | Lister tous les clients |
| POST | `/clients` | BOUTIQUIER | Créer un client (avec user optionnel) |
| GET | `/clients/{id}` | BOUTIQUIER | Détail d'un client |
| GET | `/clients/filter` | BOUTIQUIER | Filtrer par compte (`?comptes=oui/non`) |
| GET | `/clients/status` | BOUTIQUIER | Filtrer par statut (`?active=oui/non`) |
| POST | `/clients/telephone` | BOUTIQUIER | Rechercher par téléphone |
| POST | `/clients/{id}/dettes` | BOUTIQUIER | Dettes d'un client |
| POST | `/clients/{id}/user` | BOUTIQUIER | Client avec son compte utilisateur |

**Corps `POST /clients` (avec compte utilisateur) :**
```json
{
  "surname": "Diallo",
  "adresse": "Dakar, Medina",
  "telephone": "771234567",
  "user": {
    "nom": "Moussa",
    "prenom": "Diallo",
    "login": "moussa.diallo",
    "email": "moussa@example.com",
    "password": "Secret1@",
    "password_confirmation": "Secret1@",
    "role": "CLIENT",
    "photo": "<fichier image>"
  }
}
```

### 🛒 Articles

| Méthode | Route | Rôle | Description |
|---|---|---|---|
| GET | `/articles` | BOUTIQUIER | Lister (`?disponible=oui/non`) |
| POST | `/articles` | BOUTIQUIER | Créer un article |
| GET | `/articles/{id}` | BOUTIQUIER | Détail par ID |
| POST | `/articles/libelle` | BOUTIQUIER | Rechercher par libellé |
| PATCH | `/articles/{id}` | BOUTIQUIER | Incrémenter le stock |
| POST | `/articles/stock` | BOUTIQUIER | Mettre à jour plusieurs stocks |

**Corps `POST /articles` :**
```json
{ "libelle": "Riz 25kg", "prix": 15000, "qteStock": 50 }
```

**Corps `POST /articles/stock` :**
```json
{
  "articles": [
    { "id": 1, "qteStock": 10 },
    { "id": 2, "qteStock": 5 }
  ]
}
```

### 💳 Dettes

| Méthode | Route | Rôle | Description |
|---|---|---|---|
| GET | `/dettes` | BOUTIQUIER | Lister (`?statut=Solde/NonSolde`) |
| POST | `/dettes` | BOUTIQUIER | Créer une dette |
| GET | `/dettes/{id}` | BOUTIQUIER | Détail d'une dette |
| POST | `/dettes/{id}/articles` | BOUTIQUIER | Articles d'une dette |
| GET | `/dettes/{id}/paiements` | BOUTIQUIER | Paiements d'une dette |
| POST | `/dettes/{id}/paiements` | BOUTIQUIER | Ajouter un paiement |

**Corps `POST /dettes` :**
```json
{
  "clientId": 1,
  "montant": 25000,
  "articles": [
    { "articleId": 1, "qteVente": 2, "prixVente": 10000 }
  ],
  "paiement": { "montant": 10000 }
}
```

**Corps `POST /dettes/{id}/paiements` :**
```json
{ "paiement": { "montant": 5000 } }
```

### 📦 Archivage des dettes

| Méthode | Route | Rôle | Description |
|---|---|---|---|
| POST | `/dette/archiver/{id}` | ADMIN | Archiver (si entièrement payée) |
| GET | `/dette/archiver` | ADMIN | Lister les dettes archivées |
| POST | `/dette/restaurer/{id}` | ADMIN | Restaurer une dette |
| POST | `/dette/restaurer/client/{clientId}` | ADMIN | Restaurer toutes les dettes d'un client |
| POST | `/dette/restaurer/date/{date}` | ADMIN | Restaurer les dettes d'une date |

---

## 🚀 Fonctionnalités avancées

### QR Code & Carte de fidélité (Event-driven)

À la création d'un client **avec** un compte (`POST /clients`), l'événement `ClientCreated`
déclenche automatiquement deux listeners :

1. **`UploadPhotoListener`** → Upload photo sur Cloudinary + stockage local
2. **`GenerateQrCodeAndLoyaltyCardListener`** → Génère un QR code PNG avec les infos client,
   puis génère une carte de fidélité PDF (Dompdf) et l'envoie par email

Stockage local :
- `storage/app/public/photos/` — photos
- `storage/app/public/qr_codes/` — QR codes
- `storage/app/public/Loyalty_cards/` — PDF

### Validation numéro de téléphone sénégalais

La règle `TelephoneRule` accepte :
- Mobile : préfixes `77`, `76`, `75`, `70`, `78` + 7 chiffres
- Fixe Dakar : `338` + 6 chiffres

### Format de réponse uniforme

Le trait `RestResponseTrait` standardise toutes les réponses :
```json
{
  "data": { ... },
  "status": "SUCCESS",
  "message": "..."
}
```

---

## 📦 Archivage

Les dettes archivées sont **déplacées** de MySQL vers MongoDB (ou Firebase).

**Condition d'archivage :** `SUM(paiements.montant) >= dette.montant` (dette entièrement soldée).

**Changer le backend** dans `AppServiceProvider.php` :
```php
// MongoDB (actif par défaut)
$this->app->bind(ArchivageService::class, MongoArchivageService::class);

// Firebase
$this->app->bind(ArchivageService::class, FirebaseArchivageService::class);
```

Les collections MongoDB sont nommées `archive_dettes_YYYY_MM_DD`.

---

## 📁 Structure des fichiers clés

```
routes/api.php

app/Http/Controllers/
  AuthController · UserController · ClientController
  ArticleController · DetteController · DetteArchivageController

app/Http/Requests/
  AuthRequest · StoreUserRequest · StoreClientRequest
  StoreArticleRequest · UpdateArticleRequest · UpdateMultipleStocksRequest
  StoreDetteRequest

app/Models/
  User · Client · Dette · Article · Paiement · Role

app/Services/Interfaces/
  AuthentificationServiceInterface · UserService · ClientService
  ArticleService · DetteService · ArchivageService

app/Services/
  AuthentificationPassport · AuthentificationSanctum
  UserServiceImpl · ClientServiceImpl · ArticleServiceImpl · DetteServiceImpl
  MongoArchivageService · FirebaseArchivageService
  CloudinaryService · QrCodeService · UploadService
  LoyaltyCardService · LoyaltyCardEmailService · LoyaltyCardMail

app/Repositories/Interfaces/
  ArticleRepository · ClientRepository · DetteRepository · UserRepository

app/Repositories/
  ArticleRepositoryImpl · ClientRepositoryImpl
  DetteRepositoryImpl · UserRepositoryImpl
  MongoArchivageRepositoryImpl · QrCodeRepository

app/Policies/
  ArticlePolicy · ClientPolicy · DettePolicy · UserPolicy

app/Rules/
  TelephoneRule · CustumPasswordRule

app/Traits/
  RestResponseTrait

app/Providers/
  AppServiceProvider · AuthServiceProvider · AuthCustomServiceProvider
  EventServiceProvider · QrCodeServiceProvider

app/Events/ClientCreated.php
app/Listeners/UploadPhotoListener.php
app/Listeners/GenerateQrCodeAndLoyaltyCardListener.php
```