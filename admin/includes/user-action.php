<?php

/**
 * Gestionnaire AJAX pour les actions utilisateurs
 */

session_start();
require_once 'config.php';
require_once 'user-handler.php';
require_once '../../includes/database.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Action inconnue'];

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'author';
                $status = $_POST['status'] ?? 'active';

                // Validation
                if (empty($username) || empty($email) || empty($password)) {
                    $response = ['success' => false, 'message' => 'Tous les champs sont obligatoires'];
                } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $response = ['success' => false, 'message' => 'Email invalide'];
                } else if (strlen($password) < 6) {
                    $response = ['success' => false, 'message' => 'Le mot de passe doit faire au moins 6 caractères'];
                } else if (!in_array($role, ['admin', 'editor', 'author'])) {
                    $response = ['success' => false, 'message' => 'Rôle invalide'];
                } else {
                    $response = createUser($username, $email, $password, $role, $status);
                }
            }
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userId = (int)($_POST['user_id'] ?? 0);
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $role = $_POST['role'] ?? 'author';
                $status = $_POST['status'] ?? 'active';

                // Validation
                if ($userId <= 0 || empty($username) || empty($email)) {
                    $response = ['success' => false, 'message' => 'Données invalides'];
                } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $response = ['success' => false, 'message' => 'Email invalide'];
                } else if (!in_array($role, ['admin', 'editor', 'author'])) {
                    $response = ['success' => false, 'message' => 'Rôle invalide'];
                } else {
                    $response = updateUser($userId, $username, $email, $role, $status);
                }
            }
            break;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userId = (int)($_POST['user_id'] ?? 0);

                if ($userId <= 0) {
                    $response = ['success' => false, 'message' => 'ID utilisateur invalide'];
                } else {
                    $response = deleteUser($userId);
                }
            }
            break;

        case 'resetPassword':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userId = (int)($_POST['user_id'] ?? 0);
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if ($userId <= 0 || empty($newPassword)) {
                    $response = ['success' => false, 'message' => 'Données invalides'];
                } else if ($newPassword !== $confirmPassword) {
                    $response = ['success' => false, 'message' => 'Les mots de passe ne correspondent pas'];
                } else if (strlen($newPassword) < 6) {
                    $response = ['success' => false, 'message' => 'Le mot de passe doit faire au moins 6 caractères'];
                } else {
                    $response = resetUserPassword($userId, $newPassword);
                }
            }
            break;

        case 'getUser':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $userId = (int)($_GET['user_id'] ?? 0);

                if ($userId <= 0) {
                    $response = ['success' => false, 'message' => 'ID utilisateur invalide'];
                } else {
                    $user = getUserById($userId);
                    if ($user) {
                        $response = ['success' => true, 'data' => $user];
                    } else {
                        $response = ['success' => false, 'message' => 'Utilisateur non trouvé'];
                    }
                }
            }
            break;
    }
} catch (Exception $e) {
    error_log("Erreur action utilisateur: " . $e->getMessage());
    $response = ['success' => false, 'message' => 'Une erreur est survenue'];
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
