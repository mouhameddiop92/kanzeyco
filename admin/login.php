<?php
require_once 'includes/config.php';

// Si déjà connecté, rediriger vers le dashboard
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (adminLogin($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Nom d\'utilisateur ou mot de passe incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - KANZEYCO</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="../assets/images/Logo Kanzey Co.png" alt="KANZEYCO Logo" class="logo-img">
                </div>
                <h2 class="login-title">Espace Administrateur</h2>
                <p class="login-subtitle">Connectez-vous pour gérer le site</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="login-form">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>Nom d'utilisateur
                    </label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Mot de passe
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login-submit w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php" class="back-to-site">
                    <i class="fas fa-arrow-left me-2"></i>Retour au site
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

