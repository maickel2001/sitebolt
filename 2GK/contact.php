<?php
require_once 'includes/config.php';

$pageTitle = 'Contact';
$pageDescription = 'Contactez l\'équipe de 2GK pour toute question ou assistance. Support disponible 24/7 pour vous accompagner.';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $userId = $_SESSION['user_id'] ?? null;
    
    // Validation
    if (empty($nom)) {
        $errors['nom'] = 'Le nom est requis';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email invalide';
    }
    
    if (empty($sujet)) {
        $errors['sujet'] = 'Le sujet est requis';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Le message est requis';
    }
    
    // Vérification reCAPTCHA si configuré
    if (RECAPTCHA_SECRET_KEY && (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response']))) {
        $errors['recaptcha'] = 'Veuillez cocher la case reCAPTCHA';
    } elseif (RECAPTCHA_SECRET_KEY) {
        $recaptcha = $_POST['g-recaptcha-response'];
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_SECRET_KEY . "&response=" . $recaptcha);
        $responseKeys = json_decode($response, true);
        
        if (!$responseKeys["success"]) {
            $errors['recaptcha'] = 'Vérification reCAPTCHA échouée';
        }
    }
    
    if (empty($errors)) {
        try {
            $db = Database::getInstance();
            
            $sql = "INSERT INTO messages (user_id, nom, email, sujet, contenu, statut) VALUES (?, ?, ?, ?, ?, 'nouveau')";
            $db->query($sql, [$userId, $nom, $email, $sujet, $message]);
            
            $success = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';
            
            // Réinitialiser le formulaire
            $nom = $email = $sujet = $message = '';
            
        } catch (Exception $e) {
            $errors['general'] = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <!-- Header de la page -->
    <div class="page-header">
        <h1>Nous contacter</h1>
        <p>Une question ? Un problème ? Notre équipe est là pour vous aider !</p>
    </div>

    <div class="contact-content">
        <div class="row">
            <!-- Informations de contact -->
            <div class="col-md-4">
                <div class="contact-info">
                    <h2>Informations de contact</h2>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Email</h3>
                            <p><?php echo SITE_EMAIL; ?></p>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>" class="btn btn-outline btn-sm">
                                Nous écrire
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Horaires de support</h3>
                            <p>Lundi - Vendredi : 9h - 18h</p>
                            <p>Weekend : 10h - 16h</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Support client</h3>
                            <p>Réponse sous 24h maximum</p>
                            <p>Support disponible 7j/7</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Livraison</h3>
                            <p>Instantanée pour les produits automatiques</p>
                            <p>Sous 24h pour la livraison manuelle</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ rapide -->
                <div class="quick-faq">
                    <h3>Questions fréquentes</h3>
                    <div class="faq-links">
                        <a href="<?php echo SITE_URL; ?>/faq#livraison">
                            <i class="fas fa-truck"></i> Comment fonctionne la livraison ?
                        </a>
                        <a href="<?php echo SITE_URL; ?>/faq#paiement">
                            <i class="fas fa-credit-card"></i> Quels moyens de paiement ?
                        </a>
                        <a href="<?php echo SITE_URL; ?>/faq#codes">
                            <i class="fas fa-key"></i> Mes codes ne fonctionnent pas
                        </a>
                        <a href="<?php echo SITE_URL; ?>/faq#remboursement">
                            <i class="fas fa-undo"></i> Politique de remboursement
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de contact -->
            <div class="col-md-8">
                <div class="contact-form-container">
                    <h2>Envoyez-nous un message</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
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
                    
                    <form method="POST" action="" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom complet *</label>
                                <input type="text" id="nom" name="nom" class="form-control" 
                                       value="<?php echo htmlspecialchars($nom ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="sujet">Sujet *</label>
                            <select id="sujet" name="sujet" class="form-control" required>
                                <option value="">Choisissez un sujet</option>
                                <option value="Question générale" <?php echo ($sujet ?? '') == 'Question générale' ? 'selected' : ''; ?>>Question générale</option>
                                <option value="Problème de commande" <?php echo ($sujet ?? '') == 'Problème de commande' ? 'selected' : ''; ?>>Problème de commande</option>
                                <option value="Code défaillant" <?php echo ($sujet ?? '') == 'Code défaillant' ? 'selected' : ''; ?>>Code défaillant</option>
                                <option value="Problème de paiement" <?php echo ($sujet ?? '') == 'Problème de paiement' ? 'selected' : ''; ?>>Problème de paiement</option>
                                <option value="Demande de remboursement" <?php echo ($sujet ?? '') == 'Demande de remboursement' ? 'selected' : ''; ?>>Demande de remboursement</option>
                                <option value="Suggestion d'amélioration" <?php echo ($sujet ?? '') == 'Suggestion d\'amélioration' ? 'selected' : ''; ?>>Suggestion d'amélioration</option>
                                <option value="Autre" <?php echo ($sujet ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" class="form-control" rows="6" 
                                      placeholder="Décrivez votre demande en détail..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            <small class="form-help">
                                Pour un problème de commande, merci d'indiquer votre numéro de commande.
                            </small>
                        </div>
                        
                        <?php if (RECAPTCHA_SITE_KEY): ?>
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-large">
                                <i class="fas fa-paper-plane"></i> Envoyer le message
                            </button>
                            <p class="form-note">
                                <i class="fas fa-info-circle"></i>
                                Nous nous engageons à vous répondre sous 24h maximum.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section avantages -->
    <div class="contact-advantages">
        <h2>Pourquoi nous choisir ?</h2>
        <div class="advantages-grid">
            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-shield-check"></i>
                </div>
                <h3>Fiabilité garantie</h3>
                <p>Tous nos codes sont testés et garantis fonctionnels</p>
            </div>
            
            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-lightning-bolt"></i>
                </div>
                <h3>Livraison rapide</h3>
                <p>Livraison instantanée ou sous 24h maximum</p>
            </div>
            
            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Support réactif</h3>
                <p>Une équipe dédiée pour résoudre vos problèmes</p>
            </div>
            
            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Paiement sécurisé</h3>
                <p>Transactions protégées avec KiaPay</p>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    text-align: center;
    margin-bottom: 4rem;
    padding: 2rem 0;
}

.page-header h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, var(--highlight-color), #ff6b9d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-header p {
    font-size: 1.2rem;
    color: var(--text-muted);
}

.contact-content {
    margin-bottom: 4rem;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding: 0 1rem;
}

.col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
    padding: 0 1rem;
}

