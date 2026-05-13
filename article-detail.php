<?php
require_once 'includes/articles-db.php';
$requestedSlug = $_GET['slug'] ?? null;
$article = null;
if ($requestedSlug) {
    $article = getArticleBySlug($requestedSlug);
}
if (!$article) {
    // Fallback au premier article existant, ou message d'erreur
    $articles = getArticles();
    $article = $articles[0] ?? null;
    $notFound = true;
} else {
    $notFound = false;
}

// Récupérer les commentaires approuvés
$comments = [];
if ($article && isset($article['article_id'])) {
    error_log("article-detail.php: article_id = " . $article['article_id']);
    $comments = getArticleComments($article['article_id'], 'all');
    error_log("article-detail.php: comments count = " . count($comments));
} else {
    error_log("article-detail.php: article not found or no article_id");
}

$relatedArticles = array_filter(getArticles(), function ($item) use ($article) {
    return $item['slug'] !== $article['slug'];
});
$relatedArticles = array_slice(array_values($relatedArticles), 0, 3);
?>
<?php include 'includes/header.php'; ?>

<section class="article-hero py-5" style="padding-top: 6rem;">
    <div class="container">
        <?php if ($notFound): ?>
            <div class="alert alert-warning mb-4" role="alert">
                L'article demandé est introuvable. Nous vous proposons notre dernière publication.
            </div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <span class="badge bg-primary-subtle text-primary mb-3"><?php echo htmlspecialchars($article['category']); ?></span>
                <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($article['title']); ?></h1>
                <p class="text-muted mb-4">
                    Publié le <?php echo htmlspecialchars($article['date']); ?> · <?php echo htmlspecialchars($article['read_time']); ?> · Par <?php echo htmlspecialchars($article['author']); ?>
                </p>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php foreach ($article['tags'] as $tag): ?>
                        <span class="badge rounded-pill bg-light text-dark"><?php echo htmlspecialchars($tag); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow">
                    <img src="<?php echo (strpos($article['image'], 'http') === 0) ? htmlspecialchars($article['image']) : BASE_URL . htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-100 h-100" style="object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="article-body pb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5 mb-4">
                    <?php foreach ($article['content'] as $block): ?>
                        <?php if ($block['type'] === 'paragraph'): ?>
                            <p class="lead" style="font-size:1.05rem;"><?php echo nl2br(htmlspecialchars($block['text'])); ?></p>
                        <?php elseif ($block['type'] === 'list'): ?>
                            <h3 class="h5 mt-4"><?php echo htmlspecialchars($block['title']); ?></h3>
                            <ul class="list-unstyled ps-3">
                                <?php foreach ($block['items'] as $item): ?>
                                    <li class="d-flex align-items-start mb-2">
                                        <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                        <span><?php echo htmlspecialchars($item); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="bg-light rounded-4 p-4">
                    <h3 class="h5 mb-3">Envie d'aller plus loin ?</h3>
                    <p class="mb-4">Nos experts peuvent vous aider à cadrer votre projet en deux semaines : audit express, plan d'action et feuille de route priorisée.</p>
                    <a href="<?php echo BASE_URL; ?>index.php#contact" class="btn btn-primary">Discuter avec un expert</a>
                </div>

                <!-- Section Commentaires -->
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5 mt-4">
                    <h3 class="h5 mb-4">Commentaires (<?php echo count($comments); ?>)</h3>

                    <!-- Formulaire de commentaire -->
                    <div class="mb-5">
                        <h4 class="h6 mb-3">Ajouter un commentaire</h4>
                        <form id="commentForm" class="needs-validation">
                            <input type="hidden" name="article_id" value="<?php echo $article['article_id'] ?? ''; ?>">

                            <div class="mb-3">
                                <label for="commentNom" class="form-label">Votre nom</label>
                                <input type="text" class="form-control" id="commentNom" name="nom" placeholder="Jean Dupont" required minlength="2">
                                <small class="form-text text-muted">Au minimum 2 caractères</small>
                            </div>

                            <div class="mb-3">
                                <label for="commentEmail" class="form-label">Votre email</label>
                                <input type="email" class="form-control" id="commentEmail" name="email" placeholder="jean@example.com" required>
                                <small class="form-text text-muted">Nous ne partagerons pas votre email</small>
                            </div>

                            <div class="mb-3">
                                <label for="commentMessage" class="form-label">Commentaire</label>
                                <textarea class="form-control" id="commentMessage" name="message" rows="4" placeholder="Partagez votre avis..." required minlength="10"></textarea>
                                <small class="form-text text-muted">Au minimum 10 caractères</small>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>Publier le commentaire
                            </button>
                            <div id="commentMessage" class="alert mt-2" style="display: none;"></div>
                        </form>
                    </div>

                    <hr>

                    <!-- Liste des commentaires approuvés -->
                    <div class="mt-5">
                        <h4 class="h6 mb-4">Commentaires approuvés</h4>
                        <?php if (empty($comments)): ?>
                            <p class="text-muted">Aucun commentaire approuvé pour cet article.</p>
                        <?php else: ?>
                            <div class="comments-list">
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment-item mb-4 pb-4 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <h5 class="mb-0"><?php echo htmlspecialchars($comment['nom']); ?></h5>
                                                    <?php if (isset($comment['status'])): ?>
                                                        <span class="badge bg-<?php echo $comment['status'] === 'approved' ? 'success' : ($comment['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                                            <?php
                                                            $statusLabel = [
                                                                'approved' => 'Approuvé',
                                                                'pending' => 'En attente',
                                                                'rejected' => 'Rejeté'
                                                            ];
                                                            echo $statusLabel[$comment['status']] ?? $comment['status'];
                                                            ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php
                                                    if (isset($comment['date_created'])) {
                                                        $date = new DateTime($comment['date_created']);
                                                        echo $date->format('d M Y à H:i');
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-secondary"><?php echo nl2br(htmlspecialchars($comment['message'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <div class="col-lg-4">
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h3 class="h5 mb-3">Articles liés</h3>
                    <?php foreach ($relatedArticles as $related): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small"><?php echo htmlspecialchars($related['category']); ?> · <?php echo htmlspecialchars($related['read_time']); ?></p>
                                <a href="<?php echo BASE_URL; ?>article-detail.php?slug=<?php echo urlencode($related['slug']); ?>" class="text-decoration-none fw-semibold"><?php echo htmlspecialchars($related['title']); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="<?php echo BASE_URL; ?>articles.php" class="btn btn-outline-primary w-100 mt-2">Retour au blog</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const commentForm = document.getElementById('commentForm');
        const commentMessageDiv = document.getElementById('commentMessage');

        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(commentForm);
                const submitBtn = commentForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Envoi...';

                fetch('<?php echo BASE_URL; ?>includes/process-comment.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        commentMessageDiv.style.display = 'block';

                        if (data.success) {
                            commentMessageDiv.className = 'alert alert-success mt-2';
                            commentMessageDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + data.message;

                            // Réinitialiser le formulaire
                            commentForm.reset();

                            // Recharger la page après 2 secondes pour afficher le nouvel commentaire
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            commentMessageDiv.className = 'alert alert-danger mt-2';
                            commentMessageDiv.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>' + data.message;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        commentMessageDiv.style.display = 'block';
                        commentMessageDiv.className = 'alert alert-danger mt-2';
                        commentMessageDiv.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>Erreur lors de l\'envoi du commentaire';
                    })
                    .finally(() => {
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });
        }
    });
</script>