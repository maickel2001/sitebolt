<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function register($data) {
        // Validation des données
        $errors = $this->validateRegistration($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Vérifier si l'email existe déjà
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['email' => 'Cet email est déjà utilisé']];
        }
        
        // Hasher le mot de passe
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Token de vérification email
        $verificationToken = bin2hex(random_bytes(32));
        
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO users (email, password, nom, prenom, telephone, adresse, token_verification) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $data['email'],
                $hashedPassword,
                $data['nom'],
                $data['prenom'],
                $data['telephone'] ?? null,
                $data['adresse'] ?? null,
                $verificationToken
            ]);
            
            $userId = $this->db->lastInsertId();
            
            $this->db->commit();
            
            // Envoyer l'email de vérification (si configuré)
            if (SMTP_HOST) {
                $this->sendVerificationEmail($data['email'], $verificationToken);
            }
            
            return [
                'success' => true, 
                'message' => 'Inscription réussie. Vérifiez votre email pour activer votre compte.',
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'errors' => ['general' => 'Erreur lors de l\'inscription']];
        }
    }
    
    public function login($email, $password, $rememberMe = false) {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ? AND statut = 'actif'", [$email]);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        }
        
        // Mettre à jour la dernière connexion
        $this->db->query("UPDATE users SET date_derniere_connexion = NOW() WHERE id = ?", [$user['id']]);
        
        // Démarrer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nom'] = $user['nom'];
        
        // Cookie "Se souvenir de moi"
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            // Vous pouvez stocker ce token en base pour plus de sécurité
        }
        
        // Synchroniser le panier de session avec le panier utilisateur
        $this->syncCart($user['id']);
        
        return ['success' => true, 'message' => 'Connexion réussie', 'user' => $user];
    }
    
    public function logout() {
        // Supprimer les cookies
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Détruire la session
        session_destroy();
        
        return ['success' => true, 'message' => 'Déconnexion réussie'];
    }
    
    public function resetPassword($email) {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ? AND statut = 'actif'", [$email]);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Aucun compte trouvé avec cet email'];
        }
        
        $resetToken = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Stocker le token de reset (vous devrez ajouter une table pour cela)
        $this->db->query(
            "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) 
             ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)",
            [$email, $resetToken, $expiry]
        );
        
        // Envoyer l'email de reset
        if (SMTP_HOST) {
            $this->sendPasswordResetEmail($email, $resetToken);
        }
        
        return ['success' => true, 'message' => 'Un lien de réinitialisation a été envoyé à votre email'];
    }
    
    public function updateProfile($userId, $data) {
        $errors = $this->validateProfileUpdate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $sql = "UPDATE users SET nom = ?, prenom = ?, telephone = ?, adresse = ? WHERE id = ?";
            $this->db->query($sql, [
                $data['nom'],
                $data['prenom'],
                $data['telephone'] ?? null,
                $data['adresse'] ?? null,
                $userId
            ]);
            
            return ['success' => true, 'message' => 'Profil mis à jour avec succès'];
            
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['general' => 'Erreur lors de la mise à jour']];
        }
    }
    
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);
        
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Mot de passe actuel incorrect'];
        }
        
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'Le nouveau mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        try {
            $this->db->query("UPDATE users SET password = ? WHERE id = ?", [$hashedPassword, $userId]);
            return ['success' => true, 'message' => 'Mot de passe modifié avec succès'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la modification du mot de passe'];
        }
    }
    
    public function getUserById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    public function getUserOrders($userId, $limit = 10, $offset = 0) {
        return $this->db->fetchAll(
            "SELECT o.*, COUNT(oi.id) as total_items 
             FROM orders o 
             LEFT JOIN order_items oi ON o.id = oi.order_id 
             WHERE o.user_id = ? 
             GROUP BY o.id 
             ORDER BY o.date_commande DESC 
             LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }
    
    private function validateRegistration($data) {
        $errors = [];
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }
        
        if (empty($data['password']) || strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères';
        }
        
        if (empty($data['password_confirm']) || $data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';
        }
        
        if (empty($data['nom']) || strlen($data['nom']) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
        }
        
        if (empty($data['prenom']) || strlen($data['prenom']) < 2) {
            $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères';
        }
        
        return $errors;
    }
    
    private function validateProfileUpdate($data) {
        $errors = [];
        
        if (empty($data['nom']) || strlen($data['nom']) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
        }
        
        if (empty($data['prenom']) || strlen($data['prenom']) < 2) {
            $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères';
        }
        
        if (!empty($data['telephone']) && !preg_match('/^[+]?[0-9\s\-\(\)]{8,}$/', $data['telephone'])) {
            $errors['telephone'] = 'Numéro de téléphone invalide';
        }
        
        return $errors;
    }
    
    private function emailExists($email) {
        $user = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        return $user !== false;
    }
    
    private function syncCart($userId) {
        // Récupérer le panier de session (localStorage côté client)
        // Cette fonction sera appelée via AJAX depuis le JavaScript
        // Pour l'instant, on peut la laisser vide ou implémenter une logique simple
    }
    
    private function sendVerificationEmail($email, $token) {
        // Implémenter l'envoi d'email de vérification
        // Utiliser PHPMailer ou la fonction mail() de PHP
        $verificationUrl = SITE_URL . "/verify-email?token=" . $token;
        
        $subject = "Vérification de votre compte " . SITE_NAME;
        $message = "
            <h2>Bienvenue sur " . SITE_NAME . " !</h2>
            <p>Cliquez sur le lien suivant pour vérifier votre compte :</p>
            <p><a href='{$verificationUrl}'>Vérifier mon compte</a></p>
            <p>Ce lien expire dans 24 heures.</p>
        ";
        
        // Envoyer l'email (implémentation dépend de votre configuration SMTP)
        return $this->sendEmail($email, $subject, $message);
    }
    
    private function sendPasswordResetEmail($email, $token) {
        $resetUrl = SITE_URL . "/reset-password?token=" . $token;
        
        $subject = "Réinitialisation de votre mot de passe - " . SITE_NAME;
        $message = "
            <h2>Réinitialisation de mot de passe</h2>
            <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p>
            <p><a href='{$resetUrl}'>Réinitialiser mon mot de passe</a></p>
            <p>Ce lien expire dans 1 heure.</p>
        ";
        
        return $this->sendEmail($email, $subject, $message);
    }
    
    private function sendEmail($to, $subject, $message) {
        // Implémentation basique avec mail() - à améliorer avec PHPMailer
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SITE_EMAIL,
            'Reply-To: ' . SITE_EMAIL,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    
    public function verifyEmail($token) {
        $user = $this->db->fetch("SELECT * FROM users WHERE token_verification = ?", [$token]);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Token de vérification invalide'];
        }
        
        try {
            $this->db->query(
                "UPDATE users SET verification_email = 1, token_verification = NULL WHERE id = ?",
                [$user['id']]
            );
            
            return ['success' => true, 'message' => 'Email vérifié avec succès'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la vérification'];
        }
    }
}
?>