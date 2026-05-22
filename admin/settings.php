<?php
require_once 'includes/config.php';
requireAdmin();

$pageTitle = "Paramètres";
require_once 'includes/admin-header.php';
require_once 'includes/settings-handler.php';

// Initialiser la table settings
initSettingsTable();

// Récupérer les paramètres existants
$settings = getAllSettings();

// Valeurs par défaut
$defaults = [
    'site_name' => 'KANZEYCO',
    'site_tagline' => 'Transformons l\'Afrique par le digital',
    'site_email' => 'contact@kanzey.co',
    'site_phone' => '+221 XX XXX XX XX',
    'site_address' => 'Afrique de l\'Ouest',
    'site_description' => 'Des solutions digitales sectorielles pensées pour l\'Afrique de l\'Ouest.'
];

// Fusionner avec les valeurs sauvegardées
foreach ($defaults as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}

// Traitement du formulaire
$success = false;
$error = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim($_POST['action'] ?? '');

    if ($action === 'save_general') {
        // Sauvegarder les paramètres généraux
        $generalSettings = [
            'site_name' => trim($_POST['site_name'] ?? ''),
            'site_tagline' => trim($_POST['site_tagline'] ?? ''),
            'site_email' => trim($_POST['site_email'] ?? ''),
            'site_phone' => trim($_POST['site_phone'] ?? ''),
            'site_address' => trim($_POST['site_address'] ?? ''),
            'site_description' => trim($_POST['site_description'] ?? '')
        ];

        if (saveSettings($generalSettings)) {
            $success = true;
            // Mettre à jour l'array $settings
            $settings = array_merge($settings, $generalSettings);
        } else {
            $error = true;
        }
    } elseif ($action === 'change_password') {
        // Changer le mot de passe
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (empty($newPassword)) {
            $error = 'Le nouveau mot de passe ne peut pas être vide';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Les mots de passe ne correspondent pas';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères';
        } else {
            // Mettre à jour le mot de passe de l'utilisateur admin
            $pdo = getDBConnection();
            if ($pdo) {
                try {
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    $sql = "UPDATE users SET password = ? WHERE user_id = ?";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$hashedPassword, $_SESSION['admin_user_id']])) {
                        $success = true;
                        $successMsg = 'Mot de passe mis à jour avec succès';
                    } else {
                        $error = 'Erreur lors de la mise à jour du mot de passe';
                    }
                } catch (PDOException $e) {
                    error_log('Erreur changement password: ' . $e->getMessage());
                    $error = 'Erreur serveur';
                }
            } else {
                $error = 'BDD indisponible';
            }
        }
    }
}
?>

<div class="page-header">
    <h1 class="page-title">Paramètres du site</h1>
    <p class="page-subtitle">Gérez les configurations générales</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $successMsg ?? 'Paramètres enregistrés avec succès !'; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo is_string($error) ? $error : 'Erreur lors de l\'enregistrement'; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Paramètres généraux -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-cog me-2"></i>Paramètres généraux
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="settings.php">
                    <input type="hidden" name="action" value="save_general">

                    <div class="form-group mb-3">
                        <label for="siteName" class="form-label">Nom du site *</label>
                        <input type="text" class="form-control" id="siteName" name="site_name"
                            value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="siteTagline" class="form-label">Slogan du site</label>
                        <input type="text" class="form-control" id="siteTagline" name="site_tagline"
                            value="<?php echo htmlspecialchars($settings['site_tagline']); ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label for="siteEmail" class="form-label">Email de contact *</label>
                        <input type="email" class="form-control" id="siteEmail" name="site_email"
                            value="<?php echo htmlspecialchars($settings['site_email']); ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="sitePhone" class="form-label">Téléphone de contact</label>
                        <input type="tel" class="form-control" id="sitePhone" name="site_phone"
                            value="<?php echo htmlspecialchars($settings['site_phone']); ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label for="siteAddress" class="form-label">Adresse</label>
                        <textarea class="form-control" id="siteAddress" name="site_address" rows="2"><?php echo htmlspecialchars($settings['site_address']); ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="siteDescription" class="form-label">Description du site</label>
                        <textarea class="form-control" id="siteDescription" name="site_description" rows="4"><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Paramètres de sécurité -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-shield-alt me-2"></i>Sécurité
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="settings.php">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group mb-3">
                        <label for="adminUsername" class="form-label">Nom d'utilisateur admin</label>
                        <input type="text" class="form-control" id="adminUsername" name="admin_username"
                            value="<?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?>" readonly>
                        <small class="form-text text-muted">Le nom d'utilisateur ne peut pas être modifié depuis cette interface.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password"
                            placeholder="Laisser vide pour ne pas changer">
                    </div>

                    <div class="form-group mb-3">
                        <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                            placeholder="Confirmer le nouveau mot de passe">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Informations système -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-info-circle me-2"></i>Informations système
                </h5>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Version PHP</span>
                        <span class="info-value"><?php echo phpversion(); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Version du CMS</span>
                        <span class="info-value">1.0.0</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Serveur</span>
                        <span class="info-value"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dernière mise à jour</span>
                        <span class="info-value"><?php echo date('d/m/Y H:i'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aide et support -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-question-circle me-2"></i>Aide et support
                </h5>
            </div>
            <div class="card-body">
                <div class="support-links">
                    <a href="#" class="support-link">
                        <i class="fas fa-book me-2"></i>Documentation
                    </a>
                    <a href="#" class="support-link">
                        <i class="fas fa-envelope me-2"></i>Contacter le support
                    </a>
                    <a href="#" class="support-link">
                        <i class="fas fa-bug me-2"></i>Signaler un problème
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>