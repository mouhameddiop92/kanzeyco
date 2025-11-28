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
$relatedArticles = array_filter(getArticles(), function($item) use ($article) {
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
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-100 h-100" style="object-fit: cover;">
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
                    <a href="index.php#contact" class="btn btn-primary">Discuter avec un expert</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h3 class="h5 mb-3">Articles liés</h3>
                    <?php foreach ($relatedArticles as $related): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small"><?php echo htmlspecialchars($related['category']); ?> · <?php echo htmlspecialchars($related['read_time']); ?></p>
                                <a href="article-detail.php?slug=<?php echo urlencode($related['slug']); ?>" class="text-decoration-none fw-semibold"><?php echo htmlspecialchars($related['title']); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="articles.php" class="btn btn-outline-primary w-100 mt-2">Retour au blog</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


