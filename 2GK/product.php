<?php
require_once 'includes/config.php';

// Récupérer l'ID du produit
$productId = (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    header('Location: ' . SITE_URL . '/catalogue');
    exit;
}

$db = Database::getInstance();

// Récupérer le produit
$product = $db->fetch("
    SELECT p.*, c.nom as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.actif = 1
", [$productId]);

if (!$product) {
    header('Location: ' . SITE_URL . '/catalogue');
    exit;
}

$pageTitle = $product['nom'];
$pageDescription = substr($product['description'], 0, 160);

// Récupérer les produits similaires
$similarProducts = $db->fetchAll("
    SELECT p.*, c.nom as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.category_id = ? AND p.id != ? AND p.actif = 1 AND p.stock > 0
    ORDER BY RAND() 
    LIMIT 4
", [$product['category_id'], $productId]);

include 'includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="<?php echo SITE_URL; ?>">Accueil</a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?php echo SITE_URL; ?>/catalogue">Catalogue</a>
        <?php if ($product['category_name']): ?>
            <span class="breadcrumb-separator">/</span>
            <a href="<?php echo SITE_URL; ?>/catalogue?category=<?php echo $product['category_id']; ?>">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a>
        <?php endif; ?>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?php echo htmlspecialchars($product['nom']); ?></span>
    </nav>

    <!-- Produit principal -->
    <div class="product-detail">
        <div class="row">
            <!-- Image du produit -->
            <div class="col-md-6">
                <div class="product-image-container">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/products/' . $product['image']; ?>" 
                             alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                             class="product-main-image">
                    <?php else: ?>
                        <div class="product-placeholder">
                            <i class="fas fa-image"></i>
                            <p>Aucune image disponible</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Badges -->
                    <div class="product-badges">
                        <?php if ($product['stock'] == 0): ?>
                            <span class="badge badge-danger">Rupture de stock</span>
                        <?php elseif ($product['stock'] <= 5): ?>
                            <span class="badge badge-warning">Stock faible</span>
                        <?php endif; ?>
                        
                        <?php if ($product['type_livraison'] == 'automatique'): ?>
                            <span class="badge badge-success">
                                <i class="fas fa-bolt"></i> Livraison instantanée
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Informations du produit -->
            <div class="col-md-6">
                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['nom']); ?></h1>
                    
                    <div class="product-meta">
                        <?php if ($product['category_name']): ?>
                            <span class="meta-item">
                                <i class="fas fa-tag"></i>
                                <a href="<?php echo SITE_URL; ?>/catalogue?category=<?php echo $product['category_id']; ?>">
                                    <?php echo htmlspecialchars($product['category_name']); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($product['pays']): ?>
                            <span class="meta-item">
                                <i class="fas fa-globe"></i>
                                <?php echo htmlspecialchars($product['pays']); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($product['plateforme']): ?>
                            <span class="meta-item">
                                <i class="fas fa-desktop"></i>
                                <?php echo htmlspecialchars($product['plateforme']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="product-price">
                        <span class="price-amount"><?php echo number_format($product['prix'], 0, ',', ' '); ?> FCFA</span>
                    </div>

                    <div class="product-stock">
                        <i class="fas fa-boxes"></i>
                        <span class="stock-text <?php echo $product['stock'] > 10 ? 'stock-good' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                            <?php if ($product['stock'] > 0): ?>
                                <?php echo $product['stock']; ?> code(s) disponible(s)
                            <?php else: ?>
                                Rupture de stock
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Actions d'achat -->
                    <div class="product-actions">
                        <div class="quantity-selector">
                            <label for="quantity">Quantité :</label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">-</button>
                                <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                                <button type="button" class="quantity-btn" onclick="changeQuantity(1)">+</button>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-large add-to-cart-single" 
                                data-product-id="<?php echo $product['id']; ?>"
                                data-product-name="<?php echo htmlspecialchars($product['nom']); ?>"
                                data-product-price="<?php echo $product['prix']; ?>"
                                data-max-quantity="<?php echo $product['stock']; ?>"
                                <?php echo $product['stock'] === 0 ? 'disabled' : ''; ?>>
                            <?php if ($product['stock'] === 0): ?>
                                <i class="fas fa-ban"></i> Rupture de stock
                            <?php else: ?>
                                <i class="fas fa-cart-plus"></i> Ajouter au panier
                            <?php endif; ?>
                        </button>
                    </div>

                    <!-- Informations de livraison -->
                    <div class="delivery-info">
                        <h3>Informations de livraison</h3>
                        <div class="delivery-features">
                            <div class="delivery-feature">
                                <i class="fas fa-bolt"></i>
                                <div>
                                    <strong>Livraison <?php echo $product['type_livraison']; ?></strong>
                                    <p>
                                        <?php if ($product['type_livraison'] == 'automatique'): ?>
                                            Votre code sera livré immédiatement après le paiement
                                        <?php else: ?>
                                            Livraison manuelle sous 24h maximum
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="delivery-feature">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <strong>Code garanti</strong>
                                    <p>Tous nos codes sont authentiques et fonctionnels</p>
                                </div>
                            </div>
                            
                            <div class="delivery-feature">
                                <i class="fas fa-headset"></i>
                                <div>
                                    <strong>Support 24/7</strong>
                                    <p>Une équipe disponible pour vous accompagner</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description détaillée -->
    <div class="product-description-section">
        <div class="section-header">
            <h2>Description du produit</h2>
        </div>
        <div class="description-content">
            <?php if ($product['description']): ?>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <?php else: ?>
                <p>Aucune description disponible pour ce produit.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Produits similaires -->
    <?php if (!empty($similarProducts)): ?>
    <div class="similar-products-section">
        <div class="section-header">
            <h2>Produits similaires</h2>
            <a href="<?php echo SITE_URL; ?>/catalogue?category=<?php echo $product['category_id']; ?>" 
               class="btn btn-outline">Voir tous</a>
        </div>
        
        <div class="products-grid">
            <?php foreach ($similarProducts as $similarProduct): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($similarProduct['image']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/products/' . $similarProduct['image']; ?>" 
                             alt="<?php echo htmlspecialchars($similarProduct['nom']); ?>">
                    <?php else: ?>
                        <i class="fas fa-image"></i>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="<?php echo SITE_URL; ?>/product/<?php echo $similarProduct['id']; ?>">
                            <?php echo htmlspecialchars($similarProduct['nom']); ?>
                        </a>
                    </h3>
                    <div class="product-price"><?php echo number_format($similarProduct['prix'], 0, ',', ' '); ?> FCFA</div>
                    <div class="product-stock <?php echo $similarProduct['stock'] > 10 ? 'stock-available' : ($similarProduct['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                        <?php echo $similarProduct['stock']; ?> disponible(s)
                    </div>
                    <button class="btn btn-primary btn-full add-to-cart" 
                            data-product-id="<?php echo $similarProduct['id']; ?>"
                            data-product-name="<?php echo htmlspecialchars($similarProduct['nom']); ?>"
                            data-product-price="<?php echo $similarProduct['prix']; ?>"
                            data-max-quantity="<?php echo $similarProduct['stock']; ?>">
                        <i class="fas fa-cart-plus"></i> Ajouter au panier
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.breadcrumb {
    margin-bottom: 2rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.breadcrumb a {
    color: var(--text-muted);
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: var(--highlight-color);
}

.breadcrumb-separator {
    margin: 0 0.5rem;
    color: var(--text-muted);
}

.breadcrumb-current {
    color: var(--text-color);
    font-weight: 500;
}

.product-detail {
    margin-bottom: 4rem;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0 1rem;
}

.product-image-container {
    position: relative;
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.product-main-image {
    max-width: 100%;
    max-height: 400px;
    object-fit: contain;
    border-radius: 10px;
}

.product-placeholder {
    height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
}

.product-placeholder i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.product-badges {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.badge-danger {
    background: var(--danger-color);
    color: white;
}

.badge-warning {
    background: var(--warning-color);
    color: var(--primary-color);
}

.badge-success {
    background: var(--success-color);
    color: white;
}

.product-info {
    padding-left: 2rem;
}

.product-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
    color: var(--text-color);
    line-height: 1.2;
}

.product-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.meta-item i {
    color: var(--highlight-color);
}

.meta-item a {
    color: var(--highlight-color);
    text-decoration: none;
}

.meta-item a:hover {
    text-decoration: underline;
}

.product-price {
    margin-bottom: 2rem;
}

.price-amount {
    font-size: 3rem;
    font-weight: bold;
    color: var(--highlight-color);
}

.product-stock {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.stock-good {
    color: var(--success-color);
}

.stock-low {
    color: var(--warning-color);
}

.stock-out {
    color: var(--danger-color);
}

.product-actions {
    margin-bottom: 3rem;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.quantity-selector label {
    font-weight: 500;
    color: var(--text-color);
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
}

.quantity-btn {
    background: var(--accent-color);
    border: none;
    color: var(--text-color);
    width: 40px;
    height: 40px;
    cursor: pointer;
    font-size: 1.2rem;
    transition: background-color 0.3s ease;
}

.quantity-btn:hover {
    background: var(--highlight-color);
}

.quantity-controls input {
    width: 60px;
    height: 40px;
    border: none;
    background: var(--input-bg);
    color: var(--text-color);
    text-align: center;
    font-size: 1.1rem;
    font-weight: bold;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.2rem;
    font-weight: bold;
    width: 100%;
}

.delivery-info {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.delivery-info h3 {
    margin-bottom: 1.5rem;
    color: var(--text-color);
}

.delivery-features {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.delivery-feature {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.delivery-feature i {
    font-size: 1.5rem;
    color: var(--highlight-color);
    margin-top: 0.2rem;
}

.delivery-feature strong {
    color: var(--text-color);
    display: block;
    margin-bottom: 0.3rem;
}

.delivery-feature p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9rem;
}

.product-description-section,
.similar-products-section {
    margin-bottom: 4rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.section-header h2 {
    color: var(--text-color);
    font-size: 2rem;
}

.description-content {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    line-height: 1.8;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .product-info {
        padding-left: 0;
        margin-top: 2rem;
    }
    
    .product-title {
        font-size: 2rem;
    }
    
    .price-amount {
        font-size: 2.5rem;
    }
    
    .product-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .delivery-features {
        gap: 1rem;
    }
    
    .delivery-feature {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .section-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
}
</style>

<script>
function changeQuantity(delta) {
    const quantityInput = document.getElementById('quantity');
    const currentQuantity = parseInt(quantityInput.value);
    const maxQuantity = parseInt(quantityInput.max);
    const newQuantity = currentQuantity + delta;
    
    if (newQuantity >= 1 && newQuantity <= maxQuantity) {
        quantityInput.value = newQuantity;
    }
}

// Gestion de l'ajout au panier avec quantité
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.querySelector('.add-to-cart-single');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const quantity = parseInt(document.getElementById('quantity').value);
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;
            const maxQuantity = this.dataset.maxQuantity;
            
            // Utiliser la fonction du panier principal
            if (window.twoGK) {
                // Simuler un bouton avec la bonne quantité
                const tempButton = document.createElement('button');
                tempButton.dataset.productId = productId;
                tempButton.dataset.productName = productName;
                tempButton.dataset.productPrice = productPrice;
                tempButton.dataset.maxQuantity = maxQuantity;
                
                // Ajouter la quantité sélectionnée
                for (let i = 0; i < quantity; i++) {
                    window.twoGK.addToCart(tempButton);
                }
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>