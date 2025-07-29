<?php
require_once 'includes/config.php';

$pageTitle = 'Erreur interne du serveur - Erreur 500';
$pageDescription = 'Une erreur interne s\'est produite sur le serveur. Nous travaillons à résoudre le problème.';

// Définir le code de statut HTTP 500
http_response_code(500);

include 'includes/header.php';
?>

<div class="container">
    <div class="error-page">
        <div class="error-content">
            <div class="error-illustration">
                <div class="error-code">500</div>
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="error-gears">
                    <i class="fas fa-cog gear-1"></i>
                    <i class="fas fa-cog gear-2"></i>
                </div>
            </div>
            
            <div class="error-text">
                <h1>Oups ! Erreur interne</h1>
                <p>Une erreur inattendue s'est produite sur nos serveurs. Nos équipes techniques ont été automatiquement notifiées et travaillent à résoudre le problème dans les plus brefs délais.</p>
            </div>
            
            <div class="error-actions">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
                <button onclick="window.location.reload()" class="btn btn-outline">
                    <i class="fas fa-redo"></i> Réessayer
                </button>
            </div>
            
            <div class="status-info">
                <div class="status-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>Heure de l'incident</strong>
                        <span><?php echo date('d/m/Y à H:i:s'); ?></span>
                    </div>
                </div>
                <div class="status-item">
                    <i class="fas fa-tools"></i>
                    <div>
                        <strong>Statut</strong>
                        <span>Nos équipes sont sur le problème</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="help-section">
            <h3>Que pouvez-vous faire ?</h3>
            <div class="help-grid">
                <div class="help-item">
                    <i class="fas fa-redo-alt"></i>
                    <h4>Réessayez dans quelques minutes</h4>
                    <p>Le problème peut être temporaire. Actualisez la page ou revenez dans quelques minutes.</p>
                </div>
                <div class="help-item">
                    <i class="fas fa-history"></i>
                    <h4>Vérifiez votre historique</h4>
                    <p>Si vous étiez en train de passer une commande, vérifiez votre espace client.</p>
                </div>
                <div class="help-item">
                    <i class="fas fa-envelope"></i>
                    <h4>Contactez-nous</h4>
                    <p>Si le problème persiste, n'hésitez pas à nous contacter pour obtenir de l'aide.</p>
                </div>
            </div>
        </div>
        
        <div class="contact-support">
            <div class="support-card">
                <div class="support-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="support-content">
                    <h3>Besoin d'aide immédiate ?</h3>
                    <p>Notre équipe support est disponible pour vous aider</p>
                    <div class="support-actions">
                        <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> Nous contacter
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo SITE_URL; ?>/account" class="btn btn-outline">
                            <i class="fas fa-user"></i> Mon compte
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="technical-info">
            <details>
                <summary>Informations techniques</summary>
                <div class="tech-details">
                    <div class="tech-item">
                        <strong>Code d'erreur :</strong> HTTP 500 - Internal Server Error
                    </div>
                    <div class="tech-item">
                        <strong>Timestamp :</strong> <?php echo date('c'); ?>
                    </div>
                    <div class="tech-item">
                        <strong>Serveur :</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Non disponible'; ?>
                    </div>
                    <div class="tech-item">
                        <strong>User Agent :</strong> <?php echo substr($_SERVER['HTTP_USER_AGENT'] ?? 'Non disponible', 0, 100); ?>
                    </div>
                    <?php if (DEBUG_MODE): ?>
                    <div class="tech-item">
                        <strong>URL demandée :</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'Non disponible'; ?>
                    </div>
                    <div class="tech-item">
                        <strong>Méthode :</strong> <?php echo $_SERVER['REQUEST_METHOD'] ?? 'Non disponible'; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </details>
        </div>
    </div>
</div>

<style>
.error-page {
    max-width: 900px;
    margin: 2rem auto;
    text-align: center;
}

.error-content {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 4rem 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.error-content::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(220, 53, 69, 0.05) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
}

.error-illustration {
    position: relative;
    margin-bottom: 3rem;
    z-index: 2;
}

