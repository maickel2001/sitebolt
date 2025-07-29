<?php
require_once 'includes/config.php';

$pageTitle = 'Page non trouvée - Erreur 404';
$pageDescription = 'La page que vous recherchez n\'existe pas ou a été déplacée.';

// Définir le code de statut HTTP 404
http_response_code(404);

include 'includes/header.php';
?>

<div class="container">
    <div class="error-page">
        <div class="error-content">
            <div class="error-illustration">
                <div class="error-code">404</div>
                <div class="error-icon">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <div class="error-text">
                <h1>Oups ! Page non trouvée</h1>
                <p>La page que vous recherchez n'existe pas ou a été déplacée. Elle a peut-être été supprimée, renommée ou vous avez saisi une URL incorrecte.</p>
            </div>
            
            <div class="error-actions">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
                <a href="<?php echo SITE_URL; ?>/catalogue" class="btn btn-outline">
                    <i class="fas fa-shopping-bag"></i> Voir le catalogue
                </a>
            </div>
            
            <div class="search-section">
                <h3>Ou recherchez ce que vous cherchez :</h3>
                <form action="<?php echo SITE_URL; ?>/catalogue" method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="search" placeholder="Rechercher un produit..." class="search-input">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="helpful-links">
            <h3>Liens utiles</h3>
            <div class="links-grid">
                <a href="<?php echo SITE_URL; ?>/catalogue" class="helpful-link">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Catalogue des produits</span>
                </a>
                <a href="<?php echo SITE_URL; ?>/contact" class="helpful-link">
                    <i class="fas fa-envelope"></i>
                    <span>Nous contacter</span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo SITE_URL; ?>/account" class="helpful-link">
                    <i class="fas fa-user"></i>
                    <span>Mon compte</span>
                </a>
                <a href="<?php echo SITE_URL; ?>/account/orders" class="helpful-link">
                    <i class="fas fa-list"></i>
                    <span>Mes commandes</span>
                </a>
                <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/login" class="helpful-link">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Se connecter</span>
                </a>
                <a href="<?php echo SITE_URL; ?>/register" class="helpful-link">
                    <i class="fas fa-user-plus"></i>
                    <span>S'inscrire</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="popular-categories">
            <h3>Catégories populaires</h3>
            <div class="categories-list">
                <?php
                $db = Database::getInstance();
                $categories = $db->fetchAll("
                    SELECT c.*, COUNT(p.id) as product_count 
                    FROM categories c 
                    LEFT JOIN products p ON c.id = p.category_id AND p.actif = 1 AND p.stock > 0
                    WHERE c.actif = 1 
                    GROUP BY c.id 
                    ORDER BY product_count DESC, c.ordre_affichage ASC 
                    LIMIT 6
                ");
                
                foreach ($categories as $category):
                ?>
                <a href="<?php echo SITE_URL; ?>/catalogue?category=<?php echo $category['id']; ?>" 
                   class="category-link">
                    <i class="fas fa-tag"></i>
                    <span><?php echo htmlspecialchars($category['nom']); ?></span>
                    <small>(<?php echo $category['product_count']; ?> produit<?php echo $category['product_count'] > 1 ? 's' : ''; ?>)</small>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    max-width: 800px;
    margin: 2rem auto;
    text-align: center;
}

.error-content {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 4rem 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    margin-bottom: 3rem;
}

.error-illustration {
    position: relative;
    margin-bottom: 3rem;
}

.error-code {
    font-size: 8rem;
    font-weight: bold;
    color: var(--highlight-color);
    line-height: 1;
    margin-bottom: 1rem;
    text-shadow: 0 10px 30px rgba(233, 69, 96, 0.3);
}

.error-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120px;
    height: 120px;
    background: var(--accent-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.1;
}

.error-icon i {
    font-size: 4rem;
    color: var(--text-color);
}

.error-text {
    margin-bottom: 3rem;
}

