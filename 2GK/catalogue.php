<?php
require_once 'includes/config.php';

$pageTitle = 'Catalogue';
$pageDescription = 'Découvrez tous nos produits numériques : cartes cadeaux, codes d\'abonnement, licences logicielles avec livraison instantanée.';

$db = Database::getInstance();

// Paramètres de filtrage et pagination
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$country = $_GET['country'] ?? '';
$platform = $_GET['platform'] ?? '';
$sortBy = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Construction de la requête
$whereConditions = ['p.actif = 1'];
$params = [];

if (!empty($search)) {
    $whereConditions[] = '(p.nom LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $whereConditions[] = 'p.category_id = ?';
    $params[] = $category;
}

if (!empty($country)) {
    $whereConditions[] = 'p.pays = ?';
    $params[] = $country;
}

if (!empty($platform)) {
    $whereConditions[] = 'p.plateforme = ?';
    $params[] = $platform;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

// Tri
$orderBy = match($sortBy) {
    'price_asc' => 'ORDER BY p.prix ASC',
    'price_desc' => 'ORDER BY p.prix DESC',
    'name' => 'ORDER BY p.nom ASC',
    'popular' => 'ORDER BY (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.id) DESC',
    default => 'ORDER BY p.date_creation DESC'
};

// Récupérer les produits
$sql = "
    SELECT p.*, c.nom as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    $whereClause 
    $orderBy 
    LIMIT $perPage OFFSET $offset
";

$products = $db->fetchAll($sql, $params);

// Compter le total pour la pagination
$countSql = "
    SELECT COUNT(*) as total 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    $whereClause
";

$totalResult = $db->fetch($countSql, $params);
$totalProducts = $totalResult['total'];
$totalPages = ceil($totalProducts / $perPage);

// Récupérer les options de filtres
$categories = $db->fetchAll("SELECT * FROM categories WHERE actif = 1 ORDER BY nom");
$countries = $db->fetchAll("SELECT DISTINCT pays FROM products WHERE pays IS NOT NULL AND pays != '' ORDER BY pays");
$platforms = $db->fetchAll("SELECT DISTINCT plateforme FROM products WHERE plateforme IS NOT NULL AND plateforme != '' ORDER BY plateforme");

include 'includes/header.php';
?>

<div class="container">
    <!-- Header du catalogue -->
    <div class="catalogue-header">
        <h1>Catalogue de produits</h1>
        <p>Découvrez notre sélection de <?php echo number_format($totalProducts); ?> produits numériques</p>
    </div>

    <!-- Filtres -->
    <div class="filters">
        <form method="GET" action="" id="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" class="filter-input" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Nom du produit...">
                </div>
                
                <div class="filter-group">
                    <label for="category">Catégorie</label>
                    <select id="category" name="category" class="filter-input">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="country">Pays</label>
                    <select id="country" name="country" class="filter-input">
                        <option value="">Tous les pays</option>
                        <?php foreach ($countries as $countryOption): ?>
                            <option value="<?php echo htmlspecialchars($countryOption['pays']); ?>" 
                                    <?php echo $country == $countryOption['pays'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($countryOption['pays']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="platform">Plateforme</label>
                    <select id="platform" name="platform" class="filter-input">
                        <option value="">Toutes les plateformes</option>
                        <?php foreach ($platforms as $platformOption): ?>
                            <option value="<?php echo htmlspecialchars($platformOption['plateforme']); ?>" 
                                    <?php echo $platform == $platformOption['plateforme'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($platformOption['plateforme']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sort">Trier par</label>
                    <select id="sort" name="sort" class="filter-input">
                        <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>Plus récents</option>
                        <option value="popular" <?php echo $sortBy == 'popular' ? 'selected' : ''; ?>>Plus populaires</option>
                        <option value="price_asc" <?php echo $sortBy == 'price_asc' ? 'selected' : ''; ?>>Prix croissant</option>
                        <option value="price_desc" <?php echo $sortBy == 'price_desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                        <option value="name" <?php echo $sortBy == 'name' ? 'selected' : ''; ?>>Nom A-Z</option>
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Résultats -->
    <div class="catalogue-results">
        <div class="results-header">
            <div class="results-info">
                <?php if ($totalProducts > 0): ?>
                    <p>
                        <?php echo number_format($totalProducts); ?> produit(s) trouvé(s)
                        <?php if ($page > 1): ?>
                            - Page <?php echo $page; ?> sur <?php echo $totalPages; ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <!-- Aucun résultat -->
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Aucun produit trouvé</h3>
                <p>Essayez de modifier vos critères de recherche ou parcourez toutes les catégories.</p>
                <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-primary">Voir tous les produits</a>
            </div>
        <?php else: ?>
            <!-- Grille de produits -->
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="<?php echo SITE_URL . '/uploads/products/' . $product['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['nom']); ?>">
                        <?php else: ?>
                            <i class="fas fa-image"></i>
                        <?php endif; ?>
                        
                        <?php if ($product['stock'] == 0): ?>
                            <div class="product-badge out-of-stock">Rupture de stock</div>
                        <?php elseif ($product['stock'] <= 5): ?>
                            <div class="product-badge low-stock">Stock faible</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="<?php echo SITE_URL; ?>/product/<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['nom']); ?>
                            </a>
                        </h3>
                        
                        <p class="product-description">
                            <?php echo htmlspecialchars(substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : '')); ?>
                        </p>
                        
                        <div class="product-meta">
                            <?php if ($product['category_name']): ?>
                                <span class="meta-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <?php endif; ?>
                            <?php if ($product['pays']): ?>
                                <span class="product-country"><?php echo htmlspecialchars($product['pays']); ?></span>
                            <?php endif; ?>
                            <?php if ($product['plateforme']): ?>
                                <span class="product-platform"><?php echo htmlspecialchars($product['plateforme']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-price">
                            <?php echo number_format($product['prix'], 0, ',', ' '); ?> FCFA
                        </div>
                        
                        <div class="product-stock <?php echo $product['stock'] > 10 ? 'stock-available' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                            <?php if ($product['stock'] > 0): ?>
                                <?php echo $product['stock']; ?> disponible(s)
                            <?php else: ?>
                                Rupture de stock
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <button class="btn btn-primary btn-full add-to-cart" 
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
                            
                            <a href="<?php echo SITE_URL; ?>/product/<?php echo $product['id']; ?>" 
                               class="btn btn-outline btn-full">
                                <i class="fas fa-eye"></i> Voir détails
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                       class="pagination-btn">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </a>
                <?php endif; ?>
                
                <div class="pagination-numbers">
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    if ($start > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" 
                           class="pagination-number">1</a>
                        <?php if ($start > 2): ?>
                            <span class="pagination-dots">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="pagination-number active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                               class="pagination-number"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <span class="pagination-dots">...</span>
                        <?php endif; ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>" 
                           class="pagination-number"><?php echo $totalPages; ?></a>
                    <?php endif; ?>
                </div>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                       class="pagination-btn">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.catalogue-header {
    text-align: center;
    margin-bottom: 3rem;
}

.catalogue-header h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, var(--highlight-color), #ff6b9d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.filter-actions {
    display: flex;
    gap: 1rem;
    align-items: end;
}

.results-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.results-info p {
    color: var(--text-muted);
    margin: 0;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
}

.no-results-icon {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.no-results h3 {
    margin-bottom: 1rem;
    color: var(--text-color);
}

.no-results p {
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.out-of-stock {
    background: var(--danger-color);
    color: white;
}

.low-stock {
    background: var(--warning-color);
    color: var(--primary-color);
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-category {
    background: var(--highlight-color);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.8rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 3rem;
    padding: 2rem 0;
}

.pagination-btn {
    background: var(--accent-color);
    color: var(--text-color);
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.pagination-btn:hover {
    background: var(--highlight-color);
}

.pagination-numbers {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.pagination-number {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-color);
    transition: all 0.3s ease;
}

.pagination-number:hover {
    background: var(--accent-color);
}

.pagination-number.active {
    background: var(--highlight-color);
    color: white;
}

.pagination-dots {
    color: var(--text-muted);
    padding: 0 0.5rem;
}

@media (max-width: 768px) {
    .catalogue-header h1 {
        font-size: 2rem;
    }
    
    .filter-row {
        flex-direction: column;
    }
    
    .filter-actions {
        justify-content: center;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
    
    .pagination-numbers {
        order: -1;
        width: 100%;
        justify-content: center;
        margin-bottom: 1rem;
    }
}
</style>

<script>
// Auto-submit des filtres
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('.filter-input');
    
    filterInputs.forEach(input => {
        if (input.type === 'text') {
            // Délai pour la recherche textuelle
            let timeout;
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    document.getElementById('filter-form').submit();
                }, 500);
            });
        } else {
            // Submit immédiat pour les selects
            input.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>