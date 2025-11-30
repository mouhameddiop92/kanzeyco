<?php
/**
 * Page d'accueil du dashboard admin
 * Redirige vers login.php ou dashboard.php selon l'état de connexion
 */
require_once 'includes/config.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>

