<?php
require_once '../includes/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$pageTitle = 'Mon profil';
$pageDescription = 'Gérez vos informations personnelles et paramètres de compte.';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

$errors = [];
$success = '';

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? '')
    ];
    
    $result = $user->updateProfile($_SESSION['user_id'], $data);
    
    if ($result['success']) {
        $success = 'Profil mis à jour avec succès';
        $userData = $user->getUserById($_SESSION['user_id']); // Recharger les données
    } else {
        $errors = $result['errors'];
    }
}

// Traitement du formulaire de changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $result = $user->changePassword($_SESSION['user_id'], $currentPassword, $newPassword, $confirmPassword);
    
    if ($result['success']) {
        $success = 'Mot de passe modifié avec succès';
    } else {
        $errors = $result['errors'];
    }
}

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
                <a href="<?php echo SITE_URL; ?>/account/orders" class="nav-item">
                    <i class="fas fa-shopping-bag"></i> Mes commandes
                </a>
                <a href="<?php echo SITE_URL; ?>/account/codes" class="nav-item">
                    <i class="fas fa-key"></i> Mes codes
                </a>
                <a href="<?php echo SITE_URL; ?>/account/profile" class="nav-item active">
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
                <h1>Mon profil</h1>
                <p>Gérez vos informations personnelles et paramètres de compte</p>
            </div>

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

            <div class="profile-sections">
                <!-- Informations personnelles -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user"></i> Informations personnelles</h2>
                        <p>Modifiez vos informations de base</p>
                    </div>
                    
                    <form method="POST" class="profile-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" 
                                       value="<?php echo htmlspecialchars($userData['prenom']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" class="form-control" 
                                       value="<?php echo htmlspecialchars($userData['nom']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($userData['email']); ?>" disabled>
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i>
                                L'adresse email ne peut pas être modifiée. Contactez le support si nécessaire.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" class="form-control" 
                                   value="<?php echo htmlspecialchars($userData['telephone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <textarea id="adresse" name="adresse" class="form-control" rows="3"><?php echo htmlspecialchars($userData['adresse'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Changement de mot de passe -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="fas fa-lock"></i> Sécurité</h2>
                        <p>Modifiez votre mot de passe pour sécuriser votre compte</p>
                    </div>
                    
                    <form method="POST" class="password-form">
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel *</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-control" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">Nouveau mot de passe *</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="form-control" required minlength="6">
                                <small class="form-help">Minimum 6 caractères</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirmer le mot de passe *</label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="password-strength">
                            <div class="strength-meter">
                                <div class="strength-bar" id="strength-bar"></div>
                            </div>
                            <span class="strength-text" id="strength-text">Entrez un mot de passe</span>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="change_password" class="btn btn-warning">
                                <i class="fas fa-key"></i> Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Informations du compte -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="fas fa-info-circle"></i> Informations du compte</h2>
                        <p>Détails de votre compte</p>
                    </div>
                    
                    <div class="account-info">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar-plus"></i>
                                Date de création
                            </div>
                            <div class="info-value">
                                <?php echo date('d/m/Y à H:i', strtotime($userData['date_creation'])); ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-clock"></i>
                                Dernière connexion
                            </div>
                            <div class="info-value">
                                <?php 
                                if ($userData['date_derniere_connexion']) {
                                    echo date('d/m/Y à H:i', strtotime($userData['date_derniere_connexion']));
                                } else {
                                    echo 'Jamais';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-shield-alt"></i>
                                Statut du compte
                            </div>
                            <div class="info-value">
                                <span class="status-badge status-<?php echo $userData['statut']; ?>">
                                    <?php echo ucfirst($userData['statut']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-envelope-check"></i>
                                Email vérifié
                            </div>
                            <div class="info-value">
                                <?php if ($userData['verification_email']): ?>
                                    <span class="verification-badge verified">
                                        <i class="fas fa-check"></i> Vérifié
                                    </span>
                                <?php else: ?>
                                    <span class="verification-badge not-verified">
                                        <i class="fas fa-times"></i> Non vérifié
                                    </span>
                                    <button class="btn btn-sm btn-outline" onclick="resendVerification()">
                                        Renvoyer l'email
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions du compte -->
                <div class="profile-section danger-section">
                    <div class="section-header">
                        <h2><i class="fas fa-exclamation-triangle"></i> Zone de danger</h2>
                        <p>Actions irréversibles sur votre compte</p>
                    </div>
                    
                    <div class="danger-actions">
                        <div class="danger-item">
                            <div class="danger-content">
                                <h3>Supprimer mon compte</h3>
                                <p>Cette action est irréversible. Toutes vos données seront définitivement supprimées.</p>
                            </div>
                            <button class="btn btn-danger" onclick="confirmDeleteAccount()">
                                <i class="fas fa-trash"></i> Supprimer le compte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-sections {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.profile-section {
    background: var(--card-bg);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.section-header {
    background: var(--input-bg);
    padding: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.section-header h2 {
    color: var(--text-color);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.section-header h2 i {
    color: var(--highlight-color);
}

.section-header p {
    color: var(--text-muted);
    margin: 0;
}

.profile-form,
.password-form {
    padding: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-help {
    display: block;
    margin-top: 0.5rem;
    color: var(--text-muted);
    font-size: 0.85rem;
}

.form-help i {
    color: var(--highlight-color);
    margin-right: 0.3rem;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.password-strength {
    margin: 1rem 0;
}

.strength-meter {
    width: 100%;
    height: 8px;
    background: var(--border-color);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-bar {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.strength-text {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.account-info {
    padding: 2rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    color: var(--text-color);
    font-weight: 500;
}

.info-label i {
    color: var(--highlight-color);
    width: 20px;
}

.info-value {
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.status-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: capitalize;
}

.status-actif {
    background: var(--success-color);
    color: white;
}

.status-suspendu {
    background: var(--warning-color);
    color: var(--primary-color);
}

.status-supprime {
    background: var(--danger-color);
    color: white;
}

.verification-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.verification-badge.verified {
    background: var(--success-color);
    color: white;
}

.verification-badge.not-verified {
    background: var(--warning-color);
    color: var(--primary-color);
}

.danger-section {
    border: 2px solid var(--danger-color);
}

.danger-section .section-header {
    background: rgba(220, 53, 69, 0.1);
    border-color: var(--danger-color);
}

.danger-section .section-header h2 {
    color: var(--danger-color);
}

.danger-actions {
    padding: 2rem;
}

.danger-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.danger-content h3 {
    color: var(--danger-color);
    margin-bottom: 0.5rem;
}

.danger-content p {
    color: var(--text-muted);
    margin: 0;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .danger-item {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .danger-item .btn {
        width: 100%;
    }
}
</style>

<script>
// Vérification de la force du mot de passe
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    
    let strength = 0;
    let feedback = '';
    
    if (password.length >= 6) strength += 1;
    if (password.match(/[a-z]/)) strength += 1;
    if (password.match(/[A-Z]/)) strength += 1;
    if (password.match(/[0-9]/)) strength += 1;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
    
    const percentage = (strength / 5) * 100;
    strengthBar.style.width = percentage + '%';
    
    if (strength === 0) {
        strengthBar.style.backgroundColor = '#dc3545';
        feedback = 'Très faible';
    } else if (strength <= 2) {
        strengthBar.style.backgroundColor = '#fd7e14';
        feedback = 'Faible';
    } else if (strength === 3) {
        strengthBar.style.backgroundColor = '#ffc107';
        feedback = 'Moyen';
    } else if (strength === 4) {
        strengthBar.style.backgroundColor = '#20c997';
        feedback = 'Fort';
    } else {
        strengthBar.style.backgroundColor = '#28a745';
        feedback = 'Très fort';
    }
    
    strengthText.textContent = feedback;
});

// Vérification de la confirmation du mot de passe
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Les mots de passe ne correspondent pas');
    } else {
        this.setCustomValidity('');
    }
});

// Renvoyer l'email de vérification
function resendVerification() {
    fetch('<?php echo SITE_URL; ?>/api/user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=resend_verification'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Email de vérification envoyé avec succès');
        } else {
            alert('Erreur : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
}

// Confirmation de suppression du compte
function confirmDeleteAccount() {
    if (confirm('Êtes-vous absolument sûr de vouloir supprimer votre compte ?\n\nCette action est irréversible et supprimera définitivement :\n- Toutes vos commandes\n- Tous vos codes\n- Toutes vos informations personnelles\n\nTapez "SUPPRIMER" pour confirmer')) {
        const confirmation = prompt('Tapez "SUPPRIMER" en majuscules pour confirmer :');
        if (confirmation === 'SUPPRIMER') {
            deleteAccount();
        }
    }
}

function deleteAccount() {
    fetch('<?php echo SITE_URL; ?>/api/user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=delete_account'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Votre compte a été supprimé avec succès');
            window.location.href = '<?php echo SITE_URL; ?>';
        } else {
            alert('Erreur : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
}
</script>

<?php include '../includes/footer.php'; ?>