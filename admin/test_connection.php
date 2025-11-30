<?php
/**
 * Script simple pour tester la connexion admin
 * Accédez à: http://localhost/KANZEYCO/admin/test_connection.php
 */

// Ne pas démarrer de session ici
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';

$username = 'admin';
$password = 'admin123';

echo "<h2>Test de connexion admin</h2>";
echo "<pre>";

echo "Username: $username\n";
echo "Password: $password\n\n";

// Test 1: Connexion à la base de données
echo "=== Test 1: Connexion BDD ===\n";
$pdo = getDBConnection();
if ($pdo) {
    echo "✓ Connexion OK\n\n";
} else {
    echo "✗ Erreur de connexion\n";
    exit;
}

// Test 2: Récupérer l'utilisateur
echo "=== Test 2: Récupération utilisateur ===\n";
try {
    $sql = "SELECT user_id, username, email, password, role, status FROM users WHERE username = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✓ Utilisateur trouvé:\n";
        echo "  - ID: " . $user['user_id'] . "\n";
        echo "  - Username: " . $user['username'] . "\n";
        echo "  - Email: " . $user['email'] . "\n";
        echo "  - Role: " . $user['role'] . "\n";
        echo "  - Status: " . $user['status'] . "\n";
        echo "  - Hash: " . substr($user['password'], 0, 30) . "...\n\n";
        
        // Test 3: Vérifier le mot de passe
        echo "=== Test 3: Vérification mot de passe ===\n";
        $isValid = password_verify($password, $user['password']);
        echo ($isValid ? "✓" : "✗") . " Mot de passe " . ($isValid ? "VALIDE" : "INVALIDE") . "\n\n";
        
        if ($isValid) {
            // Test 4: Appeler adminLogin()
            echo "=== Test 4: Fonction adminLogin() ===\n";
            $_SESSION = []; // Réinitialiser la session
            
            $result = adminLogin($username, $password);
            echo ($result ? "✓" : "✗") . " adminLogin() retourne: " . ($result ? "TRUE" : "FALSE") . "\n";
            
            if ($result) {
                echo "\n✓ CONNEXION RÉUSSIE !\n";
                echo "Session créée:\n";
                echo "  - admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? "Oui" : "Non") . "\n";
                echo "  - admin_username: " . ($_SESSION['admin_username'] ?? 'Non défini') . "\n";
                echo "\n<a href='login.php'>Essayez de vous connecter maintenant</a>\n";
            } else {
                echo "\n✗ ÉCHEC DE LA CONNEXION\n";
                echo "Session:\n";
                echo "  - admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? "Oui" : "Non") . "\n";
            }
        }
    } else {
        echo "✗ Utilisateur non trouvé\n";
    }
} catch (PDOException $e) {
    echo "✗ Erreur SQL: " . $e->getMessage() . "\n";
}

echo "</pre>";

