<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;
if ($isLoggedIn) {
    $db = Database::getInstance();
    $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

// Compter les articles dans le panier
$cartCount = 0;
if ($isLoggedIn) {
    $db = Database::getInstance();
    $cartItems = $db->fetchAll("SELECT SUM(quantite) as total FROM user_cart WHERE user_id = ?", [$_SESSION['user_id']]);
    $cartCount = $cartItems[0]['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME . ' - Produits numériques'; ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Achetez vos produits numériques : cartes cadeaux, codes d\'abonnement, licences logicielles sur ' . SITE_NAME; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- reCAPTCHA (si configuré) -->
    <?php if (RECAPTCHA_SITE_KEY): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>
    
    <?php if (isset($additionalHead)): ?>
        <?php echo $additionalHead; ?>
    <?php endif; ?>
</head>
<body <?php echo $isLoggedIn ? 'class="user-logged-in"' : ''; ?>>
    <header class="header">
        <nav class="nav-container">
            <a href="<?php echo SITE_URL; ?>" class="logo">2GK</a>
            
            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Rechercher un produit...">
                <button class="search-btn" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <ul class="nav-menu">
                <li><a href="<?php echo SITE_URL; ?>">Accueil</a></li>
                <li><a href="<?php echo SITE_URL; ?>/catalogue">Catalogue</a></li>
                <li><a href="<?php echo SITE_URL; ?>/categories">Catégories</a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact">Contact</a></li>
                
                <?php if ($isLoggedIn): ?>
                    <li><a href="<?php echo SITE_URL; ?>/account">Mon compte</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/logout">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>/login">Connexion</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/register">Inscription</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="user-actions">
                <a href="<?php echo SITE_URL; ?>/cart" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-count"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
                
                <button class="mobile-toggle" style="display: none;">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </header>
    
    <main class="main-content">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="container">
                <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?>">
                    <?php 
                    echo $_SESSION['flash_message']; 
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                    ?>
                </div>
            </div>
        <?php endif; ?>