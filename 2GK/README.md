# 2GK - Site de vente de produits numériques

![2GK Logo](assets/images/logo.png)

**2GK** est une plateforme de vente de produits numériques inspirée de G2A, spécialisée dans les cartes cadeaux, codes d'abonnement, licences logicielles et autres produits numériques avec livraison instantanée.

## 🚀 Fonctionnalités principales

### 🛒 Catalogue de produits
- Cartes cadeaux (Steam, PSN, Xbox, etc.)
- Codes d'abonnement (Netflix, Spotify, etc.)
- Licences logicielles
- Système de catégories et filtres
- Recherche dynamique
- Gestion du stock en temps réel

### 👤 Gestion des utilisateurs
- Inscription/Connexion sécurisée
- Profil utilisateur complet
- Historique des commandes
- Téléchargement des codes achetés
- Système de panier persistant

### 🔐 Interface d'administration
- Accès sécurisé via `/admin-2GK`
- Gestion complète des produits
- Gestion du stock et des codes
- Suivi des commandes et livraisons
- Statistiques de vente
- Gestion des promotions

### 💳 Paiement et livraison
- Intégration KiaPay
- Livraison automatique ou manuelle
- Notifications par email
- Génération de factures PDF

### 🎨 Design
- Interface sombre et professionnelle
- Responsive (mobile/tablette/desktop)
- Animations et transitions fluides
- UX optimisée

## 📋 Prérequis

- **Serveur web** : Apache/Nginx avec PHP 7.4+
- **Base de données** : MySQL 5.7+ ou MariaDB 10.3+
- **PHP Extensions** :
  - PDO et PDO_MySQL
  - mbstring
  - json
  - curl
  - gd (pour les images)
  - openssl
- **Composer** (optionnel, pour les dépendances futures)

## 🛠️ Installation locale

### 1. Cloner le projet
```bash
git clone <votre-repo>
cd 2GK
```

### 2. Configuration de la base de données

#### Créer la base de données
```sql
CREATE DATABASE 2gk_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Importer la structure
```bash
mysql -u root -p 2gk_database < database.sql
```

### 3. Configuration du site

#### Modifier le fichier de configuration
Éditez `includes/config.php` et ajustez les paramètres :

```php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', '2gk_database');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');

// Configuration du site
define('SITE_URL', 'http://localhost/2GK');
define('SITE_EMAIL', 'votre@email.com');

// Clé secrète (IMPORTANT : À changer !)
define('SECRET_KEY', 'votre_cle_secrete_unique_tres_longue');
```

### 4. Permissions des dossiers
```bash
chmod 755 uploads/
chmod 755 uploads/products/
chmod 644 includes/config.php
```

### 5. Serveur local
```bash
# Avec PHP intégré
php -S localhost:8000

# Ou avec XAMPP/WAMP
# Placez le dossier dans htdocs/www
```

### 6. Accès initial
- **Site** : http://localhost:8000 (ou votre URL locale)
- **Admin** : http://localhost:8000/admin-2GK
  - Email : `admin@2gk.com`
  - Mot de passe : `admin123` (⚠️ À changer immédiatement !)

## 🌐 Déploiement sur Hostinger

### 1. Préparer les fichiers

#### Créer une archive
```bash
# Exclure les fichiers de développement
zip -r 2gk-production.zip . -x "*.git*" "README.md" "*.md"
```

### 2. Upload sur Hostinger

1. Connectez-vous au **File Manager** Hostinger
2. Naviguez vers `public_html/`
3. Uploadez et extrayez `2gk-production.zip`
4. Ou utilisez FTP/SFTP :
```bash
# Via FTP
ftp votre-domaine.com
# Uploadez tous les fichiers dans public_html/
```

### 3. Configuration de la base de données

#### Via le panneau Hostinger
1. Allez dans **Bases de données MySQL**
2. Créez une nouvelle base : `votre_db_2gk`
3. Créez un utilisateur avec tous les privilèges
4. Importez `database.sql` via phpMyAdmin

#### Mettre à jour la configuration
```php
// includes/config.php
define('DB_HOST', 'localhost'); // Ou l'IP fournie par Hostinger
define('DB_NAME', 'votre_nom_db');
define('DB_USER', 'votre_user_db');
define('DB_PASS', 'votre_pass_db');

