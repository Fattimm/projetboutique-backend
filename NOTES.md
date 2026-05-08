# Notes de développement

## État actuel (08/05/2026)
- Branche active : dev
- Toutes les routes de base testées et fonctionnelles
- MongoDB archivage opérationnel

## Ce qui reste à tester
- [ ] POST /dette/restaurer/client/{clientId}
- [ ] POST /dette/restaurer/date/{date}
- [ ] POST /register
- [ ] POST /clients (avec user + photo)

## Ce qui reste à développer/corriger

### 🔴 Priorité haute
- [ ] Tester envoi email avec carte fidélité PDF
      - Vue : resources/views/emails/loyalty-card.blade.php (existe)
      - Service : LoyaltyCardEmailService
      - Mail : LoyaltyCardMail
      - Configurer MAIL_* dans .env
- [ ] Tester Events/Listeners lors création client avec user
      - Event : ClientCreated
      - Listeners : UploadPhotoListener, GenerateQrCodeAndLoyaltyCardListener
- [ ] Swagger/OpenAPI (api.yaml existe déjà)
      - Vérifier la doc swagger sur /api/documentation

### 🟡 Priorité moyenne
- [ ] Corriger double imbrication des réponses JSON
- [ ] Corriger role_id (ENUM string → FK entière vers table roles)
- [ ] Implémenter FirebaseArchivageService (méthodes vides)
- [ ] Corriger hasRole() dans User model
- [ ] Ajouter Jobs si traitement asynchrone nécessaire
- [ ] Vérifier Observers si présents

### 🟢 Priorité basse
- [ ] Masquer password dans réponses User
- [ ] Uniformiser format réponses (RestResponseTrait partout)

## Bugs connus
1. role_id contient strings au lieu d'entiers FK
2. Double imbrication réponses (data.data)
3. FirebaseArchivageService.restaurerDettesParDate() vide
4. FirebaseArchivageService.restaurerDettesParClient() vide

## Commandes pour relancer le projet
```bash
sudo service mysql start
sudo service nginx start  
sudo service php8.3-fpm start
php artisan serve
```

## Comptes de test
- ADMIN    : login=johnpaul.lowe / password=password
- BOUTIQUIER : login=cfisher / password=password

## Accès
- API        : http://127.0.0.1:8000/api/v1
- phpMyAdmin : http://localhost:8080/phpmyadmin (root/passer123)
- MongoDB    : cloud.mongodb.com (cluster0 — résumer si en pause)

## Configuration .env importante
- DB_DATABASE=boutique_db
- DB_USERNAME=root  
- DB_PASSWORD=passer123
- MONGODB_URI= (dans .env)
- CLOUDINARY_URL= (à configurer pour photos)
- MAIL_* (à configurer pour emails)