.contact-info {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    margin-bottom: 2rem;
}

.contact-info h2 {
    margin-bottom: 2rem;
    color: var(--text-color);
    text-align: center;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.contact-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: var(--highlight-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.contact-details h3 {
    margin-bottom: 0.5rem;
    color: var(--text-color);
    font-size: 1.1rem;
}

.contact-details p {
    color: var(--text-muted);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.quick-faq {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.quick-faq h3 {
    margin-bottom: 1.5rem;
    color: var(--text-color);
    text-align: center;
}

.faq-links {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.faq-links a {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 1rem;
    background: var(--input-bg);
    border-radius: 8px;
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.faq-links a:hover {
    background: var(--accent-color);
    color: var(--highlight-color);
}

.faq-links a i {
    color: var(--highlight-color);
    width: 16px;
}

.contact-form-container {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.contact-form-container h2 {
    margin-bottom: 2rem;
    color: var(--text-color);
    text-align: center;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.form-help {
    display: block;
    margin-top: 0.5rem;
    color: var(--text-muted);
    font-size: 0.8rem;
}

.form-actions {
    text-align: center;
    margin-top: 2rem;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: bold;
}

.form-note {
    margin-top: 1rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.form-note i {
    color: var(--highlight-color);
    margin-right: 0.5rem;
}

.alert ul {
    margin: 0.5rem 0 0 1.5rem;
    padding: 0;
}

.alert li {
    margin-bottom: 0.3rem;
}

.contact-advantages {
    background: var(--card-bg);
    padding: 4rem 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.contact-advantages h2 {
    font-size: 2.5rem;
    margin-bottom: 3rem;
    color: var(--text-color);
}

.advantages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.advantage-item {
    padding: 2rem;
    background: var(--input-bg);
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.advantage-item:hover {
    transform: translateY(-5px);
}

.advantage-icon {
    width: 80px;
    height: 80px;
    background: var(--highlight-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
}

.advantage-item h3 {
    margin-bottom: 1rem;
    color: var(--text-color);
    font-size: 1.3rem;
}

.advantage-item p {
    color: var(--text-muted);
    line-height: 1.6;
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2.5rem;
    }
    
    .col-md-4,
    .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .col-md-4 {
        margin-bottom: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .contact-item {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .advantages-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-advantages {
        padding: 2rem 1rem;
    }
    
    .contact-advantages h2 {
        font-size: 2rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>