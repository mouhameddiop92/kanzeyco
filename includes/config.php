<?php
/**
 * Configuration globale de l'application
 */

if (!defined('BASE_URL')) {
    // Détection du protocole
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    
    // Détection de l'hôte
    $host = $_SERVER['HTTP_HOST'];
    
    // Détection du dossier racine du projet
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = str_replace('\\', '/', dirname($scriptName));
    
    // On remonte d'un niveau si le script est dans un sous-dossier connu
    $parts = explode('/', trim($scriptDir, '/'));
    $lastPart = end($parts);
    
    if ($lastPart === 'includes' || $lastPart === 'admin' || $lastPart === 'assets') {
        array_pop($parts);
    }
    
    $projectPath = !empty($parts) ? '/' . implode('/', $parts) : '';
    $projectPath = rtrim($projectPath, '/') . '/';
    
    // Définition de la constante BASE_URL
    define('BASE_URL', $protocol . '://' . $host . $projectPath);
}
