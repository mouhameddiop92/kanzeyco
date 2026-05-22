<?php

/**
 * Endpoint AJAX / export pour la newsletter (admin)
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/newsletter-handler.php';

// Auth
if (!isAdmin()) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Export via GET (simple link) ou POST actions
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['export'])) {
    $status = trim($_GET['status'] ?? '');
    $csv = exportSubscribersCSV($status);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter_export.csv"');
    echo $csv;
    exit;
}

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $s = getSubscriberById($id);
            if (!$s) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Abonné introuvable']);
            } else echo json_encode(['success' => true, 'data' => $s]);
            break;

        case 'unsubscribe':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = unsubscribeSubscriber($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'reactivate':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = reactivateSubscriber($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = deleteSubscriber($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'stats':
            $stats = getNewsletterStats();
            echo json_encode(['success' => true, 'data' => $stats]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
            break;
    }
} catch (Exception $e) {
    error_log('newsletter-action error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