.error-text h1 {
    font-size: 3rem;
    color: var(--text-color);
    margin-bottom: 1rem;
    font-weight: bold;
}

.error-text p {
    font-size: 1.2rem;
    color: var(--text-muted);
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

.error-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.search-section {
    margin-top: 3rem;
    padding-top: 3rem;
    border-top: 1px solid var(--border-color);
}

.search-section h3 {
    color: var(--text-color);
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
}

.search-form {
    max-width: 400px;
    margin: 0 auto;
}

.search-input-group {
    display: flex;
    background: var(--input-bg);
    border: 2px solid var(--border-color);
    border-radius: 50px;
    overflow: hidden;
    transition: border-color 0.3s ease;
}

.search-input-group:focus-within {
    border-color: var(--highlight-color);
}

.search-input {
    flex: 1;
    padding: 1rem 1.5rem;
    border: none;
    background: transparent;
    color: var(--text-color);
    font-size: 1rem;
}

.search-input:focus {
    outline: none;
}

.search-input::placeholder {
    color: var(--text-muted);
}

.search-btn {
    padding: 1rem 1.5rem;
    background: var(--highlight-color);
    border: none;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-btn:hover {
    background: #c73650;
}

.helpful-links {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    margin-bottom: 2rem;
}

.helpful-links h3 {
    color: var(--text-color);
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.helpful-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--input-bg);
    border-radius: 10px;
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.helpful-link:hover {
    background: var(--accent-color);
    border-color: var(--highlight-color);
    color: var(--highlight-color);
    transform: translateY(-2px);
}

.helpful-link i {
    font-size: 1.2rem;
    color: var(--highlight-color);
    width: 20px;
    text-align: center;
}

.popular-categories {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.popular-categories h3 {
    color: var(--text-color);
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.categories-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.category-link {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 1rem;
    background: var(--input-bg);
    border-radius: 10px;
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.category-link:hover {
    background: var(--accent-color);
    border-left-color: var(--highlight-color);
    transform: translateX(5px);
}

.category-link i {
    color: var(--highlight-color);
    font-size: 1rem;
}

.category-link small {
    color: var(--text-muted);
    margin-left: auto;
    font-size: 0.8rem;
}

/* Animations */
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.error-code {
    animation: bounce 2s infinite;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error-text,
.error-actions,
.search-section {
    animation: fadeInUp 0.6s ease-out;
}

.error-actions {
    animation-delay: 0.2s;
}

.search-section {
    animation-delay: 0.4s;
}

.helpful-links {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 0.6s;
}

.popular-categories {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 0.8s;
}

/* Responsive design */
@media (max-width: 768px) {
    .error-content {
        padding: 3rem 1.5rem;
        margin-bottom: 2rem;
    }
    
    .error-code {
        font-size: 6rem;
    }
    
    .error-text h1 {
        font-size: 2.5rem;
    }
    
    .error-text p {
        font-size: 1.1rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .error-actions .btn {
        width: 100%;
        max-width: 300px;
    }
    
    .links-grid {
        grid-template-columns: 1fr;
    }
    
    .categories-list {
        grid-template-columns: 1fr;
    }
    
    .helpful-links,
    .popular-categories {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .error-page {
        margin: 1rem auto;
    }
    
    .error-content {
        padding: 2rem 1rem;
    }
    
    .error-code {
        font-size: 4rem;
    }
    
    .error-text h1 {
        font-size: 2rem;
    }
    
    .error-text p {
        font-size: 1rem;
    }
    
    .search-section h3,
    .helpful-links h3,
    .popular-categories h3 {
        font-size: 1.3rem;
    }
    
    .helpful-link,
    .category-link {
        padding: 0.8rem;
    }
}

/* Mode sombre amélioré pour les erreurs */
@media (prefers-color-scheme: dark) {
    .error-illustration::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        right: -50px;
        bottom: -50px;
        background: radial-gradient(circle, rgba(233, 69, 96, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: -1;
    }
}
</style>

<?php include 'includes/footer.php'; ?>