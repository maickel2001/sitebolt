<?php
require_once 'includes/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$pageTitle = 'Mon compte';
$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

if (!$userData) {
    session_destroy();
    header('Location: ' . SITE_URL . '/login');
    exit;
}

// Récupérer les commandes récentes
$recentOrders = $user->getUserOrders($_SESSION['user_id'], 5);

// Récupérer les statistiques
$db = Database::getInstance();
$stats = $db->fetch("
    SELECT 
        COUNT(DISTINCT o.id) as total_orders,
        SUM(o.total) as total_spent,
        COUNT(DISTINCT CASE WHEN o.statut = 'livre' THEN o.id END) as completed_orders
    FROM orders o 
    WHERE o.user_id = ?
", [$_SESSION['user_id']]);

include 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3>Mon compte</h3>
                </div>
                <div class="account-menu">
                    <a href="<?php echo SITE_URL; ?>/account" class="menu-item active">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                    <a href="<?php echo SITE_URL; ?>/account/orders" class="menu-item">
                        <i class="fas fa-shopping-bag"></i> Mes commandes
                    </a>
                    <a href="<?php echo SITE_URL; ?>/account/profile" class="menu-item">
                        <i class="fas fa-user"></i> Mon profil
                    </a>
                    <a href="<?php echo SITE_URL; ?>/account/password" class="menu-item">
                        <i class="fas fa-lock"></i> Mot de passe
                    </a>
                    <a href="<?php echo SITE_URL; ?>/account/downloads" class="menu-item">
                        <i class="fas fa-download"></i> Téléchargements
                    </a>
                    <a href="<?php echo SITE_URL; ?>/logout" class="menu-item">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Welcome Message -->
            <div class="card mb-3">
                <div class="card-header">
                    <h2>Bienvenue, <?php echo htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']); ?> !</h2>
                </div>
                <div style="padding: 1.5rem;">
                    <p>Voici un aperçu de votre compte et de vos activités récentes.</p>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_orders'] ?? 0; ?></h3>
                        <p>Commandes totales</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['completed_orders'] ?? 0; ?></h3>
                        <p>Commandes livrées</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_spent'] ?? 0, 0, ',', ' '); ?> FCFA</h3>
                        <p>Total dépensé</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h3>Commandes récentes</h3>
                    <a href="<?php echo SITE_URL; ?>/account/orders" class="btn btn-secondary" style="margin-left: auto;">
                        Voir toutes
                    </a>
                </div>
                
                <?php if (!empty($recentOrders)): ?>
                <div class="orders-table">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 1rem; text-align: left;">N° Commande</th>
                                <th style="padding: 1rem; text-align: left;">Date</th>
                                <th style="padding: 1rem; text-align: left;">Articles</th>
                                <th style="padding: 1rem; text-align: left;">Total</th>
                                <th style="padding: 1rem; text-align: left;">Statut</th>
                                <th style="padding: 1rem; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem;">
                                    <strong><?php echo htmlspecialchars($order['numero_commande']); ?></strong>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php echo date('d/m/Y', strtotime($order['date_commande'])); ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php echo $order['total_items']; ?> article(s)
                                </td>
                                <td style="padding: 1rem;">
                                    <strong><?php echo number_format($order['total'], 0, ',', ' '); ?> FCFA</strong>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php
                                    $statusClass = [
                                        'en_attente' => 'warning',
                                        'paye' => 'info',
                                        'livre' => 'success',
                                        'annule' => 'danger',
                                        'rembourse' => 'secondary'
                                    ];
                                    $statusText = [
                                        'en_attente' => 'En attente',
                                        'paye' => 'Payé',
                                        'livre' => 'Livré',
                                        'annule' => 'Annulé',
                                        'rembourse' => 'Remboursé'
                                    ];
                                    ?>
                                    <span class="status-badge status-<?php echo $statusClass[$order['statut']] ?? 'secondary'; ?>">
                                        <?php echo $statusText[$order['statut']] ?? $order['statut']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <a href="<?php echo SITE_URL; ?>/account/order/<?php echo $order['id']; ?>" 
                                       class="btn btn-sm btn-outline">Voir</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div style="padding: 2rem; text-align: center;">
                    <p style="color: var(--text-muted);">Aucune commande trouvée</p>
                    <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-primary" style="margin-top: 1rem;">
                        Commencer vos achats
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
    padding: 0 1rem;
}

.col-md-9 {
    flex: 0 0 75%;
    max-width: 75%;
    padding: 0 1rem;
}

.account-menu {
    padding: 0;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 1rem 1.5rem;
    color: var(--text-color);
    text-decoration: none;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.3s ease;
}

.menu-item:hover,
.menu-item.active {
    background: var(--accent-color);
    color: var(--highlight-color);
}

.menu-item:last-child {
    border-bottom: none;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--highlight-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-content h3 {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.stat-content p {
    color: var(--text-muted);
    margin: 0;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-success {
    background: rgba(40, 167, 69, 0.2);
    color: var(--success-color);
}

.status-warning {
    background: rgba(255, 193, 7, 0.2);
    color: var(--warning-color);
}

.status-info {
    background: rgba(23, 162, 184, 0.2);
    color: #17a2b8;
}

.status-danger {
    background: rgba(220, 53, 69, 0.2);
    color: var(--danger-color);
}

.status-secondary {
    background: rgba(108, 117, 125, 0.2);
    color: #6c757d;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .col-md-3,
    .col-md-9 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .col-md-3 {
        margin-bottom: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
    
    .orders-table {
        overflow-x: auto;
    }
    
    .card-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
}
</style>

<?php include 'includes/footer.php'; ?>