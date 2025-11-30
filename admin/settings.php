<?php
$pageTitle = "Paramètres";
require_once 'includes/admin-header.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ici, on pourrait sauvegarder les paramètres
    $success = true;
}
?>

<div class="page-header">
    <h1 class="page-title">Paramètres du site</h1>
    <p class="page-subtitle">Gérez les configurations générales</p>
</div>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>Paramètres enregistrés avec succès !
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
                    <div class="form-group mb-3">
                        <label for="siteName" class="form-label">Nom du site *</label>
                        <input type="text" class="form-control" id="siteName" name="site_name" 
                               value="KANZEYCO" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="siteTagline" class="form-label">Slogan du site</label>
                        <input type="text" class="form-control" id="siteTagline" name="site_tagline" 
                               value="Transformons l'Afrique par le digital">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="siteEmail" class="form-label">Email de contact *</label>
                        <input type="email" class="form-control" id="siteEmail" name="site_email" 
                               value="contact@kanzey.co" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="sitePhone" class="form-label">Téléphone de contact</label>
                        <input type="tel" class="form-control" id="sitePhone" name="site_phone" 
                               value="+221 XX XXX XX XX">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="siteAddress" class="form-label">Adresse</label>
                        <textarea class="form-control" id="siteAddress" name="site_address" rows="2">Afrique de l'Ouest</textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="siteDescription" class="form-label">Description du site</label>
                        <textarea class="form-control" id="siteDescription" name="site_description" rows="4">Des solutions digitales sectorielles pensées pour l'Afrique de l'Ouest. Nous accompagnons les entreprises dans leur transformation numérique avec des outils performants et accessibles.</textarea>
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

