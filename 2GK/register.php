<?php
require_once 'includes/config.php';

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/account');
    exit;
}

$pageTitle = 'Inscription';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
        'nom' => $_POST['nom'] ?? '',
        'prenom' => $_POST['prenom'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'adresse' => $_POST['adresse'] ?? ''
    ];
    
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
        $user = new User();
        $result = $user->register($data);
        
        if ($result['success']) {
            $success = $result['message'];
            // Optionnel : connecter automatiquement l'utilisateur
            // $_SESSION['user_id'] = $result['user_id'];
        } else {
            $errors = $result['errors'] ?? ['general' => 'Erreur lors de l\'inscription'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title text-center">Inscription</h2>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $field => $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                        <p><a href="<?php echo SITE_URL; ?>/login" style="color: var(--success-color);">Se connecter maintenant</a></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" class="form-control" 
                                   value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
                            <?php if (isset($errors['prenom'])): ?>
                                <small style="color: var(--danger-color);"><?php echo $errors['prenom']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" class="form-control" 
                                   value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                            <?php if (isset($errors['nom'])): ?>
                                <small style="color: var(--danger-color);"><?php echo $errors['nom']; ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <small style="color: var(--danger-color);"><?php echo $errors['email']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>" 
                               placeholder="+229 XX XX XX XX">
                        <?php if (isset($errors['telephone'])): ?>
                            <small style="color: var(--danger-color);"><?php echo $errors['telephone']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea id="adresse" name="adresse" class="form-control" rows="3" 
                                  placeholder="Votre adresse complète"><?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="password">Mot de passe *</label>
                            <input type="password" id="password" name="password" class="form-control" 
                                   minlength="<?php echo PASSWORD_MIN_LENGTH; ?>" required>
                            <small style="color: var(--text-muted);">Minimum <?php echo PASSWORD_MIN_LENGTH; ?> caractères</small>
                            <?php if (isset($errors['password'])): ?>
                                <small style="color: var(--danger-color); display: block;"><?php echo $errors['password']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Confirmer le mot de passe *</label>
                            <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                            <?php if (isset($errors['password_confirm'])): ?>
                                <small style="color: var(--danger-color);"><?php echo $errors['password_confirm']; ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (RECAPTCHA_SITE_KEY): ?>
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                        <?php if (isset($errors['recaptcha'])): ?>
                            <small style="color: var(--danger-color);"><?php echo $errors['recaptcha']; ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <input type="checkbox" required style="margin-top: 0.2rem;">
                            <span>J'accepte les <a href="<?php echo SITE_URL; ?>/cgv" target="_blank" style="color: var(--highlight-color);">conditions générales de vente</a> et la <a href="<?php echo SITE_URL; ?>/politique-confidentialite" target="_blank" style="color: var(--highlight-color);">politique de confidentialité</a></span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-full">S'inscrire</button>
                    </div>
                </form>
                
                <div class="text-center" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <p>Déjà un compte ? <a href="<?php echo SITE_URL; ?>/login" style="color: var(--highlight-color);">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
    padding: 0 1rem;
}

@media (max-width: 768px) {
    .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .form-group > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}

textarea.form-control {
    min-height: 80px;
    resize: vertical;
}

.g-recaptcha {
    margin: 1rem 0;
}
</style>

<script>
// Validation côté client
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    function validatePasswords() {
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);
});
</script>

<?php include 'includes/footer.php'; ?>