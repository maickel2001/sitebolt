<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $user = new User();
    $user->logout();
}

$_SESSION['flash_message'] = 'Vous avez été déconnecté avec succès';
$_SESSION['flash_type'] = 'success';

header('Location: ' . SITE_URL);
exit;
?>