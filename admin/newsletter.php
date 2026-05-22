<?php
require_once 'includes/config.php';
requireAdmin();

$pageTitle = 'Newsletter';
require_once 'includes/admin-header.php';
require_once 'includes/newsletter-handler.php';

$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;

$result = getSubscribers($q, $status, $page, $perPage);
$subs = $result['data'];
$total = $result['total'];
$totalPages = ($perPage > 0) ? ceil($total / $perPage) : 1;
$stats = getNewsletterStats();
?>

<div class="page-header">
    <h1 class="page-title">Newsletter</h1>
    <p class="page-subtitle">Gestion des abonnés et export</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div class="me-4">
                <strong>Total :</strong> <?php echo $stats['total']; ?>
            </div>
            <div class="me-4">
                <strong>Actifs :</strong> <?php echo $stats['active']; ?>
            </div>
            <div class="me-4">
                <strong>Inactifs :</strong> <?php echo $stats['inactive']; ?>
            </div>
            <div class="ms-auto">
                <form method="get" action="includes/newsletter-action.php" target="_blank" style="display:inline">
                    <input type="hidden" name="export" value="1">
                    <select name="status" class="form-select d-inline-block" style="width:auto">
                        <option value="">Tous</option>
                        <option value="active">Actifs</option>
                        <option value="inactive">Inactifs</option>
                    </select>
                    <button class="btn btn-outline-primary">Exporter CSV</button>
                </form>
            </div>
        </div>

        <form method="get" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Rechercher email">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Actifs</option>
                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactifs</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" type="submit">Filtrer</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Inscrit le</th>
                        <th>Désinscrit le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subs)): ?>
                        <tr>
                            <td colspan="5">Aucun abonné trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subs as $s): ?>
                            <tr id="sub-row-<?php echo $s['newsletter_id']; ?>">
                                <td><?php echo htmlspecialchars($s['email']); ?></td>
                                <td><?php echo htmlspecialchars($s['status']); ?></td>
                                <td><?php echo htmlspecialchars($s['date_subscribed'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($s['date_unsubscribed'] ?? ''); ?></td>
                                <td>
                                    <?php if ($s['status'] === 'active'): ?>
                                        <button class="btn btn-sm btn-outline-secondary btn-unsubscribe" data-id="<?php echo $s['newsletter_id']; ?>">Désabonner</button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-success btn-reactivate" data-id="<?php echo $s['newsletter_id']; ?>">Réactiver</button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $s['newsletter_id']; ?>">Supprimer</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const api = 'includes/newsletter-action.php';

        function post(action, data) {
            const f = new FormData();
            f.append('action', action);
            for (const k in data) f.append(k, data[k]);
            return fetch(api, {
                method: 'POST',
                body: f
            }).then(r => r.json());
        }

        document.querySelectorAll('.btn-unsubscribe').forEach(b => b.addEventListener('click', function() {
            if (!confirm('Désabonner cet utilisateur ?')) return;
            const id = this.dataset.id;
            post('unsubscribe', {
                id
            }).then(r => {
                if (r.success) location.reload();
                else alert(r.message || 'Erreur');
            }).catch(() => alert('Erreur'));
        }));

        document.querySelectorAll('.btn-reactivate').forEach(b => b.addEventListener('click', function() {
            const id = this.dataset.id;
            post('reactivate', {
                id
            }).then(r => {
                if (r.success) location.reload();
                else alert(r.message || 'Erreur');
            }).catch(() => alert('Erreur'));
        }));

        document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', function() {
            if (!confirm('Supprimer cet abonné ?')) return;
            const id = this.dataset.id;
            post('delete', {
                id
            }).then(r => {
                if (r.success) location.reload();
                else alert(r.message || 'Erreur');
            }).catch(() => alert('Erreur'));
        }));
    });
</script>

<?php require_once 'includes/admin-footer.php'; ?>