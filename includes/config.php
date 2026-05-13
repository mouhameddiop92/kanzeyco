<?php
/**
 * Configuration globale de l'application
 */

if (!defined('BASE_URL')) {
    // Détection du protocole (gestion des proxies HTTPS)
    $protocol = 'http';
    if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) {
        $protocol = 'https';
    }
    
    // Détection de l'hôte
    $host = $_SERVER['HTTP_HOST'];
    
    // Détection du chemin du projet
    // On utilise SCRIPT_NAME pour obtenir le chemin du script actuel
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = str_replace('\\', '/', dirname($scriptName));
    
    // Nettoyage du chemin pour obtenir la racine du projet
    // Si on est dans un sous-dossier connu (includes, admin, assets), on remonte
    $pathParts = explode('/', trim($scriptDir, '/'));
    $lastPart = end($pathParts);
    
    if (in_array($lastPart, ['includes', 'admin', 'assets'])) {
        array_pop($pathParts);
    }
    
    $projectPath = !empty($pathParts) ? '/' . implode('/', $pathParts) : '';
    $projectPath = rtrim($projectPath, '/') . '/';
    
    // Définition de la constante BASE_URL
    // VOUS POUVEZ FORCER L'URL ICI SI LA DÉTECTION AUTOMATIQUE ÉCHOUE
    // define('BASE_URL', 'https://votre-domaine.com/');
    define('BASE_URL', $protocol . '://' . $host . $projectPath);
}
