<?php

/**
 * Traitement du formulaire newsletter
 * Sauvegarde dans la table `newsletter` et vérification des doublons
 */

require_once __DIR__ . '/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer l'email
$email = trim($_POST['cta_newsletter_email'] ?? '');

// Validation basique
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Impossible de se connecter à la base de données');
    }

    // Vérifier les doublons (email actif ou non)
    $checkSql = "SELECT newsletter_id, status FROM newsletter WHERE email = ? LIMIT 1";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$email]);
    $existing = $checkStmt->fetch();

    if ($existing) {
        // Si déjà actif
        if (isset($existing['status']) && $existing['status'] === 'active') {
            echo json_encode(['success' => true, 'message' => 'Vous êtes déjà abonné à la newsletter']);
            exit;
        }

        // Si présent mais désabonné, on réactive
        $updateSql = "UPDATE newsletter SET status = 'active', date_unsubscribed = NULL, date_subscribed = NOW() WHERE newsletter_id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$existing['newsletter_id']]);

        echo json_encode(['success' => true, 'message' => 'Votre abonnement a été réactivé']);
        exit;
    }

    // Insérer le nouvel abonné
    $insertSql = "INSERT INTO newsletter (email, status, date_subscribed) VALUES (?, 'active', NOW())";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([$email]);

    // Optionnel : envoyer un email de bienvenue (non-critique)
    // $to = $email; $subject = 'Bienvenue'; mail(...);

    echo json_encode(['success' => true, 'message' => 'Merci ! Vous êtes bien abonné à la newsletter.']);
    exit;
} catch (PDOException $e) {
    error_log('Erreur BDD newsletter: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur, veuillez réessayer']);
    exit;
} catch (Exception $e) {
    error_log('Erreur traitement newsletter: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur, veuillez réessayer']);
    exit;
}