define('SITE_URL', 'https://votre-domaine.com');
```

### 4. Configuration SSL et sécurité

#### Forcer HTTPS (dans .htaccess)
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Sécurité
<Files "includes/config.php">
    Order Allow,Deny
    Deny from all
</Files>

<Files "database.sql">
    Order Allow,Deny
    Deny from all
</Files>
```

### 5. Configuration email (SMTP)

#### Avec Gmail/Outlook
```php
// includes/config.php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre@email.com');
define('SMTP_PASSWORD', 'votre_mot_de_passe_app');
define('SMTP_SECURE', 'tls');
```

#### Avec Hostinger Email
```php
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'contact@votre-domaine.com');
define('SMTP_PASSWORD', 'votre_mot_de_passe');
```

## 🔧 Configuration des services

### KiaPay (Paiement)

1. Créez un compte sur [KiaPay](https://kiapay.com)
2. Récupérez vos clés API
3. Configurez dans `includes/config.php` :
```php
define('KIAPAY_API_KEY', 'votre_cle_api');
define('KIAPAY_SECRET_KEY', 'votre_cle_secrete');
define('KIAPAY_SANDBOX', false); // true pour les tests
```

### Google reCAPTCHA

1. Allez sur [Google reCAPTCHA](https://www.google.com/recaptcha/)
2. Créez un site (v2 Checkbox)
3. Configurez :
```php
define('RECAPTCHA_SITE_KEY', 'votre_cle_site');
define('RECAPTCHA_SECRET_KEY', 'votre_cle_secrete');
```

## 👨‍💼 Gestion administrative

### Accès à l'administration

**URL** : `https://votre-domaine.com/admin-2GK`

### Gestion des produits

#### Ajouter un produit
1. **Admin** → **Produits** → **Ajouter**
2. Remplissez les informations :
   - Nom, description, prix
   - Catégorie, pays, plateforme
   - Image (optionnelle)
   - Type de livraison (automatique/manuelle)

#### Gestion du stock
1. **Produits** → **Gérer les codes**
2. Ajoutez des codes manuellement ou par import CSV/TXT
3. Format CSV : `code1,code2,code3` (un par ligne)

### Gestion des commandes

#### Livraison automatique
- Les codes sont livrés automatiquement si en stock
- Email envoyé au client
- Statut mis à jour automatiquement

#### Livraison manuelle
1. **Commandes** → **En attente de livraison**
2. Sélectionnez la commande
3. Assignez un code disponible
4. Confirmez la livraison

### Promotions et codes de réduction

#### Créer une promotion
1. **Promotions** → **Ajouter**
2. Configurez :
   - Code promo (ex: `WELCOME10`)
   - Type : pourcentage ou montant fixe
   - Valeur de la reduction
   - Conditions (montant minimum, dates)
   - Nombre d'utilisations

### Statistiques

#### Tableau de bord
- Ventes du jour/mois
- Produits populaires
- Alertes de stock faible
- Revenus et commissions

#### Rapports
- Export des ventes (CSV/Excel)
- Statistiques par produit
- Analyse des clients

## 🔒 Sécurité

### Mesures implémentées

- **Mots de passe** : Hashage bcrypt
- **Sessions** : Tokens sécurisés
- **CSRF** : Protection des formulaires
- **SQL Injection** : Requêtes préparées
- **XSS** : Échappement des données
- **Upload** : Validation des fichiers

### Recommandations

#### Changements obligatoires
```php
// includes/config.php
define('SECRET_KEY', 'CHANGEZ_CETTE_CLE_MAINTENANT');

// Base de données
// Changez le mot de passe admin par défaut
UPDATE admin SET password = '$2y$10$...' WHERE email = 'admin@2gk.com';
```

#### Permissions fichiers
```bash
chmod 644 *.php
chmod 755 uploads/
chmod 600 includes/config.php
```

#### Sauvegarde automatique
```bash
# Script de sauvegarde (crontab)
#!/bin/bash
mysqldump -u user -p password 2gk_database > backup_$(date +%Y%m%d).sql
tar -czf backup_files_$(date +%Y%m%d).tar.gz public_html/
```

## 📧 Configuration des emails

### Templates d'emails

Les templates sont dans `includes/email-templates/` :
- `welcome.html` : Email de bienvenue
- `order-confirmation.html` : Confirmation de commande
- `delivery.html` : Livraison de codes
- `password-reset.html` : Réinitialisation

### Personnalisation
```php
// Modifier les templates
$emailTemplate = file_get_contents('includes/email-templates/delivery.html');
$emailTemplate = str_replace('{CUSTOMER_NAME}', $customerName, $emailTemplate);
$emailTemplate = str_replace('{ORDER_NUMBER}', $orderNumber, $emailTemplate);
$emailTemplate = str_replace('{PRODUCT_CODES}', $codes, $emailTemplate);
```

## 🔄 Maintenance et mises à jour

### Tâches périodiques

#### Nettoyage automatique
```php
// À exécuter via cron (quotidien)
// Nettoyer les sessions de panier expirées
$cart = new Cart();
$cart->cleanExpiredSessions();

// Nettoyer les tokens de réinitialisation expirés
DELETE FROM password_resets WHERE expires_at < NOW();
```

#### Sauvegarde de base de données
```bash
# Crontab quotidien
0 2 * * * mysqldump -u user -p password 2gk_database > /backups/2gk_$(date +\%Y\%m\%d).sql
```

### Monitoring

#### Logs à surveiller
- Erreurs PHP : `/logs/php_errors.log`
- Erreurs serveur : `/logs/error.log`
- Tentatives de connexion admin
- Commandes échouées

#### Alertes recommandées
- Stock faible (< 5 codes)
- Commandes en attente > 24h
- Erreurs de paiement
- Tentatives de connexion suspectes

## 🐛 Dépannage

### Problèmes courants

#### Erreur de connexion base de données
```
Solution :
1. Vérifiez les identifiants dans config.php
2. Testez la connexion MySQL
3. Vérifiez que le serveur MySQL est démarré
```

#### Images ne s'affichent pas
```
Solution :
1. Vérifiez les permissions du dossier uploads/
2. Contrôlez le chemin dans SITE_URL
3. Testez l'accès direct aux images
```

#### Emails non envoyés
```
Solution :
1. Vérifiez la configuration SMTP
2. Testez avec un service comme Mailtrap
3. Contrôlez les logs du serveur mail
```

#### Erreur 500 après déploiement
```
Solution :
1. Vérifiez les logs d'erreur
2. Contrôlez les permissions des fichiers
3. Testez la configuration PHP
4. Vérifiez le fichier .htaccess
```

### Mode debug

#### Activer le debug
```php
// includes/config.php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

#### Logs personnalisés
```php
// Ajouter dans vos scripts
if (DEBUG_MODE) {
    error_log("Debug: " . print_r($variable, true));
}
```

## 📞 Support

### Documentation
- **API KiaPay** : https://docs.kiapay.com
- **PHP PDO** : https://www.php.net/manual/en/book.pdo.php
- **MySQL** : https://dev.mysql.com/doc/

### Contacts
- **Email** : support@2gk.com
- **Documentation** : Consultez ce README
- **Issues** : Créez un ticket sur le repository

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez :

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 🚀 Roadmap

### Version 2.0 (Prévue)
- [ ] API REST complète
- [ ] Application mobile
- [ ] Système d'affiliation
- [ ] Multi-devises
- [ ] Marketplace multi-vendeurs
- [ ] Système de reviews
- [ ] Chat support en temps réel

### Améliorations continues
- [ ] Optimisation des performances
- [ ] Tests automatisés
- [ ] Documentation API
- [ ] Internationalisation (i18n)

---

**Développé avec ❤️ pour la vente de produits numériques**

*Dernière mise à jour : Décembre 2024*