<?php
// Configuration principale de 2GK
// Fichier de configuration - À adapter selon votre environnement

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', '2gk_database');
define('DB_USER', 'root'); // À changer pour la production
define('DB_PASS', ''); // À changer pour la production
define('DB_CHARSET', 'utf8mb4');

// Configuration du site
define('SITE_NAME', '2GK');
define('SITE_URL', 'http://localhost/2GK'); // À changer pour la production
define('ADMIN_URL', SITE_URL . '/admin-2GK');
define('SITE_EMAIL', 'contact@2gk.com');

// Chemins
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Configuration de sécurité
define('SECRET_KEY', 'votre_cle_secrete_unique_a_changer'); // IMPORTANT: À changer!
define('SESSION_LIFETIME', 3600 * 24); // 24 heures
define('PASSWORD_MIN_LENGTH', 8);

// Configuration KiaPay
define('KIAPAY_API_KEY', ''); // À remplir avec votre clé API KiaPay
define('KIAPAY_SECRET_KEY', ''); // À remplir avec votre clé secrète KiaPay
define('KIAPAY_SANDBOX', true); // false pour la production

// Configuration email (SMTP)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_SECURE', 'tls');

// Configuration reCAPTCHA
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// Configuration des uploads
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configuration des erreurs
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    // Mode développement
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    define('DEBUG_MODE', true);
} else {
    // Mode production
    error_reporting(0);
    ini_set('display_errors', 0);
    define('DEBUG_MODE', false);
}

// Timezone
date_default_timezone_set('Africa/Porto-Novo'); // Ajustez selon votre localisation

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader simple pour les classes
spl_autoload_register(function ($class) {
    $file = INCLUDES_PATH . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
?>