.error-code {
    font-size: 8rem;
    font-weight: bold;
    color: var(--danger-color);
    line-height: 1;
    margin-bottom: 1rem;
    text-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
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
    color: var(--danger-color);
    animation: shake 2s ease-in-out infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.error-gears {
    position: absolute;
    top: 20px;
    right: 20px;
}

.gear-1, .gear-2 {
    position: absolute;
    color: var(--danger-color);
    opacity: 0.3;
}

.gear-1 {
    font-size: 2rem;
    animation: rotate 3s linear infinite;
}

.gear-2 {
    font-size: 1.5rem;
    top: 15px;
    left: 20px;
    animation: rotate-reverse 2s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes rotate-reverse {
    from { transform: rotate(360deg); }
    to { transform: rotate(0deg); }
}

.error-text {
    margin-bottom: 3rem;
    z-index: 2;
    position: relative;
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
    z-index: 2;
    position: relative;
}

.status-info {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 3rem;
    padding-top: 3rem;
    border-top: 1px solid var(--border-color);
    flex-wrap: wrap;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--input-bg);
    padding: 1rem 1.5rem;
    border-radius: 10px;
    border-left: 4px solid var(--danger-color);
}

.status-item i {
    font-size: 1.5rem;
    color: var(--danger-color);
}

.status-item div {
    text-align: left;
}

.status-item strong {
    display: block;
    color: var(--text-color);
    margin-bottom: 0.3rem;
}

.status-item span {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.help-section {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    margin-bottom: 2rem;
}

.help-section h3 {
    color: var(--text-color);
    margin-bottom: 2rem;
    font-size: 1.8rem;
}

.help-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.help-item {
    text-align: center;
    padding: 1.5rem;
    background: var(--input-bg);
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.help-item:hover {
    transform: translateY(-5px);
}

.help-item i {
    font-size: 3rem;
    color: var(--highlight-color);
    margin-bottom: 1rem;
}

.help-item h4 {
    color: var(--text-color);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.help-item p {
    color: var(--text-muted);
    line-height: 1.6;
    font-size: 0.95rem;
}

.contact-support {
    margin-bottom: 2rem;
}

.support-card {
    background: linear-gradient(135deg, var(--highlight-color), #ff6b9d);
    border-radius: 20px;
    padding: 3rem 2rem;
    color: white;
    box-shadow: 0 15px 40px rgba(233, 69, 96, 0.3);
}

.support-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.support-content h3 {
    font-size: 2rem;
    margin-bottom: 1rem;
    font-weight: bold;
}

.support-content p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.support-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.support-actions .btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    backdrop-filter: blur(10px);
}

.support-actions .btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
}

.technical-info {
    background: var(--card-bg);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.technical-info details {
    padding: 1.5rem;
}

.technical-info summary {
    color: var(--text-color);
    font-weight: 500;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.technical-info summary:hover {
    background: var(--input-bg);
}

.tech-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.tech-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}

.tech-item:last-child {
    border-bottom: none;
}

.tech-item strong {
    color: var(--text-color);
    min-width: 150px;
}

.tech-item:last-of-type {
    color: var(--text-muted);
    word-break: break-all;
}

/* Animations d'entrée */
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
.status-info {
    animation: fadeInUp 0.6s ease-out;
}

.error-actions {
    animation-delay: 0.2s;
}

.status-info {
    animation-delay: 0.4s;
}

.help-section {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 0.6s;
}

.contact-support {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 0.8s;
}

.technical-info {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 1s;
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
    
    .status-info {
        flex-direction: column;
        gap: 1rem;
    }
    
    .help-grid {
        grid-template-columns: 1fr;
    }
    
    .support-actions {
        flex-direction: column;
    }
    
    .support-actions .btn {
        width: 100%;
        max-width: 250px;
    }
    
    .help-section,
    .support-card {
        padding: 1.5rem;
    }
    
    .tech-item {
        flex-direction: column;
        gap: 0.3rem;
    }
    
    .tech-item strong {
        min-width: auto;
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
    
    .help-section h3,
    .support-content h3 {
        font-size: 1.5rem;
    }
    
    .help-item {
        padding: 1rem;
    }
    
    .help-item i {
        font-size: 2.5rem;
    }
    
    .support-icon {
        font-size: 3rem;
    }
}

/* Effet de glitch pour le code d'erreur */
@keyframes glitch {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-2px); }
    40% { transform: translateX(2px); }
    60% { transform: translateX(-1px); }
    80% { transform: translateX(1px); }
}

.error-code:hover {
    animation: glitch 0.3s ease-in-out;
}
</style>

<?php include 'includes/footer.php'; ?>