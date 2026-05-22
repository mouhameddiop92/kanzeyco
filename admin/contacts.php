<?php
require_once 'includes/config.php';
requireAdmin();

$pageTitle = 'Contacts';
require_once 'includes/admin-header.php';
require_once 'includes/contacts-handler.php';

$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

$result = getContacts($q, $status, $page, $perPage);
$contacts = $result['data'];
$total = $result['total'];
$totalPages = ($perPage > 0) ? ceil($total / $perPage) : 1;
?>

<div class="page-header">
    <h1 class="page-title">Messages de contact</h1>
    <p class="page-subtitle">Liste des messages reçus via le formulaire de contact</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-5">
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Rechercher par nom, email, entreprise ou message">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="new" <?php echo $status === 'new' ? 'selected' : ''; ?>>Nouveaux</option>
                    <option value="read" <?php echo $status === 'read' ? 'selected' : ''; ?>>Lu</option>
                    <option value="replied" <?php echo $status === 'replied' ? 'selected' : ''; ?>>Répondu</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archivé</option>
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
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Entreprise</th>
                        <th>Message</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contacts)): ?>
                        <tr>
                            <td colspan="8">Aucun message trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contacts as $c): ?>
                            <tr id="contact-row-<?php echo $c['contact_id']; ?>">
                                <td><?php echo htmlspecialchars($c['nom']); ?></td>
                                <td><?php echo htmlspecialchars($c['email']); ?></td>
                                <td><?php echo htmlspecialchars($c['telephone']); ?></td>
                                <td><?php echo htmlspecialchars($c['entreprise']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(substr($c['message'], 0, 100))); ?><?php echo strlen($c['message']) > 100 ? '…' : ''; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $c['status'] === 'new' ? 'warning' : ($c['status'] === 'read' ? 'secondary' : ($c['status'] === 'replied' ? 'success' : 'dark')); ?>">
                                        <?php echo htmlspecialchars($c['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($c['date_created']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-view" data-id="<?php echo $c['contact_id']; ?>">Voir</button>
                                    <?php if ($c['status'] !== 'read'): ?>
                                        <button class="btn btn-sm btn-outline-success btn-mark-read" data-id="<?php echo $c['contact_id']; ?>">Marquer lu</button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-warning btn-mark-unread" data-id="<?php echo $c['contact_id']; ?>">Marquer non lu</button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-secondary btn-archive" data-id="<?php echo $c['contact_id']; ?>">Archiver</button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $c['contact_id']; ?>">Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination simple -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(['q' => $q, 'status' => $status, 'page' => $p]); ?>"><?php echo $p; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>
</div>

<!-- Modal lecture/réponse -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contactDetails"></div>

                <hr />
                <h6>Répondre</h6>
                <form id="replyForm">
                    <input type="hidden" name="id" id="replyContactId" />
                    <div class="mb-2">
                        <input class="form-control" id="replySubject" name="subject" placeholder="Sujet" value="Réponse de KANZEYCO">
                    </div>
                    <div class="mb-2">
                        <textarea class="form-control" id="replyMessage" name="message" rows="6" placeholder="Votre message..."></textarea>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary" id="sendReplyBtn">Envoyer la réponse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const apiUrl = 'includes/contacts-action.php';

        function postAction(action, data) {
            const form = new FormData();
            form.append('action', action);
            for (const k in data) form.append(k, data[k]);

            return fetch(apiUrl, {
                    method: 'POST',
                    body: form
                })
                .then(r => r.json());
        }

        // View
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                postAction('get', {
                    id
                }).then(resp => {
                    if (resp.success) {
                        const c = resp.data;
                        const details = `
                        <p><strong>Nom:</strong> ${escapeHtml(c.nom)}<br>
                        <strong>Email:</strong> ${escapeHtml(c.email)}<br>
                        <strong>Téléphone:</strong> ${escapeHtml(c.telephone)}<br>
                        <strong>Entreprise:</strong> ${escapeHtml(c.entreprise)}</p>
                        <hr>
                        <p>${nl2br(escapeHtml(c.message))}</p>
                    `;
                        document.getElementById('contactDetails').innerHTML = details;
                        document.getElementById('replyContactId').value = c.contact_id;
                        var modal = new bootstrap.Modal(document.getElementById('contactModal'));
                        modal.show();
                    } else {
                        alert(resp.message || 'Erreur');
                    }
                }).catch(e => alert('Erreur réseau'));
            });
        });

        // Mark read
        document.querySelectorAll('.btn-mark-read').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                postAction('mark_read', {
                    id
                }).then(resp => {
                    if (resp.success) location.reload();
                    else alert('Erreur');
                }).catch(() => alert('Erreur'));
            });
        });

        // Mark unread
        document.querySelectorAll('.btn-mark-unread').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                postAction('mark_unread', {
                    id
                }).then(resp => {
                    if (resp.success) location.reload();
                    else alert('Erreur');
                }).catch(() => alert('Erreur'));
            });
        });

        // Archive
        document.querySelectorAll('.btn-archive').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Archiver ce message ?')) return;
                const id = this.dataset.id;
                postAction('archive', {
                    id
                }).then(resp => {
                    if (resp.success) location.reload();
                    else alert('Erreur');
                }).catch(() => alert('Erreur'));
            });
        });

        // Delete
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Supprimer définitivement ce message ?')) return;
                const id = this.dataset.id;
                postAction('delete', {
                    id
                }).then(resp => {
                    if (resp.success) location.reload();
                    else alert('Erreur');
                }).catch(() => alert('Erreur'));
            });
        });

        // Reply form
        document.getElementById('replyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('replyContactId').value;
            const subject = document.getElementById('replySubject').value;
            const message = document.getElementById('replyMessage').value;
            if (!message.trim()) {
                alert('Le message est vide');
                return;
            }
            postAction('reply', {
                id,
                subject,
                message
            }).then(resp => {
                if (resp.success) {
                    alert('Réponse envoyée');
                    location.reload();
                } else {
                    alert(resp.message || 'Erreur envoi');
                }
            }).catch(() => alert('Erreur réseau'));
        });

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