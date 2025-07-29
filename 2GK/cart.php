<?php
require_once 'includes/config.php';

$pageTitle = 'Mon panier';
$userId = $_SESSION['user_id'] ?? null;

$cart = new Cart($userId);
$cartItems = $cart->getItems();
$cartTotal = $cart->getTotal();
$appliedPromo = $cart->getAppliedPromo();
$finalTotal = $cart->getFinalTotal();

// Traitement des actions AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'update_quantity':
            $productId = $_POST['product_id'] ?? 0;
            $quantity = (int)($_POST['quantity'] ?? 0);
            $result = $cart->updateItem($productId, $quantity);
            echo json_encode($result);
            exit;
            
        case 'remove_item':
            $productId = $_POST['product_id'] ?? 0;
            $result = $cart->removeItem($productId);
            echo json_encode($result);
            exit;
            
        case 'apply_promo':
            $promoCode = $_POST['promo_code'] ?? '';
            $result = $cart->applyPromoCode($promoCode);
            echo json_encode($result);
            exit;
            
        case 'remove_promo':
            $result = $cart->removePromoCode();
            echo json_encode($result);
            exit;
            
        case 'clear_cart':
            $result = $cart->clear();
            echo json_encode($result);
            exit;
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="cart-header mb-3">
        <h1>Mon panier</h1>
        <div class="cart-actions">
            <?php if (!empty($cartItems)): ?>
                <button class="btn btn-outline" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Vider le panier
                </button>
            <?php endif; ?>
            <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Continuer mes achats
            </a>
        </div>
    </div>
    
    <?php if (empty($cartItems)): ?>
        <!-- Panier vide -->
        <div class="empty-cart">
            <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2>Votre panier est vide</h2>
            <p>Découvrez notre sélection de produits numériques et ajoutez-les à votre panier.</p>
            <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-primary">
                Voir le catalogue
            </a>
        </div>
    <?php else: ?>
        <!-- Panier avec articles -->
        <div class="cart-content">
            <div class="row">
                <!-- Liste des articles -->
                <div class="col-lg-8">
                    <div class="cart-items-section">
                        <div class="cart-items-header">
                            <h3>Articles dans votre panier (<?php echo count($cartItems); ?>)</h3>
                        </div>
                        
                        <div class="cart-items" id="cart-items">
                            <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                                <div class="item-image">
                                    <?php if ($item['image']): ?>
                                        <img src="<?php echo SITE_URL . '/uploads/products/' . $item['image']; ?>" 
                                             alt="<?php echo htmlspecialchars($item['nom']); ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-details">
                                    <h4 class="item-title"><?php echo htmlspecialchars($item['nom']); ?></h4>
                                    <div class="item-meta">
                                        <?php if ($item['pays']): ?>
                                            <span class="meta-tag"><?php echo htmlspecialchars($item['pays']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($item['plateforme']): ?>
                                            <span class="meta-tag"><?php echo htmlspecialchars($item['plateforme']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-price">
                                        <?php echo number_format($item['prix'], 0, ',', ' '); ?> FCFA
                                    </div>
                                    <div class="item-stock">
                                        Stock disponible: <?php echo $item['stock']; ?>
                                    </div>
                                </div>
                                
                                <div class="item-quantity">
                                    <label>Quantité:</label>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['quantite'] - 1; ?>)">-</button>
                                        <input type="number" 
                                               class="quantity-input" 
                                               value="<?php echo $item['quantite']; ?>" 
                                               min="1" 
                                               max="<?php echo $item['stock']; ?>"
                                               onchange="updateQuantity(<?php echo $item['product_id']; ?>, this.value)">
                                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['quantite'] + 1; ?>)">+</button>
                                    </div>
                                </div>
                                
                                <div class="item-total">
                                    <div class="total-price">
                                        <?php echo number_format($item['total_item'], 0, ',', ' '); ?> FCFA
                                    </div>
                                    <button class="remove-btn" onclick="removeItem(<?php echo $item['product_id']; ?>)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Résumé de commande -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <div class="summary-header">
                            <h3>Résumé de la commande</h3>
                        </div>
                        
                        <div class="summary-content">
                            <div class="summary-line">
                                <span>Sous-total:</span>
                                <span id="cart-subtotal"><?php echo number_format($cartTotal, 0, ',', ' '); ?> FCFA</span>
                            </div>
                            
                            <?php if ($appliedPromo): ?>
                            <div class="summary-line promo-line">
                                <span>
                                    Code promo (<?php echo $appliedPromo['code']; ?>)
                                    <button class="remove-promo-btn" onclick="removePromo()">×</button>
                                </span>
                                <span class="discount">-<?php echo number_format($appliedPromo['discount'], 0, ',', ' '); ?> FCFA</span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="summary-line total-line">
                                <span><strong>Total:</strong></span>
                                <span id="cart-total"><strong><?php echo number_format($finalTotal, 0, ',', ' '); ?> FCFA</strong></span>
                            </div>
                        </div>
                        
                        <!-- Code promo -->
                        <?php if (!$appliedPromo): ?>
                        <div class="promo-section">
                            <div class="promo-toggle" onclick="togglePromoForm()">
                                <i class="fas fa-tag"></i> Avez-vous un code promo ?
                            </div>
                            <div class="promo-form" id="promo-form" style="display: none;">
                                <input type="text" id="promo-code" placeholder="Entrez votre code promo" class="form-control">
                                <button class="btn btn-outline btn-full" onclick="applyPromo()">Appliquer</button>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Bouton de commande -->
                        <div class="checkout-section">
                            <?php if ($userId): ?>
                                <a href="<?php echo SITE_URL; ?>/checkout" class="btn btn-primary btn-full btn-lg">
                                    <i class="fas fa-credit-card"></i> Passer la commande
                                </a>
                            <?php else: ?>
                                <div class="login-required">
                                    <p style="text-align: center; margin-bottom: 1rem; color: var(--text-muted);">
                                        Vous devez être connecté pour passer commande
                                    </p>
                                    <a href="<?php echo SITE_URL; ?>/login?redirect=<?php echo urlencode('/cart'); ?>" 
                                       class="btn btn-primary btn-full">
                                        Se connecter
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/register" 
                                       class="btn btn-outline btn-full" style="margin-top: 0.5rem;">
                                        Créer un compte
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Informations de livraison -->
                        <div class="delivery-info">
                            <div class="info-item">
                                <i class="fas fa-bolt"></i>
                                <span>Livraison instantanée</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Paiement sécurisé</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Codes garantis</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.cart-actions {
    display: flex;
    gap: 1rem;
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-cart-icon {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.empty-cart h2 {
    margin-bottom: 1rem;
    color: var(--text-color);
}

.empty-cart p {
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.col-lg-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
    padding: 0 1rem;
}

.col-lg-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding: 0 1rem;
}

.cart-items-section {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.cart-items-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.cart-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.no-image {
    width: 100%;
    height: 100%;
    background: var(--accent-color);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 2rem;
}

.item-details {
    flex: 1;
}

.item-title {
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.item-meta {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.meta-tag {
    background: var(--accent-color);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.8rem;
}

.item-price {
    font-weight: bold;
    color: var(--highlight-color);
    margin-bottom: 0.3rem;
}

.item-stock {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.item-quantity {
    text-align: center;
}

.item-quantity label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
}

.quantity-btn {
    background: var(--accent-color);
    border: none;
    color: var(--text-color);
    width: 35px;
    height: 35px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.quantity-btn:hover {
    background: var(--highlight-color);
}

.quantity-input {
    width: 50px;
    height: 35px;
    border: none;
    background: var(--input-bg);
    color: var(--text-color);
    text-align: center;
    outline: none;
}

.item-total {
    text-align: center;
}

.total-price {
    font-weight: bold;
    font-size: 1.1rem;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.remove-btn {
    background: none;
    border: none;
    color: var(--danger-color);
    cursor: pointer;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.remove-btn:hover {
    color: #dc3545;
}

.cart-summary {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    position: sticky;
    top: 120px;
}

.summary-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.promo-line {
    color: var(--success-color);
}

.remove-promo-btn {
    background: none;
    border: none;
    color: var(--danger-color);
    cursor: pointer;
    margin-left: 0.5rem;
    font-size: 1.2rem;
}

.discount {
    color: var(--success-color);
}

.total-line {
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
    font-size: 1.2rem;
}

.promo-section {
    margin: 2rem 0;
    padding: 1rem 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
}

.promo-toggle {
    color: var(--highlight-color);
    cursor: pointer;
    margin-bottom: 1rem;
}

.promo-toggle:hover {
    text-decoration: underline;
}

.promo-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.checkout-section {
    margin-top: 2rem;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.login-required {
    text-align: center;
}

.delivery-info {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.info-item i {
    color: var(--success-color);
    width: 16px;
}

@media (max-width: 768px) {
    .cart-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .cart-actions {
        justify-content: center;
    }
    
    .col-lg-8,
    .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .col-lg-4 {
        margin-top: 2rem;
    }
    
    .cart-item {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .item-details {
        order: 1;
    }
    
    .item-quantity {
        order: 2;
    }
    
    .item-total {
        order: 3;
    }
    
    .cart-summary {
        position: static;
    }
}
</style>

<script>
function updateQuantity(productId, quantity) {
    if (quantity < 1) return;
    
    fetch('<?php echo SITE_URL; ?>/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_quantity&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            window.twoGK.showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.twoGK.showAlert('Erreur lors de la mise à jour', 'danger');
    });
}

function removeItem(productId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) return;
    
    fetch('<?php echo SITE_URL; ?>/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove_item&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            window.twoGK.showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.twoGK.showAlert('Erreur lors de la suppression', 'danger');
    });
}

function togglePromoForm() {
    const form = document.getElementById('promo-form');
    form.style.display = form.style.display === 'none' ? 'flex' : 'none';
}

function applyPromo() {
    const promoCode = document.getElementById('promo-code').value.trim();
    if (!promoCode) return;
    
    fetch('<?php echo SITE_URL; ?>/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=apply_promo&promo_code=${encodeURIComponent(promoCode)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            window.twoGK.showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.twoGK.showAlert('Erreur lors de l\'application du code promo', 'danger');
    });
}

function removePromo() {
    fetch('<?php echo SITE_URL; ?>/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=remove_promo'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            window.twoGK.showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.twoGK.showAlert('Erreur lors de la suppression du code promo', 'danger');
    });
}

function clearCart() {
    if (!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) return;
    
    fetch('<?php echo SITE_URL; ?>/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear_cart'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            window.twoGK.showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.twoGK.showAlert('Erreur lors du vidage du panier', 'danger');
    });
}
</script>

<?php include 'includes/footer.php'; ?>