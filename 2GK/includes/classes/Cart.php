<?php
class Cart {
    private $db;
    private $userId;
    private $sessionId;
    
    public function __construct($userId = null) {
        $this->db = Database::getInstance();
        $this->userId = $userId;
        $this->sessionId = session_id();
    }
    
    public function addItem($productId, $quantity = 1) {
        // Vérifier que le produit existe et est disponible
        $product = $this->db->fetch(
            "SELECT * FROM products WHERE id = ? AND actif = 1",
            [$productId]
        );
        
        if (!$product) {
            return ['success' => false, 'message' => 'Produit non trouvé'];
        }
        
        if ($product['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Stock insuffisant'];
        }
        
        try {
            if ($this->userId) {
                // Utilisateur connecté - utiliser user_cart
                $existingItem = $this->db->fetch(
                    "SELECT * FROM user_cart WHERE user_id = ? AND product_id = ?",
                    [$this->userId, $productId]
                );
                
                if ($existingItem) {
                    $newQuantity = $existingItem['quantite'] + $quantity;
                    if ($newQuantity > $product['stock']) {
                        return ['success' => false, 'message' => 'Stock insuffisant'];
                    }
                    
                    $this->db->query(
                        "UPDATE user_cart SET quantite = ? WHERE user_id = ? AND product_id = ?",
                        [$newQuantity, $this->userId, $productId]
                    );
                } else {
                    $this->db->query(
                        "INSERT INTO user_cart (user_id, product_id, quantite) VALUES (?, ?, ?)",
                        [$this->userId, $productId, $quantity]
                    );
                }
            } else {
                // Utilisateur non connecté - utiliser cart_sessions
                $existingItem = $this->db->fetch(
                    "SELECT * FROM cart_sessions WHERE session_id = ? AND product_id = ?",
                    [$this->sessionId, $productId]
                );
                
                if ($existingItem) {
                    $newQuantity = $existingItem['quantite'] + $quantity;
                    if ($newQuantity > $product['stock']) {
                        return ['success' => false, 'message' => 'Stock insuffisant'];
                    }
                    
                    $this->db->query(
                        "UPDATE cart_sessions SET quantite = ?, date_expiration = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE session_id = ? AND product_id = ?",
                        [$newQuantity, $this->sessionId, $productId]
                    );
                } else {
                    $this->db->query(
                        "INSERT INTO cart_sessions (session_id, product_id, quantite) VALUES (?, ?, ?)",
                        [$this->sessionId, $productId, $quantity]
                    );
                }
            }
            
            return ['success' => true, 'message' => 'Produit ajouté au panier'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'ajout au panier'];
        }
    }
    
    public function updateItem($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }
        
        // Vérifier le stock
        $product = $this->db->fetch(
            "SELECT stock FROM products WHERE id = ? AND actif = 1",
            [$productId]
        );
        
        if (!$product || $product['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Stock insuffisant'];
        }
        
        try {
            if ($this->userId) {
                $this->db->query(
                    "UPDATE user_cart SET quantite = ? WHERE user_id = ? AND product_id = ?",
                    [$quantity, $this->userId, $productId]
                );
            } else {
                $this->db->query(
                    "UPDATE cart_sessions SET quantite = ?, date_expiration = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE session_id = ? AND product_id = ?",
                    [$quantity, $this->sessionId, $productId]
                );
            }
            
            return ['success' => true, 'message' => 'Quantité mise à jour'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }
    }
    
