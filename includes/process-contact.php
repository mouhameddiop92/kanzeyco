<?php

/**
 * Traitement du formulaire de contact
 * Sauvegarde dans la base de données et envoie une notification
 */

require_once __DIR__ . '/database.php';

header('Content-Type: application/json');

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Récupérer et nettoyer les données
$nom = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$entreprise = trim($_POST['company'] ?? '');
$telephone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation des données
$errors = [];

if (empty($nom)) {
    $errors[] = 'Le nom est obligatoire';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email invalide';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Le message doit faire au moins 10 caractères';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Connexion à la base de données
    $pdo = getDBConnection();

    if (!$pdo) {
        throw new Exception('Impossible de se connecter à la base de données');
    }

    // Préparer et exécuter la requête d'insertion
    $sql = "INSERT INTO contacts (nom, email, telephone, entreprise, message, status, date_created) 
            VALUES (?, ?, ?, ?, ?, 'new', NOW())";

    $stmt = $pdo->prepare($sql);

    if (!$stmt) {
        throw new Exception('Erreur de préparation: ' . implode(', ', $pdo->errorInfo()));
    }

    $execute_result = $stmt->execute([$nom, $email, $telephone, $entreprise, $message]);

    if (!$execute_result) {
        throw new Exception('Erreur d\'exécution: ' . implode(', ', $stmt->errorInfo()));
    }

    $contact_id = $pdo->lastInsertId();

    // Envoyer un email de notification (si la fonction mail est disponible)
    envoyerNotificationContact($nom, $email, $message, $entreprise, $telephone);

    // Réponse succès
    echo json_encode([
        'success' => true,
        'message' => 'Merci pour votre message ! Nous vous contacterons très bientôt.',
        'contact_id' => $contact_id
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log("Erreur base de données contact: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement de votre message. Veuillez réessayer.'
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Erreur traitement contact: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Envoyer une notification par email
 */
function envoyerNotificationContact($nom, $email, $message, $entreprise = '', $telephone = '')
{
    // Email administrateur (destination)
    $email_admin = 'contact@kanzey.co';

    // En-têtes du mail
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: <" . $email . ">" . "\r\n";

    // Sujet
    $sujet = "Nouveau message de contact de " . htmlspecialchars($nom);

    // Corps du mail (HTML)
    $corps = "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #0066cc; color: white; padding: 20px; text-align: center; border-radius: 5px; }
            .content { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
            .field { margin: 15px 0; }
            .field-label { font-weight: bold; color: #0066cc; }
            .field-value { margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #0066cc; }
            .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>📧 Nouveau Message de Contact</h2>
            </div>
            
            <div class='content'>
                <div class='field'>
                    <div class='field-label'>Nom</div>
                    <div class='field-value'>" . htmlspecialchars($nom) . "</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>Email</div>
                    <div class='field-value'>" . htmlspecialchars($email) . "</div>
                </div>";

    // Ajouter l'entreprise si fournie
    if (!empty($entreprise)) {
        $corps .= "
                <div class='field'>
                    <div class='field-label'>Entreprise</div>
                    <div class='field-value'>" . htmlspecialchars($entreprise) . "</div>
                </div>";
    }

    // Ajouter le téléphone si fourni
    if (!empty($telephone)) {
        $corps .= "
                <div class='field'>
                    <div class='field-label'>Téléphone</div>
                    <div class='field-value'>" . htmlspecialchars($telephone) . "</div>
                </div>";
    }

    $corps .= "
                <div class='field'>
                    <div class='field-label'>Message</div>
                    <div class='field-value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
            </div>
            
            <div class='footer'>
                <p>Message reçu le " . date('d/m/Y à H:i:s') . " via le formulaire de contact</p>
                <p>Veuillez répondre directement à : <strong>" . htmlspecialchars($email) . "</strong></p>
            </div>
        </div>
    </body>
    </html>";

    // Envoyer l'email
    $mail_sent = @mail($email_admin, $sujet, $corps, $headers);

    if ($mail_sent) {
        error_log("Email de notification envoyé pour le contact de " . $nom);
    } else {
        error_log("Erreur : Email de notification non envoyé pour " . $nom);
    }

    return $mail_sent;
}
