<?php
require_once 'includes/config.php';

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/account');
    exit;
}

$pageTitle = 'Connexion';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Tous les champs sont requis';
    } else {
        $user = new User();
        $result = $user->login($email, $password, $rememberMe);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
            
            // Rediriger vers la page demandée ou l'accueil
            $redirect = $_GET['redirect'] ?? SITE_URL . '/account';
            header('Location: ' . $redirect);
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title text-center">Connexion</h2>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" id="remember_me" name="remember_me" 
                               <?php echo isset($_POST['remember_me']) ? 'checked' : ''; ?>>
                        <label for="remember_me" style="margin: 0;">Se souvenir de moi</label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
                    </div>
                </form>
                
                <div class="text-center" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <p><a href="<?php echo SITE_URL; ?>/forgot-password" style="color: var(--highlight-color);">Mot de passe oublié ?</a></p>
                    <p>Pas encore de compte ? <a href="<?php echo SITE_URL; ?>/register" style="color: var(--highlight-color);">S'inscrire</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -1rem;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0 1rem;
}

@media (max-width: 768px) {
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

.justify-center {
    justify-content: center;
}
</style>

<?php include 'includes/footer.php'; ?>