    public function removeItem($productId) {
        try {
            if ($this->userId) {
                $this->db->query(
                    "DELETE FROM user_cart WHERE user_id = ? AND product_id = ?",
                    [$this->userId, $productId]
                );
            } else {
                $this->db->query(
                    "DELETE FROM cart_sessions WHERE session_id = ? AND product_id = ?",
                    [$this->sessionId, $productId]
                );
            }
            
            return ['success' => true, 'message' => 'Produit retiré du panier'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la suppression'];
        }
    }
    
    public function getItems() {
        if ($this->userId) {
            return $this->db->fetchAll("
                SELECT 
                    uc.*, 
                    p.nom, 
                    p.prix, 
                    p.image, 
                    p.stock,
                    p.pays,
                    p.plateforme,
                    (uc.quantite * p.prix) as total_item
                FROM user_cart uc 
                JOIN products p ON uc.product_id = p.id 
                WHERE uc.user_id = ? AND p.actif = 1
                ORDER BY uc.date_ajout DESC
            ", [$this->userId]);
        } else {
            return $this->db->fetchAll("
                SELECT 
                    cs.*, 
                    p.nom, 
                    p.prix, 
                    p.image, 
                    p.stock,
                    p.pays,
                    p.plateforme,
                    (cs.quantite * p.prix) as total_item
                FROM cart_sessions cs 
                JOIN products p ON cs.product_id = p.id 
                WHERE cs.session_id = ? AND cs.date_expiration > NOW() AND p.actif = 1
                ORDER BY cs.date_creation DESC
            ", [$this->sessionId]);
        }
    }
    
    public function getItemCount() {
        if ($this->userId) {
            $result = $this->db->fetch(
                "SELECT SUM(quantite) as total FROM user_cart WHERE user_id = ?",
                [$this->userId]
            );
        } else {
            $result = $this->db->fetch(
                "SELECT SUM(quantite) as total FROM cart_sessions WHERE session_id = ? AND date_expiration > NOW()",
                [$this->sessionId]
            );
        }
        
        return $result['total'] ?? 0;
    }
    
    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['total_item'];
        }
        
        return $total;
    }
    
    public function clear() {
        try {
            if ($this->userId) {
                $this->db->query("DELETE FROM user_cart WHERE user_id = ?", [$this->userId]);
            } else {
                $this->db->query("DELETE FROM cart_sessions WHERE session_id = ?", [$this->sessionId]);
            }
            
            return ['success' => true, 'message' => 'Panier vidé'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors du vidage du panier'];
        }
    }
    
    public function isEmpty() {
        return $this->getItemCount() == 0;
    }
    
    public function syncFromSession($userId) {
        // Synchroniser le panier de session vers le panier utilisateur lors de la connexion
        try {
            $this->db->beginTransaction();
            
            // Récupérer les items de session
            $sessionItems = $this->db->fetchAll(
                "SELECT * FROM cart_sessions WHERE session_id = ? AND date_expiration > NOW()",
                [$this->sessionId]
            );
            
            foreach ($sessionItems as $item) {
                // Vérifier si l'item existe déjà dans le panier utilisateur
                $existingItem = $this->db->fetch(
                    "SELECT * FROM user_cart WHERE user_id = ? AND product_id = ?",
                    [$userId, $item['product_id']]
                );
                
                if ($existingItem) {
                    // Mettre à jour la quantité
                    $newQuantity = $existingItem['quantite'] + $item['quantite'];
                    $this->db->query(
                        "UPDATE user_cart SET quantite = ? WHERE user_id = ? AND product_id = ?",
                        [$newQuantity, $userId, $item['product_id']]
                    );
                } else {
                    // Ajouter le nouvel item
                    $this->db->query(
                        "INSERT INTO user_cart (user_id, product_id, quantite) VALUES (?, ?, ?)",
                        [$userId, $item['product_id'], $item['quantite']]
                    );
                }
            }
            
            // Supprimer les items de session
            $this->db->query("DELETE FROM cart_sessions WHERE session_id = ?", [$this->sessionId]);
            
            $this->db->commit();
            $this->userId = $userId;
            
            return ['success' => true, 'message' => 'Panier synchronisé'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Erreur lors de la synchronisation'];
        }
    }
    
    public function validateStock() {
        $items = $this->getItems();
        $errors = [];
        
        foreach ($items as $item) {
            if ($item['quantite'] > $item['stock']) {
                $errors[] = "Stock insuffisant pour {$item['nom']} (demandé: {$item['quantite']}, disponible: {$item['stock']})";
            }
        }
        
        return empty($errors) ? ['valid' => true] : ['valid' => false, 'errors' => $errors];
    }
    
    public function applyPromoCode($promoCode) {
        $promo = $this->db->fetch("
            SELECT * FROM promotions 
            WHERE code = ? 
            AND actif = 1 
            AND (date_expiration IS NULL OR date_expiration > NOW())
            AND (utilisations_max IS NULL OR utilisations_actuelles < utilisations_max)
        ", [$promoCode]);
        
        if (!$promo) {
            return ['success' => false, 'message' => 'Code promo invalide ou expiré'];
        }
        
        $cartTotal = $this->getTotal();
        
        if ($cartTotal < $promo['montant_minimum']) {
            return [
                'success' => false, 
                'message' => "Montant minimum de {$promo['montant_minimum']} FCFA requis"
            ];
        }
        
        $discount = 0;
        if ($promo['type'] === 'pourcentage') {
            $discount = ($cartTotal * $promo['valeur']) / 100;
        } else {
            $discount = $promo['valeur'];
        }
        
        // Stocker la promo en session
        $_SESSION['applied_promo'] = [
            'id' => $promo['id'],
            'code' => $promo['code'],
            'discount' => $discount,
            'type' => $promo['type'],
            'valeur' => $promo['valeur']
        ];
        
        return [
            'success' => true, 
            'message' => 'Code promo appliqué',
            'discount' => $discount,
            'new_total' => $cartTotal - $discount
        ];
    }
    
    public function removePromoCode() {
        unset($_SESSION['applied_promo']);
        return ['success' => true, 'message' => 'Code promo retiré'];
    }
    
    public function getAppliedPromo() {
        return $_SESSION['applied_promo'] ?? null;
    }
    
    public function getFinalTotal() {
        $total = $this->getTotal();
        $promo = $this->getAppliedPromo();
        
        if ($promo) {
            $total -= $promo['discount'];
        }
        
        return max(0, $total);
    }
    
    public function cleanExpiredSessions() {
        // Nettoyer les sessions expirées (à appeler périodiquement)
        $this->db->query("DELETE FROM cart_sessions WHERE date_expiration < NOW()");
    }
}
?>