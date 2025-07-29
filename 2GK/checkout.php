<?php
require_once 'includes/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$pageTitle = 'Finaliser ma commande';
$pageDescription = 'Finalisez votre commande et procédez au paiement sécurisé.';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

$cart = new Cart($_SESSION['user_id']);
$cartItems = $cart->getItems();
$cartTotal = $cart->getTotal();
$appliedPromo = $cart->getAppliedPromo();
$finalTotal = $cart->getFinalTotal();

// Vérifier si le panier n'est pas vide
if (empty($cartItems)) {
    header('Location: ' . SITE_URL . '/cart');
    exit;
}

$errors = [];
$success = '';

// Traitement de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Validation des stocks
    $stockValidation = $cart->validateStock();
    if (!$stockValidation['valid']) {
        $errors[] = 'Certains produits ne sont plus disponibles en quantité suffisante';
        foreach ($stockValidation['errors'] as $error) {
            $errors[] = $error;
        }
    }
    
    // Acceptation des CGV
    if (!isset($_POST['accept_cgv'])) {
        $errors[] = 'Vous devez accepter les conditions générales de vente';
    }
    
    if (empty($errors)) {
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            // Créer la commande
            $orderData = [
                'user_id' => $_SESSION['user_id'],
                'total' => $finalTotal,
                'statut' => 'en_attente',
                'promo_code' => $appliedPromo ? $appliedPromo['code'] : null,
                'discount_amount' => $appliedPromo ? ($cartTotal - $finalTotal) : 0
            ];
            
            $orderId = $db->query("
                INSERT INTO orders (user_id, total, statut, promo_code, discount_amount, date_creation) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ", [
                $orderData['user_id'],
                $orderData['total'],
                $orderData['statut'],
                $orderData['promo_code'],
                $orderData['discount_amount']
            ]);
            
            $orderId = $db->lastInsertId();
            
            // Ajouter les items de commande
            foreach ($cartItems as $item) {
                $db->query("
                    INSERT INTO order_items (order_id, product_id, quantite, prix_unitaire) 
                    VALUES (?, ?, ?, ?)
                ", [$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }
            
            // Marquer l'utilisation du code promo
            if ($appliedPromo) {
                $db->query("
                    INSERT INTO promotion_utilisations (promotion_id, user_id, order_id) 
                    VALUES (?, ?, ?)
                ", [$appliedPromo['id'], $_SESSION['user_id'], $orderId]);
            }
            
            $db->commit();
            
            // Vider le panier
            $cart->clear();
            
            // Rediriger vers le paiement
            header('Location: ' . SITE_URL . '/payment/' . $orderId);
            exit;
            
        } catch (Exception $e) {
            $db->rollback();
            $errors[] = 'Erreur lors de la création de la commande. Veuillez réessayer.';
            if (DEBUG_MODE) {
                $errors[] = $e->getMessage();
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="checkout-layout">
        <!-- Contenu principal -->
        <div class="checkout-main">
            <div class="checkout-header">
                <h1>Finaliser ma commande</h1>
                <div class="checkout-steps">
                    <div class="step completed">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Panier</span>
                    </div>
                    <div class="step active">
                        <i class="fas fa-credit-card"></i>
                        <span>Commande</span>
                    </div>
                    <div class="step">
                        <i class="fas fa-check"></i>
                        <span>Confirmation</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Erreurs détectées :</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Informations de facturation -->
            <div class="checkout-section">
                <h2><i class="fas fa-user"></i> Informations de facturation</h2>
                <div class="billing-info">
                    <div class="info-card">
                        <div class="info-row">
                            <span class="label">Nom complet :</span>
                            <span class="value"><?php echo htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email :</span>
                            <span class="value"><?php echo htmlspecialchars($userData['email']); ?></span>
                        </div>
                        <?php if ($userData['telephone']): ?>
                        <div class="info-row">
                            <span class="label">Téléphone :</span>
                            <span class="value"><?php echo htmlspecialchars($userData['telephone']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($userData['adresse']): ?>
                        <div class="info-row">
                            <span class="label">Adresse :</span>
                            <span class="value"><?php echo htmlspecialchars($userData['adresse']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/account/profile" class="btn btn-outline btn-sm">
                        <i class="fas fa-edit"></i> Modifier mes informations
                    </a>
                </div>
            </div>

            <!-- Récapitulatif de la commande -->
            <div class="checkout-section">
                <h2><i class="fas fa-list"></i> Récapitulatif de la commande</h2>
                <div class="order-summary">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <div class="item-image">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo SITE_URL . '/uploads/products/' . $item['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-image"></i>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-meta">
                                <?php if ($item['pays']): ?>
                                    <span><i class="fas fa-globe"></i> <?php echo htmlspecialchars($item['pays']); ?></span>
                                <?php endif; ?>
                                <?php if ($item['plateforme']): ?>
                                    <span><i class="fas fa-desktop"></i> <?php echo htmlspecialchars($item['plateforme']); ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="delivery-type">
                                <i class="fas fa-<?php echo $item['type_livraison'] == 'automatique' ? 'bolt' : 'clock'; ?>"></i>
                                Livraison <?php echo $item['type_livraison']; ?>
                            </p>
                        </div>
                        <div class="item-quantity">
                            <span class="quantity">x<?php echo $item['quantity']; ?></span>
                        </div>
                        <div class="item-price">
                            <span class="price"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> FCFA</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Méthode de paiement -->
            <div class="checkout-section">
                <h2><i class="fas fa-credit-card"></i> Méthode de paiement</h2>
                <div class="payment-method">
                    <div class="payment-option selected">
                        <div class="option-icon">
                            <img src="<?php echo SITE_URL; ?>/assets/images/kiapay-logo.png" alt="KiaPay" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="fallback-icon" style="display: none;">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>
                        <div class="option-details">
                            <h3>KiaPay</h3>
                            <p>Paiement sécurisé par KiaPay</p>
                            <div class="payment-methods">
                                <i class="fab fa-cc-visa" title="Visa"></i>
                                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                                <i class="fas fa-mobile-alt" title="Mobile Money"></i>
                            </div>
                        </div>
                        <div class="option-check">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conditions générales -->
            <div class="checkout-section">
                <form method="POST" class="checkout-form">
                    <div class="terms-section">
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="accept_cgv" name="accept_cgv" required>
                            <label for="accept_cgv">
                                J'ai lu et j'accepte les 
                                <a href="<?php echo SITE_URL; ?>/cgv" target="_blank">Conditions Générales de Vente</a>
                                et la 
                                <a href="<?php echo SITE_URL; ?>/politique-confidentialite" target="_blank">Politique de confidentialité</a>
                            </label>
                        </div>
                        
                        <div class="security-info">
                            <div class="security-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Paiement 100% sécurisé</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-lock"></i>
                                <span>Données cryptées SSL</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-undo"></i>
                                <span>Garantie satisfait ou remboursé</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?php echo SITE_URL; ?>/cart" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Retour au panier
                        </a>
                        <button type="submit" name="place_order" class="btn btn-primary btn-large">
                            <i class="fas fa-credit-card"></i> 
                            Procéder au paiement (<?php echo number_format($finalTotal, 0, ',', ' '); ?> FCFA)
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar récapitulatif -->
        <div class="checkout-sidebar">
            <div class="order-total-card">
                <h3>Récapitulatif</h3>
                
                <div class="total-line">
                    <span>Sous-total</span>
                    <span><?php echo number_format($cartTotal, 0, ',', ' '); ?> FCFA</span>
                </div>
                
                <?php if ($appliedPromo): ?>
                <div class="total-line discount">
                    <span>
                        <i class="fas fa-tag"></i>
                        Réduction (<?php echo htmlspecialchars($appliedPromo['code']); ?>)
                    </span>
                    <span>-<?php echo number_format($cartTotal - $finalTotal, 0, ',', ' '); ?> FCFA</span>
                </div>
                <?php endif; ?>
                
                <div class="total-line final">
                    <span><strong>Total à payer</strong></span>
                    <span><strong><?php echo number_format($finalTotal, 0, ',', ' '); ?> FCFA</strong></span>
                </div>
                
                <div class="delivery-info">
                    <h4><i class="fas fa-truck"></i> Livraison</h4>
                    <div class="delivery-methods">
                        <?php
                        $hasAutomatic = false;
                        $hasManual = false;
                        foreach ($cartItems as $item) {
                            if ($item['type_livraison'] == 'automatique') $hasAutomatic = true;
                            if ($item['type_livraison'] == 'manuelle') $hasManual = true;
                        }
                        ?>
                        
                        <?php if ($hasAutomatic): ?>
                        <div class="delivery-method">
                            <i class="fas fa-bolt text-success"></i>
                            <span>Livraison instantanée pour certains produits</span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($hasManual): ?>
                        <div class="delivery-method">
                            <i class="fas fa-clock text-warning"></i>
                            <span>Livraison sous 24h pour les autres</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="contact-support">
                    <p><i class="fas fa-headset"></i> Besoin d'aide ?</p>
                    <a href="<?php echo SITE_URL; ?>/contact" class="support-link">
                        Contactez notre support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    margin-top: 2rem;
}

.checkout-main {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.checkout-header {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.checkout-header h1 {
    color: var(--text-color);
    margin-bottom: 2rem;
    font-size: 2.5rem;
}

.checkout-steps {
    display: flex;
    justify-content: center;
    gap: 2rem;
    position: relative;
}

.checkout-steps::before {
    content: '';
    position: absolute;
    top: 30px;
    left: 25%;
    right: 25%;
    height: 2px;
    background: var(--border-color);
    z-index: 1;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    z-index: 2;
}

.step i {
    width: 60px;
    height: 60px;
    background: var(--border-color);
    color: var(--text-muted);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.step.completed i {
    background: var(--success-color);
    color: white;
}

.step.active i {
    background: var(--highlight-color);
    color: white;
}

.step span {
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}

.step.completed span,
.step.active span {
    color: var(--text-color);
}

.checkout-section {
    background: var(--card-bg);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.checkout-section h2 {
    background: var(--input-bg);
    color: var(--text-color);
    padding: 1.5rem 2rem;
    margin: 0;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    border-bottom: 1px solid var(--border-color);
}

.checkout-section h2 i {
    color: var(--highlight-color);
}

.billing-info {
    padding: 2rem;
}

.info-card {
    background: var(--input-bg);
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.8rem;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-row .label {
    color: var(--text-muted);
    font-weight: 500;
}

.info-row .value {
    color: var(--text-color);
}

.order-summary {
    padding: 2rem;
}

.order-item {
    display: grid;
    grid-template-columns: 60px 1fr auto auto;
    gap: 1rem;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 60px;
    height: 60px;
    background: var(--input-bg);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-image i {
    color: var(--text-muted);
    font-size: 1.5rem;
}

.item-details h3 {
    color: var(--text-color);
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.item-meta {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.3rem;
}

.item-meta span {
    margin-right: 1rem;
}

.delivery-type {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin: 0;
}

.delivery-type i {
    color: var(--highlight-color);
    margin-right: 0.3rem;
}

.item-quantity {
    text-align: center;
    color: var(--text-muted);
    font-weight: 500;
}

.item-price {
    text-align: right;
    color: var(--text-color);
    font-weight: bold;
}

.payment-method {
    padding: 2rem;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--input-bg);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option.selected {
    border-color: var(--highlight-color);
    background: rgba(233, 69, 96, 0.05);
}

.option-icon {
    width: 60px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.option-icon img {
    max-width: 100%;
    max-height: 100%;
}

.fallback-icon {
    width: 100%;
    height: 100%;
    background: var(--highlight-color);
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.option-details h3 {
    color: var(--text-color);
    margin-bottom: 0.3rem;
}

.option-details p {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.payment-methods {
    display: flex;
    gap: 0.5rem;
}

.payment-methods i {
    font-size: 1.5rem;
    color: var(--text-muted);
}

.option-check {
    margin-left: auto;
    color: var(--highlight-color);
    font-size: 1.5rem;
}

.checkout-form {
    padding: 2rem;
}

.terms-section {
    margin-bottom: 2rem;
}

.checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    margin-bottom: 2rem;
}

.checkbox-group input[type="checkbox"] {
    margin-top: 0.2rem;
    flex-shrink: 0;
}

.checkbox-group label {
    color: var(--text-color);
    line-height: 1.5;
}

.checkbox-group a {
    color: var(--highlight-color);
    text-decoration: none;
}

.checkbox-group a:hover {
    text-decoration: underline;
}

.security-info {
    display: flex;
    justify-content: space-around;
    background: var(--input-bg);
    padding: 1.5rem;
    border-radius: 10px;
}

.security-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-align: center;
}

.security-item i {
    color: var(--highlight-color);
    font-size: 1.5rem;
}

.security-item span {
    color: var(--text-color);
    font-size: 0.8rem;
    font-weight: 500;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: bold;
}

.checkout-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.order-total-card {
    background: var(--card-bg);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.order-total-card h3 {
    color: var(--text-color);
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    text-align: center;
}

.total-line {
    display: flex;
    justify-content: space-between;
    padding: 0.8rem 0;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-color);
}

.total-line:last-of-type {
    border-bottom: none;
}

.total-line.discount {
    color: var(--success-color);
}

.total-line.final {
    font-size: 1.2rem;
    font-weight: bold;
    padding-top: 1rem;
    border-top: 2px solid var(--border-color);
    color: var(--highlight-color);
}

.delivery-info {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.delivery-info h4 {
    color: var(--text-color);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.delivery-info h4 i {
    color: var(--highlight-color);
}

.delivery-methods {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.delivery-method {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 0.9rem;
    color: var(--text-color);
}

.text-success {
    color: var(--success-color);
}

.text-warning {
    color: var(--warning-color);
}

.contact-support {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
    text-align: center;
}

.contact-support p {
    color: var(--text-muted);
    margin-bottom: 0.8rem;
}

.contact-support i {
    color: var(--highlight-color);
    margin-right: 0.5rem;
}

.support-link {
    color: var(--highlight-color);
    text-decoration: none;
    font-weight: 500;
}

.support-link:hover {
    text-decoration: underline;
}

@media (max-width: 1024px) {
    .checkout-layout {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .checkout-sidebar {
        order: -1;
        position: static;
    }
}

@media (max-width: 768px) {
    .checkout-steps {
        gap: 1rem;
    }
    
    .checkout-steps::before {
        left: 20%;
        right: 20%;
    }
    
    .step i {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .step span {
        font-size: 0.8rem;
    }
    
    .checkout-section h2 {
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
    }
    
    .billing-info,
    .order-summary,
    .payment-method,
    .checkout-form {
        padding: 1.5rem;
    }
    
    .order-item {
        grid-template-columns: 50px 1fr;
        gap: 1rem;
    }
    
    .item-quantity,
    .item-price {
        grid-column: 2;
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
    }
    
    .security-info {
        flex-direction: column;
        gap: 1rem;
    }
    
    .security-item {
        flex-direction: row;
        justify-content: center;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
        text-align: center;
    }
    
    .order-total-card {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .checkout-header {
        padding: 1.5rem;
    }
    
    .checkout-header h1 {
        font-size: 2rem;
    }
    
    .checkout-steps {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .checkout-steps::before {
        display: none;
    }
    
    .item-image {
        width: 50px;
        height: 50px;
    }
    
    .order-item {
        grid-template-columns: 50px 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>