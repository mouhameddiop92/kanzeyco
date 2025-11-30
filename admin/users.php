<?php
$pageTitle = "Gestion des utilisateurs";
require_once 'includes/admin-header.php';
require_once '../includes/database.php';

$pdo = getDBConnection();
$users = [];

if ($pdo) {
    $qry = $pdo->query("SELECT user_id, username, email, role, status, date_created FROM users ORDER BY date_created DESC");
    $users = $qry->fetchAll();
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
                <h3 class="stat-value"><?php echo count($users); ?></h3>
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
                <h3 class="stat-value"><?php echo count(array_filter($users, fn($u) => $u['status'] === 'active')); ?></h3>
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
                <h3 class="stat-value"><?php echo count(array_filter($users, fn($u) => $u['status'] === 'inactive')); ?></h3>
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
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
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
                            <span class="badge bg-info"><?php echo htmlspecialchars($user['role']); ?></span>
                        </td>
                        <td>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($user['date_created'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon" title="Modifier" onclick="editUser(<?php echo $user['user_id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-icon-danger" title="Supprimer" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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
                        <label for="userName" class="form-label">Nom complet *</label>
                        <input type="text" class="form-control" id="userName" name="name" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="userEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="userRole" class="form-label">Rôle *</label>
                        <select class="form-select" id="userRole" name="role" required>
                            <option value="">Sélectionner...</option>
                            <option value="Admin">Administrateur</option>
                            <option value="Éditeur">Éditeur</option>
                            <option value="Auteur">Auteur</option>
                            <option value="Visiteur">Visiteur</option>
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

<script>
// Recherche dans le tableau
document.getElementById('searchUsers')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Fonctions de gestion
function editUser(id) {
    alert('Édition de l\'utilisateur #' + id + ' (fonctionnalité à implémenter)');
}

function deleteUser(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        alert('Utilisateur supprimé (simulation)');
    }
}

function saveUser() {
    const form = document.getElementById('addUserForm');
    if (form.checkValidity()) {
        alert('Utilisateur ajouté (simulation)');
        const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
        modal.hide();
        form.reset();
    } else {
        form.reportValidity();
    }
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>

