<?php
require_once 'includes/config.php';

$pageTitle = 'Accueil';
$pageDescription = 'Découvrez notre large sélection de produits numériques : cartes cadeaux, codes d\'abonnement, licences logicielles. Livraison instantanée garantie.';

$db = Database::getInstance();

// Récupérer les catégories pour l'affichage
$categories = $db->fetchAll("SELECT * FROM categories WHERE actif = 1 ORDER BY ordre_affichage ASC");

// Récupérer les produits populaires (les plus vendus)
$popularProducts = $db->fetchAll("
    SELECT p.*, c.nom as category_name,
           COUNT(oi.id) as total_sales
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN order_items oi ON p.id = oi.product_id
    WHERE p.actif = 1 AND p.stock > 0
    GROUP BY p.id 
    ORDER BY total_sales DESC, p.date_creation DESC 
    LIMIT 8
");

// Récupérer les nouveaux produits
$newProducts = $db->fetchAll("
    SELECT p.*, c.nom as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.actif = 1 AND p.stock > 0
    ORDER BY p.date_creation DESC 
    LIMIT 8
");

include 'includes/header.php';
?>

<div class="container">
    <!-- Section Hero -->
    <section class="hero">
        <h1>Bienvenue sur 2GK</h1>
        <p>Votre boutique de produits numériques de confiance</p>
        <p>Cartes cadeaux • Codes d'abonnement • Licences logicielles • Livraison instantanée</p>
        <div style="margin-top: 2rem;">
            <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-primary" style="margin-right: 1rem;">
                Voir le catalogue
            </a>
            <a href="<?php echo SITE_URL; ?>/categories" class="btn btn-outline">
                Parcourir les catégories
            </a>
        </div>
    </section>

    <!-- Section Catégories -->
    <?php if (!empty($categories)): ?>
    <section class="categories-section mb-3">
        <h2 class="text-center mb-3">Nos catégories</h2>
        <div class="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <?php foreach ($categories as $category): ?>
            <div class="category-card" style="background: var(--card-bg); border-radius: 15px; padding: 2rem; text-align: center; transition: transform 0.3s ease; cursor: pointer;" 
                 onclick="window.location.href='<?php echo SITE_URL; ?>/category/<?php echo $category['id']; ?>'">
                <div class="category-icon" style="font-size: 3rem; margin-bottom: 1rem; color: var(--highlight-color);">
                    <?php
                    // Icônes par défaut selon le nom de la catégorie
                    $icons = [
                        'Cartes Cadeaux Gaming' => 'fas fa-gamepad',
                        'Abonnements Streaming' => 'fas fa-play-circle',
                        'Licences Logicielles' => 'fas fa-laptop-code',
                        'Cartes Prépayées' => 'fas fa-credit-card'
                    ];
                    $icon = $icons[$category['nom']] ?? 'fas fa-tag';
                    ?>
                    <i class="<?php echo $icon; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($category['nom']); ?></h3>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">
                    <?php echo htmlspecialchars($category['description']); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Section Produits Populaires -->
    <?php if (!empty($popularProducts)): ?>
    <section class="popular-products mb-3">
        <h2 class="text-center mb-3">Produits populaires</h2>
        <div class="products-grid">
            <?php foreach ($popularProducts as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/products/' . $product['image']; ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>">
                    <?php else: ?>
                        <i class="fas fa-image"></i>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($product['nom']); ?></h3>
                    <p class="product-description">
                        <?php echo htmlspecialchars(substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : '')); ?>
                    </p>
                    <div class="product-meta">
                        <?php if ($product['pays']): ?>
                            <span class="product-country"><?php echo htmlspecialchars($product['pays']); ?></span>
                        <?php endif; ?>
                        <?php if ($product['plateforme']): ?>
                            <span class="product-platform"><?php echo htmlspecialchars($product['plateforme']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-price"><?php echo number_format($product['prix'], 0, ',', ' '); ?> FCFA</div>
                    <div class="product-stock <?php echo $product['stock'] > 10 ? 'stock-available' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                        <?php echo $product['stock']; ?> disponible(s)
                    </div>
                    <button class="btn btn-primary btn-full add-to-cart" 
                            data-product-id="<?php echo $product['id']; ?>"
                            data-product-name="<?php echo htmlspecialchars($product['nom']); ?>"
                            data-product-price="<?php echo $product['prix']; ?>"
                            data-max-quantity="<?php echo $product['stock']; ?>"
                            <?php echo $product['stock'] === 0 ? 'disabled' : ''; ?>>
                        <?php echo $product['stock'] === 0 ? 'Rupture de stock' : 'Ajouter au panier'; ?>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-secondary">Voir tous les produits</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- Section Nouveaux Produits -->
    <?php if (!empty($newProducts)): ?>
    <section class="new-products mb-3">
        <h2 class="text-center mb-3">Nouveautés</h2>
        <div class="products-grid">
            <?php foreach ($newProducts as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/products/' . $product['image']; ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>">
                    <?php else: ?>
                        <i class="fas fa-image"></i>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($product['nom']); ?></h3>
                    <p class="product-description">
                        <?php echo htmlspecialchars(substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : '')); ?>
                    </p>
                    <div class="product-meta">
                        <?php if ($product['pays']): ?>
                            <span class="product-country"><?php echo htmlspecialchars($product['pays']); ?></span>
                        <?php endif; ?>
                        <?php if ($product['plateforme']): ?>
                            <span class="product-platform"><?php echo htmlspecialchars($product['plateforme']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-price"><?php echo number_format($product['prix'], 0, ',', ' '); ?> FCFA</div>
                    <div class="product-stock <?php echo $product['stock'] > 10 ? 'stock-available' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                        <?php echo $product['stock']; ?> disponible(s)
                    </div>
                    <button class="btn btn-primary btn-full add-to-cart" 
                            data-product-id="<?php echo $product['id']; ?>"
                            data-product-name="<?php echo htmlspecialchars($product['nom']); ?>"
                            data-product-price="<?php echo $product['prix']; ?>"
                            data-max-quantity="<?php echo $product['stock']; ?>"
                            <?php echo $product['stock'] === 0 ? 'disabled' : ''; ?>>
                        <?php echo $product['stock'] === 0 ? 'Rupture de stock' : 'Ajouter au panier'; ?>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Section Avantages -->
    <section class="advantages" style="margin: 4rem 0;">
        <h2 class="text-center mb-3">Pourquoi choisir 2GK ?</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div class="advantage-card" style="background: var(--card-bg); padding: 2rem; border-radius: 15px; text-align: center;">
                <div style="font-size: 3rem; color: var(--highlight-color); margin-bottom: 1rem;">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Livraison instantanée</h3>
                <p style="color: var(--text-muted);">Recevez vos codes immédiatement après le paiement</p>
            </div>
            <div class="advantage-card" style="background: var(--card-bg); padding: 2rem; border-radius: 15px; text-align: center;">
                <div style="font-size: 3rem; color: var(--highlight-color); margin-bottom: 1rem;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Paiement sécurisé</h3>
                <p style="color: var(--text-muted);">Transactions protégées avec KiaPay</p>
            </div>
            <div class="advantage-card" style="background: var(--card-bg); padding: 2rem; border-radius: 15px; text-align: center;">
                <div style="font-size: 3rem; color: var(--highlight-color); margin-bottom: 1rem;">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Support 24/7</h3>
                <p style="color: var(--text-muted);">Une équipe à votre écoute pour vous accompagner</p>
            </div>
            <div class="advantage-card" style="background: var(--card-bg); padding: 2rem; border-radius: 15px; text-align: center;">
                <div style="font-size: 3rem; color: var(--highlight-color); margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Codes garantis</h3>
                <p style="color: var(--text-muted);">Tous nos produits sont authentiques et fonctionnels</p>
            </div>
        </div>
    </section>
</div>

<style>
.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.advantage-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.advantage-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}
</style>

<?php include 'includes/footer.php'; ?>