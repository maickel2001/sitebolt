<?php
require_once '../includes/config.php';

// API pour les actions du panier
// Gère les appels AJAX depuis le JavaScript

header('Content-Type: application/json');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$action = $_POST['action'] ?? '';
$userId = $_SESSION['user_id'] ?? null;

try {
    $cart = new Cart($userId);
    
    switch ($action) {
        case 'add':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            if ($productId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Produit invalide']);
                exit;
            }
            
            // Vérifier si l'utilisateur est connecté pour l'achat
            if (!$userId) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Connexion requise',
                    'require_login' => true
                ]);
                exit;
            }
            
            $result = $cart->addItem($productId, $quantity);
            echo json_encode($result);
            break;
            
        case 'update':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            
            $result = $cart->updateItem($productId, $quantity);
            echo json_encode($result);
            break;
            
        case 'remove':
            $productId = (int)($_POST['product_id'] ?? 0);
            
            $result = $cart->removeItem($productId);
            echo json_encode($result);
            break;
            
        case 'get_count':
            $count = $cart->getItemCount();
            echo json_encode(['success' => true, 'count' => $count]);
            break;
            
        case 'get_total':
            $total = $cart->getFinalTotal();
            echo json_encode(['success' => true, 'total' => $total]);
            break;
            
        case 'sync':
            // Synchroniser le panier depuis localStorage
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
                exit;
            }
            
            $cartData = json_decode($_POST['cart_data'] ?? '[]', true);
            
            foreach ($cartData as $item) {
                if (isset($item['id'], $item['quantity'])) {
                    $cart->addItem($item['id'], $item['quantity']);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Panier synchronisé']);
            break;
            
        case 'apply_promo':
            $promoCode = $_POST['promo_code'] ?? '';
            
            if (empty($promoCode)) {
                echo json_encode(['success' => false, 'message' => 'Code promo requis']);
                exit;
            }
            
            $result = $cart->applyPromoCode($promoCode);
            echo json_encode($result);
            break;
            
        case 'remove_promo':
            $result = $cart->removePromoCode();
            echo json_encode($result);
            break;
            
        case 'validate':
            $validation = $cart->validateStock();
            echo json_encode($validation);
            break;
            
        case 'clear':
            $result = $cart->clear();
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Erreur API Cart: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => DEBUG_MODE ? $e->getMessage() : 'Erreur interne'
    ]);
}
?>