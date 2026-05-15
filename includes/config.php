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
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = str_replace('\\', '/', dirname($scriptName));
    
    // Si on est dans un sous-dossier connu, on remonte vers la racine
    $projectPath = preg_replace('/\/(includes|admin|assets)$/', '', rtrim($scriptDir, '/'));
    
    // Assurer que le chemin commence et finit par un slash, ou est juste '/'
    $projectPath = rtrim($projectPath, '/') . '/';
    if ($projectPath === '//') $projectPath = '/';
    
    // Définition de la constante BASE_URL
    // VOUS POUVEZ FORCER L'URL ICI SI LA DÉTECTION AUTOMATIQUE ÉCHOUE
    // define('BASE_URL', 'https://votre-domaine.com/');
    define('BASE_URL', $protocol . '://' . $host . $projectPath);
}
