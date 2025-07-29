<?php
require_once '../includes/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$pageTitle = 'Mes commandes';
$pageDescription = 'Consultez l\'historique de vos commandes et téléchargez vos codes.';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

$db = Database::getInstance();

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Filtres
$status = $_GET['status'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

// Construction de la requête
$whereConditions = ['o.user_id = ?'];
$params = [$_SESSION['user_id']];

if ($status) {
    $whereConditions[] = 'o.statut = ?';
    $params[] = $status;
}

if ($dateFrom) {
    $whereConditions[] = 'DATE(o.date_creation) >= ?';
    $params[] = $dateFrom;
}

if ($dateTo) {
    $whereConditions[] = 'DATE(o.date_creation) <= ?';
    $params[] = $dateTo;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

// Compter le total des commandes
$totalOrders = $db->fetch("SELECT COUNT(*) as total FROM orders o $whereClause", $params)['total'];
$totalPages = ceil($totalOrders / $perPage);

// Récupérer les commandes
$orders = $db->fetchAll("
    SELECT o.*, COUNT(oi.id) as items_count
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id
    $whereClause
    GROUP BY o.id
    ORDER BY o.date_creation DESC 
    LIMIT $perPage OFFSET $offset
", $params);

include '../includes/header.php';
?>

<div class="container">
    <div class="account-layout">
        <!-- Sidebar -->
        <div class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h3><?php echo htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']); ?></h3>
                <p><?php echo htmlspecialchars($userData['email']); ?></p>
            </div>
            
            <nav class="account-nav">
                <a href="<?php echo SITE_URL; ?>/account" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="<?php echo SITE_URL; ?>/account/orders" class="nav-item active">
                    <i class="fas fa-shopping-bag"></i> Mes commandes
                </a>
                <a href="<?php echo SITE_URL; ?>/account/codes" class="nav-item">
                    <i class="fas fa-key"></i> Mes codes
                </a>
                <a href="<?php echo SITE_URL; ?>/account/profile" class="nav-item">
                    <i class="fas fa-user-edit"></i> Profil
                </a>
                <a href="<?php echo SITE_URL; ?>/logout" class="nav-item logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </nav>
        </div>

        <!-- Contenu principal -->
        <div class="account-content">
            <div class="page-header">
                <h1>Mes commandes</h1>
                <p>Consultez l'historique de vos achats et téléchargez vos codes</p>
            </div>

            <!-- Filtres -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="status">Statut :</label>
                        <select name="status" id="status">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" <?php echo $status === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="paye" <?php echo $status === 'paye' ? 'selected' : ''; ?>>Payé</option>
                            <option value="livre" <?php echo $status === 'livre' ? 'selected' : ''; ?>>Livré</option>
                            <option value="annule" <?php echo $status === 'annule' ? 'selected' : ''; ?>>Annulé</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="date_from">Du :</label>
                        <input type="date" name="date_from" id="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="date_to">Au :</label>
                        <input type="date" name="date_to" id="date_to" value="<?php echo htmlspecialchars($dateTo); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    
                    <?php if ($status || $dateFrom || $dateTo): ?>
                    <a href="<?php echo SITE_URL; ?>/account/orders" class="btn btn-outline">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Statistiques rapides -->
            <div class="stats-cards">
                <?php
                $stats = $db->fetch("
                    SELECT 
                        COUNT(*) as total_orders,
                        COUNT(CASE WHEN statut = 'livre' THEN 1 END) as delivered_orders,
                        COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as pending_orders,
                        COALESCE(SUM(total), 0) as total_spent
                    FROM orders 
                    WHERE user_id = ?
                ", [$_SESSION['user_id']]);
                ?>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Commandes totales</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['delivered_orders']; ?></h3>
                        <p>Commandes livrées</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending_orders']; ?></h3>
                        <p>En attente</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_spent'], 0, ',', ' '); ?> FCFA</h3>
                        <p>Total dépensé</p>
                    </div>
                </div>
            </div>

            <!-- Liste des commandes -->
            <div class="orders-section">
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <h3>Aucune commande trouvée</h3>
                        <p>Vous n'avez pas encore passé de commande<?php echo ($status || $dateFrom || $dateTo) ? ' correspondant aux filtres sélectionnés' : ''; ?>.</p>
                        <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Parcourir le catalogue
                        </a>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Commande #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h3>
                                    <p class="order-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($order['date_creation'])); ?>
                                    </p>
                                </div>
                                
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo $order['statut']; ?>">
                                        <?php
                                        $statusLabels = [
                                            'en_attente' => 'En attente',
                                            'paye' => 'Payé',
                                            'livre' => 'Livré',
                                            'annule' => 'Annulé'
                                        ];
                                        echo $statusLabels[$order['statut']] ?? ucfirst($order['statut']);
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="order-details">
                                <div class="order-items">
                                    <p>
                                        <i class="fas fa-box"></i>
                                        <?php echo $order['items_count']; ?> article(s)
                                    </p>
                                </div>
                                
                                <div class="order-total">
                                    <p class="total-amount">
                                        <?php echo number_format($order['total'], 0, ',', ' '); ?> FCFA
                                    </p>
                                </div>
                            </div>
                            
                            <div class="order-actions">
                                <a href="<?php echo SITE_URL; ?>/account/order/<?php echo $order['id']; ?>" 
                                   class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Voir détails
                                </a>
                                
                                <?php if ($order['statut'] === 'livre'): ?>
                                <a href="<?php echo SITE_URL; ?>/account/order/<?php echo $order['id']; ?>/codes" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i> Télécharger codes
                                </a>
                                <?php endif; ?>
                                
                                <?php if (in_array($order['statut'], ['en_attente', 'paye'])): ?>
                                <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $dateFrom ? '&date_from=' . $dateFrom : ''; ?><?php echo $dateTo ? '&date_to=' . $dateTo : ''; ?>" 
                           class="pagination-btn">
                            <i class="fas fa-chevron-left"></i> Précédent
                        </a>
                        <?php endif; ?>
                        
                        <span class="pagination-info">
                            Page <?php echo $page; ?> sur <?php echo $totalPages; ?>
                        </span>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $dateFrom ? '&date_from=' . $dateFrom : ''; ?><?php echo $dateTo ? '&date_to=' . $dateTo : ''; ?>" 
                           class="pagination-btn">
                            Suivant <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.filters-section {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.filters-form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    color: var(--text-color);
    font-size: 0.9rem;
}

.filter-group select,
.filter-group input {
    padding: 0.5rem;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    background: var(--input-bg);
    color: var(--text-color);
    min-width: 150px;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--highlight-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-content h3 {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--text-color);
    margin-bottom: 0.3rem;
}

.stat-content p {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.orders-section {
    background: var(--card-bg);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.orders-list {
    display: flex;
    flex-direction: column;
}

.order-card {
    padding: 2rem;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.3s ease;
}

.order-card:last-child {
    border-bottom: none;
}

.order-card:hover {
    background: var(--input-bg);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.order-info h3 {
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.order-date {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.order-date i {
    margin-right: 0.5rem;
}

.status-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-en_attente {
    background: var(--warning-color);
    color: var(--primary-color);
}

.status-paye {
    background: #17a2b8;
    color: white;
}

.status-livre {
    background: var(--success-color);
    color: white;
}

.status-annule {
    background: var(--danger-color);
    color: white;
}

.order-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.order-items {
    color: var(--text-muted);
}

.order-items i {
    color: var(--highlight-color);
    margin-right: 0.5rem;
}

.total-amount {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--highlight-color);
}

.order-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: var(--text-color);
    margin-bottom: 1rem;
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem;
    background: var(--input-bg);
}

.pagination-btn {
    padding: 0.8rem 1.5rem;
    background: var(--highlight-color);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.pagination-btn:hover {
    background: #c73650;
}

.pagination-info {
    color: var(--text-color);
    font-weight: 500;
}

@media (max-width: 768px) {
    .filters-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-group select,
    .filter-group input {
        min-width: auto;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
    }
    
    .order-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .order-details {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .order-actions {
        width: 100%;
        justify-content: stretch;
    }
    
    .order-actions .btn {
        flex: 1;
        text-align: center;
    }
    
    .pagination {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<script>
function cancelOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        fetch('<?php echo SITE_URL; ?>/api/orders.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=cancel&order_id=' + orderId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>