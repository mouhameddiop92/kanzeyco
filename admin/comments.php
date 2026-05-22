<?php
require_once 'includes/config.php';
requireAdmin();

$pageTitle = 'Commentaires';
require_once 'includes/admin-header.php';
require_once 'includes/comments-handler.php';

$q = trim($_GET['q'] ?? '');
$articleId = trim($_GET['article_id'] ?? '');
$status = trim($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

$result = getComments($q, $articleId, $status, $page, $perPage);
$comments = $result['data'];
$total = $result['total'];
$totalPages = ($perPage > 0) ? ceil($total / $perPage) : 1;

// récupérer la liste des articles présents dans les commentaires pour le filtre
$pdo = getDBConnection();
$articlesForFilter = [];
if ($pdo) {
    try {
        if ($isAuthorUser) {
            $stmt = $pdo->prepare("SELECT DISTINCT a.article_id, a.title FROM articles a JOIN comments c ON a.article_id = c.article_id WHERE a.author = ? ORDER BY a.title ASC");
            $stmt->execute([$articleAuthor]);
        } else {
            $stmt = $pdo->query("SELECT DISTINCT a.article_id, a.title FROM articles a JOIN comments c ON a.article_id = c.article_id ORDER BY a.title ASC");
        }
        $articlesForFilter = $stmt->fetchAll();
    } catch (Exception $e) {
        // ignore
    }
}
?>

<div class="page-header">
    <h1 class="page-title">Commentaires</h1>
    <p class="page-subtitle">Modération des commentaires</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Rechercher par auteur, email ou contenu">
            </div>
            <div class="col-md-3">
                <select name="article_id" class="form-select">
                    <option value="">Tous les articles</option>
                    <?php foreach ($articlesForFilter as $a): ?>
                        <option value="<?php echo $a['article_id']; ?>" <?php echo $articleId == $a['article_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>En attente</option>
                    <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approuvé</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary" type="submit">Filtrer</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Auteur</th>
                        <th>Email</th>
                        <th>Article</th>
                        <th>Commentaire</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comments)): ?>
                        <tr>
                            <td colspan="7">Aucun commentaire trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <?php
                            // Normaliser les noms de colonnes
                            $authorName = $c['author_name'] ?? ($c['nom'] ?? '—');
                            $authorEmail = $c['author_email'] ?? ($c['email'] ?? '—');
                            $content = $c['content'] ?? ($c['message'] ?? '');
                            $createdAt = $c['created_at'] ?? ($c['date_created'] ?? '');
                            ?>
                            <tr id="comment-row-<?php echo $c['comment_id']; ?>">
                                <td><?php echo htmlspecialchars($authorName); ?></td>
                                <td><?php echo htmlspecialchars($authorEmail); ?></td>
                                <td><?php echo htmlspecialchars($c['article_title'] ?? '—'); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(substr($content, 0, 100))); ?><?php echo strlen($content) > 100 ? '…' : ''; ?></td>
                                <td><span class="badge bg-<?php echo $c['status'] === 'approved' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($c['status']); ?></span></td>
                                <td><?php echo htmlspecialchars($createdAt); ?></td>
                                <td>
                                    <?php if ($c['status'] !== 'approved'): ?>
                                        <button class="btn btn-sm btn-outline-success btn-approve" data-id="<?php echo $c['comment_id']; ?>">Approuver</button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-warning btn-unapprove" data-id="<?php echo $c['comment_id']; ?>">Repasser en attente</button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $c['comment_id']; ?>">Supprimer</button>
                                    <button class="btn btn-sm btn-outline-primary btn-view" data-id="<?php echo $c['comment_id']; ?>">Voir</button>
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
                        <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>"><a class="page-link" href="?<?php echo http_build_query(['q' => $q, 'article_id' => $articleId, 'status' => $status, 'page' => $p]); ?>"><?php echo $p; ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>
</div>

<!-- Modal lecture -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Commentaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="commentDetails"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const api = 'includes/comments-action.php';

        function post(action, data) {
            const f = new FormData();
            f.append('action', action);
            for (const k in data) f.append(k, data[k]);
            return fetch(api, {
                method: 'POST',
                body: f
            }).then(r => r.json());
        }

        document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', function() {
            if (!confirm('Supprimer ce commentaire ?')) return;
            const id = this.dataset.id;
            post('delete', {
                id
            }).then(r => {
                if (r.success) location.reload();
                else alert('Erreur');
            }).catch(() => alert('Erreur'));
        }));

        document.querySelectorAll('.btn-approve').forEach(b => b.addEventListener('click', function() {
            const id = this.dataset.id;
            post('approve', {
                id
            }).then(r => {
                if (r.success) location.reload();
                else alert('Erreur');
            }).catch(() => alert('Erreur'));
        }));

        document.querySelectorAll('.btn-unapprove').forEach(b => b.addEventListener('click', function() {
            const id = this.dataset.id;
            post('unapprove', {
                id
            }).then(r => {
                if (r.success) location.reload();
                else alert('Erreur');
            }).catch(() => alert('Erreur'));
        }));

        document.querySelectorAll('.btn-view').forEach(b => b.addEventListener('click', function() {
            const id = this.dataset.id;
            post('get', {
                id
            }).then(r => {
                if (r.success) {
                    const c = r.data;
                    document.getElementById('commentDetails').innerHTML = `<p><strong>Auteur:</strong> ${escapeHtml(c.author_name)} (${escapeHtml(c.author_email)})</p><p><strong>Article:</strong> ${escapeHtml(c.article_title||'—')}</p><hr><p>${nl2br(escapeHtml(c.content))}</p>`;
                    var m = new bootstrap.Modal(document.getElementById('commentModal'));
                    m.show();
                } else alert(r.message || 'Erreur');
            }).catch(() => alert('Erreur'));
        }));

        function escapeHtml(s) {
            return (s || '').replace(/[&<>"]+/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;'
                } [m];
            });
        }

        function nl2br(s) {
            return (s || '').replace(/\n/g, '<br>');
        }
    });
</script>

<?php require_once 'includes/admin-footer.php'; ?>