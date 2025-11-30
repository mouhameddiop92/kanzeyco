<?php

/**
 * Endpoint AJAX pour CRUD réalisations (admin)
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/realisations-handler.php';

header('Content-Type: application/json');
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$action = $_POST['action'] ?? '';

// dossier d'upload (relatif à la racine du projet)
$uploadDir = __DIR__ . '/../../uploads/realisations';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

try {
    switch ($action) {
        case 'get':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $r = getRealisationById($id);
            if (!$r) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Réalisaton introuvable']);
            } else echo json_encode(['success' => true, 'data' => $r]);
            break;

        case 'create':
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            if ($title === '') throw new Exception('Titre requis');

            $imagePath = null;
            if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $name = 'real_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . '/' . $name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $imagePath = 'uploads/realisations/' . $name;
                }
            }

            $ok = createRealisation($title, $description, $content, $imagePath, $status);
            echo json_encode(['success' => $ok]);
            break;

        case 'update':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $status = trim($_POST['status'] ?? 'draft');
            if ($title === '') throw new Exception('Titre requis');

            $imagePath = null;
            if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $name = 'real_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . '/' . $name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $imagePath = 'uploads/realisations/' . $name;
                }
            }

            $ok = updateRealisation($id, $title, $description, $content, $imagePath, $status);
            echo json_encode(['success' => $ok]);
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Identifiant invalide');
            $ok = deleteRealisation($id);
            echo json_encode(['success' => $ok]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
            break;
    }
} catch (Exception $e) {
    error_log('realisations-action error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
