<?php
require_once 'includes/config.php';
requireAdmin();

$pageTitle = "Gestion des utilisateurs";
require_once 'includes/admin-header.php';
require_once '../includes/database.php';
require_once 'includes/user-handler.php';

$pdo = getDBConnection();
$users = [];
$stats = getUsersStats();

if ($pdo) {
    $qry = $pdo->query("SELECT user_id, username, email, role, status, last_login, date_created FROM users ORDER BY date_created DESC");
    $users = $qry->fetchAll() ?? [];
}
?>

<div class="page-header">
    <h1 class="page-title">Gestion des utilisateurs</h1>
    <div class="page-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-2"></i>Ajouter un utilisateur
        </button>
    </div>
</div>

<!-- Statistiques utilisateurs -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo $stats['total']; ?></h3>
                <p class="stat-label">Total utilisateurs</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo $stats['active']; ?></h3>
                <p class="stat-label">Utilisateurs actifs</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-warning">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo $stats['inactive']; ?></h3>
                <p class="stat-label">Utilisateurs inactifs</p>
            </div>
        </div>
    </div>
</div>

<!-- Liste des utilisateurs -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Liste des utilisateurs</h5>
        <div class="card-search">
            <input type="text" class="form-control" id="searchUsers" placeholder="Rechercher un utilisateur...">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="usersTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr data-user-id="<?php echo $user['user_id']; ?>">
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'editor' ? 'warning' : 'info'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" title="Modifier" onclick="editUser(<?php echo $user['user_id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon" title="Réinitialiser mot de passe" onclick="resetPassword(<?php echo $user['user_id']; ?>)">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button class="btn-icon btn-icon-danger" title="Supprimer" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal d'ajout d'utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group mb-3">
                        <label for="userName" class="form-label">Nom d'utilisateur *</label>
                        <input type="text" class="form-control" id="userName" name="username" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="userEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="userPassword" class="form-label">Mot de passe *</label>
                        <input type="password" class="form-control" id="userPassword" name="password" required>
                        <small class="form-text text-muted">Minimum 6 caractères</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="userRole" class="form-label">Rôle *</label>
                        <select class="form-select" id="userRole" name="role" required>
                            <option value="">Sélectionner...</option>
                            <option value="admin">Administrateur</option>
                            <option value="author">Auteur</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="userStatus" class="form-label">Statut</label>
                        <select class="form-select" id="userStatus" name="status">
                            <option value="active" selected>Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'édition d'utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier un utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="user_id">

                    <div class="form-group mb-3">
                        <label for="editUserName" class="form-label">Nom d'utilisateur *</label>
                        <input type="text" class="form-control" id="editUserName" name="username" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="editUserEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="editUserRole" class="form-label">Rôle *</label>
                        <select class="form-select" id="editUserRole" name="role" required>
                            <option value="">Sélectionner...</option>
                            <option value="admin">Administrateur</option>
                            <option value="author">Auteur</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="editUserStatus" class="form-label">Statut</label>
                        <select class="form-select" id="editUserStatus" name="status">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="updateUser()">Mettre à jour</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de réinitialisation de mot de passe -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Réinitialiser le mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="resetPasswordForm">
                    <input type="hidden" id="resetUserId" name="user_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Entrez un nouveau mot de passe pour cet utilisateur.
                    </div>

                    <div class="form-group mb-3">
                        <label for="newPassword" class="form-label">Nouveau mot de passe *</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        <small class="form-text text-muted">Minimum 6 caractères</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="confirmPassword" class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="confirmResetPassword()">Réinitialiser</button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = 'includes/user-action.php';

        // Afficher une notification toast
        function showNotification(message, type = 'success') {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const toast = document.createElement('div');
            toast.className = `alert ${alertClass} position-fixed top-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.innerHTML = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Recherche dans le tableau
        document.getElementById('searchUsers')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Créer un utilisateur
        function saveUser() {
            const form = document.getElementById('addUserForm');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);

            fetch(`${API_URL}?action=create`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                        modal.hide();
                        form.reset();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Une erreur est survenue', 'danger');
                });
        }

        // Charger les données d'un utilisateur pour édition
        function editUser(userId) {
            fetch(`${API_URL}?action=getUser&user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const user = data.data;
                        document.getElementById('editUserId').value = user.user_id;
                        document.getElementById('editUserName').value = user.username;
                        document.getElementById('editUserEmail').value = user.email;
                        document.getElementById('editUserRole').value = user.role;
                        document.getElementById('editUserStatus').value = user.status;

                        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                        modal.show();
                    } else {
                        showNotification('Impossible de charger les données', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Une erreur est survenue', 'danger');
                });
        }

        // Mettre à jour un utilisateur
        function updateUser() {
            const form = document.getElementById('editUserForm');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);

            fetch(`${API_URL}?action=update`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                        modal.hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Une erreur est survenue', 'danger');
                });
        }

        // Supprimer un utilisateur
        function deleteUser(userId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                return;
            }

            const formData = new FormData();
            formData.append('user_id', userId);

            fetch(`${API_URL}?action=delete`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Une erreur est survenue', 'danger');
                });
        }

        // Ouvrir le modal de réinitialisation de mot de passe
        function resetPassword(userId) {
            document.getElementById('resetUserId').value = userId;
            document.getElementById('resetPasswordForm').reset();
            const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
        }

        // Confirmer la réinitialisation de mot de passe
        function confirmResetPassword() {
            const form = document.getElementById('resetPasswordForm');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);

            fetch(`${API_URL}?action=resetPassword`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                        modal.hide();
                        form.reset();
                    } else {
                        showNotification(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Une erreur est survenue', 'danger');
                });
        }
    </script>

    <?php require_once 'includes/admin-footer.php'; ?>