<?php

/**
 * Endpoint AJAX pour gérer les paramètres
 * POST /admin/includes/settings-action.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/settings-handler.php';
require_once __DIR__ . '/config.php';

// Vérifier l'authentification
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Non autorisé'
    ]);
    exit;
}

$response = [
    'success' => false,
    'message' => 'Erreur inconnue'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Méthode non autorisée';
    echo json_encode($response);
    exit;
}

$action = isset($_POST['action']) ? trim($_POST['action']) : '';

try {
    switch ($action) {
        case 'save':
            // Initialiser la table si nécessaire
            initSettingsTable();

            // Récupérer les paramètres POST (tous les champs sauf 'action')
            $settings = [];
            foreach ($_POST as $key => $value) {
                if ($key !== 'action') {
                    $settings[$key] = trim($value);
                }
            }

            if (saveSettings($settings)) {
                $response['success'] = true;
                $response['message'] = 'Paramètres enregistrés avec succès';
            } else {
                $response['message'] = 'Erreur lors de la sauvegarde';
            }
            break;

        case 'get':
            $key = isset($_POST['key']) ? trim($_POST['key']) : '';
            if (empty($key)) {
                $response['message'] = 'Clé invalide';
                break;
            }

            $value = getSetting($key);
            $response['success'] = true;
            $response['data'] = $value;
            break;

        case 'get_all':
            $settings = getAllSettings();
            $response['success'] = true;
            $response['data'] = $settings;
            break;

        default:
            $response['message'] = 'Action inconnue';
    }
} catch (Exception $e) {
    error_log('Erreur settings-action.php: ' . $e->getMessage());
    $response['message'] = 'Erreur serveur: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
