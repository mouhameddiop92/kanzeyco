<?php

/**
 * Endpoint AJAX pour actions sur les contacts (admin)
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/contacts-handler.php';

header('Content-Type: application/json');

// S'assurer que l'administrateur est connecté
requireLogin();
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'mark_read':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = markContactRead($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'mark_unread':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = markContactUnread($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'archive':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = archiveContact($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = deleteContact($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'get':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $contact = getContactById($id);
            if (!$contact) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Message introuvable']);
            } else {
                echo json_encode(['success' => true, 'data' => $contact]);
            }
            break;

        case 'reply':
            $id = intval($_POST['id'] ?? 0);
            $subject = trim($_POST['subject'] ?? 'Réponse de KANZEYCO');
            $message = trim($_POST['message'] ?? '');
            if ($id <= 0 || empty($message)) throw new Exception('Données manquantes');

            $contact = getContactById($id);
            if (!$contact) throw new Exception('Contact introuvable');

            $to = $contact['email'];
            if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) throw new Exception('Email destinataire invalide');

            $from = $_SESSION['admin_email'] ?? ('no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $headers = "From: " . $from . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $body = "<p>Bonjour " . htmlspecialchars($contact['nom']) . ",</p>";
            $body .= "<div>" . nl2br(htmlspecialchars($message)) . "</div>";
            $body .= "<p>Cordialement,<br/>Equipe KANZEYCO</p>";

            $sent = @mail($to, $subject, $body, $headers);

            if ($sent) {
                markContactReplied($id);
                echo json_encode(['success' => true, 'message' => 'Réponse envoyée']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Impossible d\'envoyer l\'email']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
            break;
    }
} catch (Exception $e) {
    error_log('contacts-action error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
