<?php
$pageTitle = 'Réalisations';
require_once 'includes/admin-header.php';
require_once 'includes/realisations-handler.php';

$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

$result = getRealisations($q, $status, $page, $perPage);
$items = $result['data'];
$total = $result['total'];
$totalPages = ($perPage > 0) ? ceil($total / $perPage) : 1;
?>

<div class="page-header">
    <h1 class="page-title">Réalisations (Cas clients)</h1>
    <p class="page-subtitle">Gestion des réalisations</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex mb-3">
            <form method="get" class="d-flex flex-grow-1">
                <input type="text" name="q" class="form-control me-2" placeholder="Recherche" value="<?php echo htmlspecialchars($q); ?>">
                <select name="status" class="form-select me-2" style="width:180px">
                    <option value="">Tous</option>
                    <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Publié</option>
                    <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Brouillon</option>
                </select>
                <button class="btn btn-secondary">Filtrer</button>
            </form>
            <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#createModal">Nouvelle réalisation</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6">Aucune réalisation trouvée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $it): ?>
                            <tr id="real-row-<?php echo $it['realisation_id']; ?>">
                                <td><?php echo htmlspecialchars($it['title']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(substr($it['description'], 0, 80))); ?><?php echo strlen($it['description']) > 80 ? '…' : ''; ?></td>
                                <td><?php if (!empty($it['image'])): ?><img src="<?php echo BASE_URL . htmlspecialchars($it['image']); ?>" alt="" style="height:48px"><?php endif; ?></td>
                                <td><?php echo htmlspecialchars($it['status']); ?></td>
                                <td><?php echo htmlspecialchars($it['date_created'] ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-edit" data-id="<?php echo $it['realisation_id']; ?>">Modifier</button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $it['realisation_id']; ?>">Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>"><a class="page-link" href="?<?php echo http_build_query(['q' => $q, 'status' => $status, 'page' => $p]); ?>"><?php echo $p; ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>
</div>

<!-- Modal create -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle réalisation</h5><button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createForm" enctype="multipart/form-data">
                    <div class="mb-2"><input name="title" class="form-control" placeholder="Titre" required></div>
                    <div class="mb-2"><input name="description" class="form-control" placeholder="Brève description"></div>
                    <div class="mb-2"><textarea name="content" class="form-control" rows="6" placeholder="Contenu détaillé"></textarea></div>
                    <div class="mb-2"><input type="file" name="image" class="form-control"></div>
                    <div class="mb-2">
                        <select name="status" class="form-select">
                            <option value="draft">Brouillon</option>
                            <option value="published">Publié</option>
                        </select>
                    </div>
                    <div class="text-end"><button class="btn btn-primary" type="submit">Créer</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit (reutilisable) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier réalisation</h5><button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-2"><input name="title" id="editTitle" class="form-control" placeholder="Titre" required></div>
                    <div class="mb-2"><input name="description" id="editDescription" class="form-control" placeholder="Brève description"></div>
                    <div class="mb-2"><textarea name="content" id="editContent" class="form-control" rows="6" placeholder="Contenu détaillé"></textarea></div>
                    <div class="mb-2">Image actuelle: <div id="currentImage"></div>
                    </div>
                    <div class="mb-2"><input type="file" name="image" class="form-control"></div>
                    <div class="mb-2">
                        <select name="status" id="editStatus" class="form-select">
                            <option value="draft">Brouillon</option>
                            <option value="published">Publié</option>
                        </select>
                    </div>
                    <div class="text-end"><button class="btn btn-primary" type="submit">Enregistrer</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const api = '<?php echo BASE_URL; ?>admin/includes/realisations-action.php';

        function postForm(form) {
            return fetch(api, {
                method: 'POST',
                body: new FormData(form)
            }).then(r => r.json());
        }

        document.getElementById('createForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const f = this;
            const fd = new FormData(f);
            fd.append('action', 'create');
            fetch(api, {
                method: 'POST',
                body: fd
            }).then(r => r.json()).then(j => {
                if (j.success) location.reload();
                else alert('Erreur');
            }).catch(() => alert('Erreur'));
        });

        document.querySelectorAll('.btn-edit').forEach(b => b.addEventListener('click', function() {
            const id = this.dataset.id;
            const fd = new FormData();
            fd.append('action', 'get');
            fd.append('id', id);
            fetch(api, {
                method: 'POST',
                body: fd
            }).then(r => r.json()).then(j => {
                if (j.success) {
                    const d = j.data;
                    document.getElementById('editId').value = d.realisation_id;
                    document.getElementById('editTitle').value = d.title;
                    document.getElementById('editDescription').value = d.description;
                    document.getElementById('editContent').value = d.content;
                    document.getElementById('editStatus').value = d.status;
                    document.getElementById('currentImage').innerHTML = d.image ? `<img src="<?php echo BASE_URL; ?>${d.image}" style="height:80px">` : '—';
                    var m = new bootstrap.Modal(document.getElementById('editModal'));
                    m.show();
                } else alert(j.message || 'Erreur');
            }).catch(() => alert('Erreur'));
        }));

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'update');
            fetch(api, {
                method: 'POST',
                body: fd
            }).then(r => r.json()).then(j => {
                if (j.success) location.reload();
                else alert('Erreur');
            }).catch(() => alert('Erreur'));
        });

        document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', function() {
            if (!confirm('Supprimer cette réalisation ?')) return;
            const id = this.dataset.id;
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch(api, {
                method: 'POST',
                body: fd
            }).then(r => r.json()).then(j => {
                if (j.success) location.reload();
                else alert('Erreur');
            }).catch(() => alert('Erreur'));
        }));
    });
</script>

<?php require_once 'includes/admin-footer.php'; ?>