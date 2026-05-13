<?php
require_once 'includes/articles-db.php';
$articles = getArticles();

$featuredArticle = $articles[0] ?? null;
?>
<?php include 'includes/header.php'; ?>

<section class="news-section py-5" style="padding-top: 6rem;">
    <div class="container">
        <div class="news-header text-center mb-5">
            <span class="transversal-tag">Blog & Actualités</span>
            <h1 class="news-title">Nos insights pour accélérer votre transformation</h1>
            <p class="news-subtitle">Cas clients, tendances sectorielles, retours d'expérience et conseils actionnables.</p>
        </div>

        <?php if ($featuredArticle): ?>
            <div class="news-feature mb-5">
                <div class="news-feature-image">
                    <img src="<?php echo (strpos($featuredArticle['image'], 'http') === 0) ? htmlspecialchars($featuredArticle['image']) : BASE_URL . htmlspecialchars($featuredArticle['image']); ?>" alt="<?php echo htmlspecialchars($featuredArticle['title']); ?>">
                    <span class="news-tag"><?php echo htmlspecialchars($featuredArticle['category']); ?></span>
                </div>
                <div class="news-feature-content">
                    <div class="news-feature-meta">
                        <span><?php echo htmlspecialchars($featuredArticle['date']); ?></span>
                        <span><?php echo htmlspecialchars($featuredArticle['read_time']); ?></span>
                    </div>
                    <h3 class="news-feature-title"><?php echo htmlspecialchars($featuredArticle['title']); ?></h3>
                    <p class="news-feature-excerpt"><?php echo htmlspecialchars($featuredArticle['excerpt']); ?></p>
                    <div class="news-feature-actions">
                        <a href="<?php echo BASE_URL; ?>article-detail.php?slug=<?php echo urlencode($featuredArticle['slug']); ?>" class="btn-news-primary">Lire l'article</a>
                        <button class="btn-news-icon" aria-label="Enregistrer l'article">
                            <i class="bi bi-bookmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="news-filters mb-4">
            <button class="news-filter-btn active">Tous</button>
            <button class="news-filter-btn">Événementiel</button>
            <button class="news-filter-btn">Immobilier</button>
            <button class="news-filter-btn">Innovation</button>
            <button class="news-filter-btn">Marketing</button>
            <button class="news-filter-btn">Success Story</button>
            <button class="news-filter-btn">Analytics</button>
        </div>

        <div class="news-grid">
            <?php foreach ($articles as $article): ?>
                <article class="news-card">
                    <div class="news-card-image">
                        <img src="<?php echo (strpos($article['image'], 'http') === 0) ? htmlspecialchars($article['image']) : BASE_URL . htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <span class="news-tag"><?php echo htmlspecialchars($article['category']); ?></span>
                    </div>
                    <div class="news-card-body">
                        <div class="news-card-meta">
                            <span><?php echo htmlspecialchars($article['date']); ?></span>
                            <span><?php echo htmlspecialchars($article['read_time']); ?></span>
                        </div>
                        <h4 class="news-card-title"><?php echo htmlspecialchars($article['title']); ?></h4>
                        <p class="news-card-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                        <a href="<?php echo BASE_URL; ?>article-detail.php?slug=<?php echo urlencode($article['slug']); ?>" class="news-card-link">Lire plus</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="news-footer">
            <p class="text-muted mb-0">De nouveaux articles sont publiés chaque mois par nos équipes produits, marketing et data.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>