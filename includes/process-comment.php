<?php

/**
 * Endpoint AJAX pour traiter l'ajout de commentaire
 * POST /includes/process-comment.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/articles-db.php';

$response = [
    'success' => false,
    'message' => 'Erreur inconnue'
];

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Méthode non autorisée';
    echo json_encode($response);
    exit;
}

try {
    // Récupérer et valider les paramètres
    $articleId = isset($_POST['article_id']) ? (int)$_POST['article_id'] : null;
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation
    if (!$articleId) {
        $response['message'] = 'Article ID invalide';
        echo json_encode($response);
        exit;
    }

    if (empty($nom) || strlen($nom) < 2) {
        $response['message'] = 'Le nom doit contenir au moins 2 caractères';
        echo json_encode($response);
        exit;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Veuillez entrer une adresse email valide';
        echo json_encode($response);
        exit;
    }

    if (empty($message) || strlen($message) < 10) {
        $response['message'] = 'Le commentaire doit contenir au moins 10 caractères';
        echo json_encode($response);
        exit;
    }

    // Vérifier que l'article existe
    $pdo = getDBConnection();
    if ($pdo) {
        $sql = "SELECT article_id FROM articles WHERE article_id = :id AND status = 'published'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        if (!$stmt->fetch()) {
            $response['message'] = 'Article introuvable';
            http_response_code(404);
            echo json_encode($response);
            exit;
        }
    }

    // Ajouter le commentaire
    if (addComment($articleId, $nom, $email, $message)) {
        $response['success'] = true;
        $response['message'] = 'Merci pour votre commentaire ! Il sera publié après modération.';
        http_response_code(200);
    } else {
        $response['message'] = 'Erreur lors de l\'ajout du commentaire';
        http_response_code(500);
    }
} catch (Exception $e) {
    error_log('Erreur process-comment.php: ' . $e->getMessage());
    $response['message'] = 'Erreur serveur';
    http_response_code(500);
}

echo json_encode($response);
exit;
