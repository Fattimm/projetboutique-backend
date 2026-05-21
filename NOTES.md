# 📝 Notes de développement

## État actuel (08/05/2026)

- Branche active : `dev`
- Toutes les routes de base testées et fonctionnelles
- Archivage MongoDB opérationnel

---

## ✅ Routes testées et fonctionnelles

| Route | Statut |
|---|---|
| `POST /login` | ✅ |
| `GET /logout` | ✅ |
| `POST /users` | ✅ |
| `GET /users` | ✅ |
| `DELETE /users/{id}` | ✅ |
| `GET /articles` | ✅ |
| `POST /articles` | ✅ |
| `GET /articles/{id}` | ✅ |
| `GET /articles/libelle` | ✅ |
| `PATCH /articles/{id}` | ✅ |
| `POST /articles/stock` | ✅ |
| `GET /clients` | ✅ |
| `POST /clients` | ✅ |
| `GET /clients/{id}` | ✅ |
| `GET /clients/filter` | ✅ |
| `GET /clients/status` | ✅ |
| `GET /clients/telephone` | ✅ |
| `GET /clients/{id}/dettes` | ✅ |
| `GET /clients/{id}/user` | ✅ |
| `GET /dettes` | ✅ |
| `POST /dettes` | ✅ |
| `GET /dettes/{id}` | ✅ |
| `GET /dettes/{id}/articles` | ✅ |
| `GET /dettes/{id}/paiements` | ✅ |
| `POST /dettes/{id}/paiements` | ✅ |
| `POST /dette/archiver/{id}` | ✅ |
| `GET /dette/archiver` | ✅ |
| `POST /dette/restaurer/{id}` | ✅ |

---

## 🔴 Routes non encore testées

- [ ] `POST /dette/restaurer/client/{clientId}`
- [ ] `POST /dette/restaurer/date/{date}`
- [ ] `POST /register`
- [ ] `POST /clients` avec user + photo (déclenche Events/Listeners)

---

## 🔴 Fonctionnalités non encore testées

- [ ] Envoi email avec carte de fidélité PDF
      - Configurer `MAIL_*` dans `.env`
      - Vue : `resources/views/emails/loyalty-card.blade.php`
      - Services : `LoyaltyCardService`, `LoyaltyCardEmailService`, `LoyaltyCardMail`
- [ ] Events/Listeners lors création client avec user
      - Event : `ClientCreated`
      - Listeners : `UploadPhotoListener`, `GenerateQrCodeAndLoyaltyCardListener`
- [ ] Swagger — vérifier `/api/documentation` (api.yaml existe)

---

## 🐛 Bugs connus

### 1. `role_id` contient des strings au lieu d'entiers
La migration initiale a créé `role` comme ENUM puis l'a renommé `role_id` sans changer le type.
`role_id` contient `'BOUTIQUIER'` au lieu de `2` (FK vers `roles`).
**Correction :** migration pour convertir ENUM → FK entière + UPDATE des données.

### 2. Double imbrication des réponses JSON
```json
{ "data": { "data": [...], "status": 200 }, "status": "SUCCESS" }
```
Certains services retournent un tableau que `RestResponseTrait` enveloppe encore.
**Correction :** uniformiser — soit les services retournent directement les données, soit ils utilisent `RestResponseTrait`.

### 3. `deletedAt` vs `deleted_at` sur `dettes` et `paiements`
Migrations utilisent `deletedAt` (camelCase). Laravel SoftDeletes attend `deleted_at`.
Contourné avec `const DELETED_AT = 'deletedAt'` dans le modèle `Dette`.
**Correction propre :** migration `renameColumn('deletedAt', 'deleted_at')`.

### 4. `FirebaseArchivageService` — méthodes non implémentées
`restaurerDettesParDate()` et `restaurerDettesParClient()` sont vides.
**Correction :** implémenter ou lever une exception explicite.

### 5. `hasRole()` dans User cassé
```php
// ❌ belongsTo retourne un objet, pas une collection
public function hasRole($roleName) {
    return $this->role->contains('name', $roleName);
}
// ✅ Correction
public function hasRole($roleName) {
    return $this->getRoleAttribute() === $roleName;
}
```

### 6. `DettePolicy` non enregistrée dans `AuthServiceProvider`
```php
// Ajouter dans $policies :
Dette::class => DettePolicy::class,
```

---

## 🟡 Améliorations à faire

- [ ] Masquer `password` dans les réponses User (ajouter dans `$hidden`)
- [ ] Uniformiser format réponses (`RestResponseTrait` partout)
- [ ] Corriger `role_id` — vraie FK entière vers table `roles`
- [ ] Ajouter Jobs pour traitement asynchrone (emails, QR codes)
- [ ] Vérifier et compléter les Observers si nécessaire
- [ ] Policies CLIENT — définir les accès du rôle CLIENT

---

## 🔧 Corrections déjà effectuées

- ✅ `ArticleRepositoryImpl` — utilisait `Dette::` au lieu d'`Article::`
- ✅ `AuthServiceProvider` — mauvais import `Workbench\App\Models\User`
- ✅ `ClientServiceImpl::deleteAccount` — utilisait `$user->deletedAt` au lieu de `$user->delete()`
- ✅ `ClientServiceImpl::index` — code mort corrigé
- ✅ `AppServiceProvider` — `QrCodeService` bindé sur `UploadService`
- ✅ `DetteRepositoryImpl` — `extends` remplacé par `implements`
- ✅ Credentials MongoDB — déplacés du code vers `.env`
- ✅ Ordre des routes — routes statiques avant routes dynamiques `{id}`
- ✅ Logout — `$user->tokens->delete()` → `$user->tokens()->each(fn($t) => $t->delete())`
- ✅ Archivage MongoDB — nom de collection unifié (`archive_dettes_YYYY_MM_DD`)
- ✅ `DetteServiceImpl` — colonne `date` inexistante supprimée dans `addPaiement`
- ✅ `UserController::store` — bypass du service corrigé
- ✅ `UserServiceImpl` — `role` → `role_id` dans `User::create()`
- ✅ `UserPolicy` — ajout méthode `deleteAccount`
- ✅ Filtre users par rôle — `where('role')` → `where('role_id')`

---

## 🖥️ Commandes utiles

```bash
# Démarrer les services
sudo service mysql start
sudo service nginx start
sudo service php8.3-fpm start
php artisan serve

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Base de données
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed  # ⚠️ recrée tout

# Logs en temps réel
tail -f storage/logs/laravel.log
```

---

## 🔑 Comptes de test

| Login | Rôle | Mot de passe |
|---|---|---|
| `johnpaul.lowe` | ADMIN | `password` |
| `cfisher` | BOUTIQUIER | `password` |

---

## 🌐 Accès

| Service | URL |
|---|---|
| API | http://127.0.0.1:8000/api/v1 |
| phpMyAdmin | http://localhost:8080/phpmyadmin |
| MongoDB | https://cloud.mongodb.com (résumer le cluster si en pause) |