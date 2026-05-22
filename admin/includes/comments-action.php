<?php

/**
 * Endpoint AJAX pour actions sur les commentaires (admin)
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/comments-handler.php';

header('Content-Type: application/json');
requireLogin();
if (!isAdmin() && !isAuthor()) {
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
        case 'get':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $c = getCommentById($id);
            if (!$c) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Commentaire introuvable']);
            } else echo json_encode(['success' => true, 'data' => $c]);
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            if (isAuthor()) {
                require_once __DIR__ . '/comments-handler.php';
                $comment = getCommentById($id);
                if (!$comment || ($comment['article_author'] ?? '') !== ($_SESSION['admin_username'] ?? '')) {
                    throw new Exception('Non autorisé');
                }
            }
            $ok = deleteComment($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'approve':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            if (isAuthor()) {
                require_once __DIR__ . '/comments-handler.php';
                $comment = getCommentById($id);
                if (!$comment || ($comment['article_author'] ?? '') !== ($_SESSION['admin_username'] ?? '')) {
                    throw new Exception('Non autorisé');
                }
            }
            $ok = approveComment($id);
            echo json_encode(['success' => $ok]);
            break;

        case 'unapprove':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            if (isAuthor()) {
                require_once __DIR__ . '/comments-handler.php';
                $comment = getCommentById($id);
                if (!$comment || ($comment['article_author'] ?? '') !== ($_SESSION['admin_username'] ?? '')) {
                    throw new Exception('Non autorisé');
                }
            }
            $ok = unapproveComment($id);
            echo json_encode(['success' => $ok]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
            break;
    }
} catch (Exception $e) {
    error_log('comments-action error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
