<?php
$pageTitle = "Statistiques";
require_once 'includes/admin-header.php';

$stats = getDashboardStats();

// Période (7, 30, 90 jours)
$days = isset($_GET['days']) ? max(7, (int)$_GET['days']) : 30;

// Données par défaut (repli si la BDD ou les tables manquent)
$viewsLabels = [];
$viewsData = [];
$categoryLabels = [];
$categoryData = [];
$topArticles = [];

$pdo = getDBConnection();
if ($pdo) {
    try {
        // Vérifier si la table article_views existe
        $tableExists = (bool)$pdo->query("SHOW TABLES LIKE 'article_views'")->fetch();

        if ($tableExists) {
            // Déterminer le nom de la colonne date (création)
            $cols = $pdo->query("SHOW COLUMNS FROM article_views")->fetchAll(PDO::FETCH_ASSOC);
            $fields = array_map(function ($c) {
                return $c['Field'];
            }, $cols);
            $dateCol = null;
            foreach (['created_at', 'date_created', 'created', 'timestamp', 'date'] as $c) {
                if (in_array($c, $fields)) {
                    $dateCol = $c;
                    break;
                }
            }
            if (!$dateCol) {
                // fallback au premier champ si inconnu
                $dateCol = $fields[0] ?? 'created_at';
            }

            // Récupérer les vues agrégées par jour sur la période
            $sql = "SELECT DATE($dateCol) AS day, COUNT(*) AS views
                    FROM article_views
                    WHERE $dateCol >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                    GROUP BY day
                    ORDER BY day ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Construire un tableau complet de jours (pour afficher les jours sans vues)
            $map = [];
            foreach ($rows as $r) $map[$r['day']] = (int)$r['views'];

            $dates = [];
            $data = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $d = (new DateTime())->modify("-{$i} days")->format('Y-m-d');
                $dates[] = $d;
                $data[] = isset($map[$d]) ? $map[$d] : 0;
            }

            $viewsLabels = $dates;
            $viewsData = $data;

            // Répartition par catégorie (sur la même période)
            $articlesTableExists = (bool)$pdo->query("SHOW TABLES LIKE 'articles'")->fetch();
            if ($articlesTableExists) {
                // Vérifier si la table articles a la colonne 'category'
                $colsA = $pdo->query("SHOW COLUMNS FROM articles")->fetchAll(PDO::FETCH_ASSOC);
                $fieldsA = array_map(function ($c) {
                    return $c['Field'];
                }, $colsA);
                $hasCategory = in_array('category', $fieldsA);

                if ($tableExists && $hasCategory) {
                    $sql = "SELECT a.category AS category, COUNT(av.article_id) AS views
                            FROM articles a
                            LEFT JOIN article_views av ON a.article_id = av.article_id
                              AND av.$dateCol >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                            GROUP BY a.category
                            ORDER BY views DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
                    $stmt->execute();
                    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($cats as $c) {
                        $label = $c['category'] ?: 'Non catégorisé';
                        $categoryLabels[] = $label;
                        $categoryData[] = (int)$c['views'];
                    }
                } else {
                    // Fallback: utiliser la colonne views dans articles si présente
                    if (in_array('views', $fieldsA)) {
                        $sql = "SELECT COALESCE(category, 'Non catégorisé') AS category, SUM(views) AS views
                                FROM articles
                                GROUP BY category
                                ORDER BY views DESC";
                        $stmt = $pdo->query($sql);
                        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($cats as $c) {
                            $categoryLabels[] = $c['category'];
                            $categoryData[] = (int)$c['views'];
                        }
                    }
                }

                // Top articles (par vues) — aligner avec les données du dashboard
                // Si article_views existe, compter les entrées ; sinon utiliser articles.views
                if ($tableExists) {
                    // Compter les vues depuis article_views, mais créer l'agrégat sur le nombre de lignes
                    $sql = "SELECT a.title AS title, COUNT(av.article_id) AS views
                            FROM articles a
                            LEFT JOIN article_views av ON a.article_id = av.article_id
                            WHERE a.status = 'published'
                            GROUP BY a.article_id
                            HAVING views > 0
                            ORDER BY views DESC
                            LIMIT 5";
                    $stmt = $pdo->query($sql);
                    $tops = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // Si aucune donnée en article_views, repli sur articles.views
                    if (empty($tops) && in_array('views', $fieldsA)) {
                        $sql = "SELECT title, views FROM articles WHERE status = 'published' ORDER BY views DESC LIMIT 5";
                        $stmt = $pdo->query($sql);
                        $tops = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    foreach ($tops as $t) {
                        $topArticles[] = [
                            'title' => $t['title'] ?: 'Untitled',
                            'views' => (int)$t['views']
                        ];
                    }
                } else {
                    // Fallback: articles.views
                    if (in_array('views', $fieldsA)) {
                        $sql = "SELECT title, views FROM articles WHERE status = 'published' ORDER BY views DESC LIMIT 5";
                        $stmt = $pdo->query($sql);
                        $tops = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($tops as $t) {
                            $topArticles[] = ['title' => $t['title'], 'views' => (int)$t['views']];
                        }
                    }
                }
            }
        } else {
            // Pas de table article_views : utiliser la colonne views dans articles si existante
            if ($pdo->query("SHOW TABLES LIKE 'articles'")->fetch()) {
                $colsA = $pdo->query("SHOW COLUMNS FROM articles")->fetchAll(PDO::FETCH_ASSOC);
                $fieldsA = array_map(function ($c) {
                    return $c['Field'];
                }, $colsA);

                if (in_array('views', $fieldsA)) {
                    // Top articles
                    $sql = "SELECT title, views FROM articles ORDER BY views DESC LIMIT 5";
                    $stmt = $pdo->query($sql);
                    $tops = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tops as $t) $topArticles[] = ['title' => $t['title'], 'views' => (int)$t['views']];

                    // Répartition par catégorie
                    if (in_array('category', $fieldsA)) {
                        $sql = "SELECT COALESCE(category, 'Non catégorisé') AS category, SUM(views) AS views
                                FROM articles GROUP BY category ORDER BY views DESC";
                        $stmt = $pdo->query($sql);
                        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($cats as $c) {
                            $categoryLabels[] = $c['category'];
                            $categoryData[] = (int)$c['views'];
                        }
                    }

                    // Pour l'évolution des vues, on ne peut pas reconstituer les jours sans article_views
                    $viewsLabels = [];
                    $viewsData = [];
                }
            }
        }
    } catch (PDOException $e) {
        error_log('Statistics error: ' . $e->getMessage());
        // garder les valeurs par défaut vides
    }
}

// Si aucune donnée de période, remplir par des zéros (pour éviter erreurs JS)
if (empty($viewsLabels)) {
    for ($i = $days - 1; $i >= 0; $i--) {
        $d = (new DateTime())->modify("-{$i} days")->format('Y-m-d');
        $viewsLabels[] = $d;
        $viewsData[] = 0;
    }
}

// Données JSON pour JS
$viewsLabelsJson = json_encode($viewsLabels);
$viewsDataJson = json_encode($viewsData);
$categoryLabelsJson = json_encode($categoryLabels);
$categoryDataJson = json_encode($categoryData);
$topArticlesJson = json_encode($topArticles);
?>

<div class="page-header">
    <h1 class="page-title">Statistiques du site</h1>
    <p class="page-subtitle">Analyse des performances et de l'activité</p>
</div>

<!-- Statistiques principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo number_format($stats['total_views'], 0, ',', ' '); ?></h3>
                <p class="stat-label">Vues totales</p>
                <small class="stat-change text-success">
                    <i class="fas fa-arrow-up"></i> +12% ce mois
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo number_format($stats['total_users'], 0, ',', ' '); ?></h3>
                <p class="stat-label">Utilisateurs</p>
                <small class="stat-change text-success">
                    <i class="fas fa-arrow-up"></i> +8% ce mois
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo $stats['total_articles']; ?></h3>
                <p class="stat-label">Articles publiés</p>
                <small class="stat-change text-muted">
                    Stable
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-warning">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo $stats['new_comments']; ?></h3>
                <p class="stat-label">Commentaires</p>
                <small class="stat-change text-success">
                    <i class="fas fa-arrow-up"></i> +5 nouveaux
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Graphique de vues -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-area me-2"></i>Évolution des vues (<?php echo $days; ?> derniers jours)
                </h5>
                <div class="card-actions">
                    <form method="get" id="periodForm">
                        <select class="form-select form-select-sm" id="chartPeriod" name="days" onchange="document.getElementById('periodForm').submit();">
                            <option value="7" <?php echo $days == 7 ? 'selected' : ''; ?>>7 jours</option>
                            <option value="30" <?php echo $days == 30 ? 'selected' : ''; ?>>30 jours</option>
                            <option value="90" <?php echo $days == 90 ? 'selected' : ''; ?>>90 jours</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <canvas id="viewsChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Répartition par catégorie -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-pie me-2"></i>Répartition par catégorie
                </h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top articles -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-trophy me-2"></i>Top 5 articles les plus vus
                </h5>
            </div>
            <div class="card-body">
                <div class="top-list">
                    <?php
                    if (!empty($topArticles)) {
                        $rank = 1;
                        foreach ($topArticles as $t) {
                            echo '<div class="top-item">';
                            echo '<div class="top-rank">' . $rank . '</div>';
                            echo '<div class="top-content">';
                            echo '<strong>' . htmlspecialchars($t['title']) . '</strong>';
                            echo '<small class="text-muted">' . number_format($t['views'], 0, ',', ' ') . ' vues</small>';
                            echo '</div></div>';
                            $rank++;
                        }
                    } else {
                        echo '<p class="text-muted">Aucune donnée disponible</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-clock me-2"></i>Activité récente
                </h5>
            </div>
            <div class="card-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon bg-primary">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="activity-content">
                            <strong>Nouvelle vue</strong>
                            <small class="text-muted d-block">Consulter le tableau des vues pour les détails</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-success">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="activity-content">
                            <strong>Nouveau commentaire</strong>
                            <small class="text-muted d-block">Vérifier la section commentaires</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const viewsLabels = <?php echo $viewsLabelsJson; ?>;
    const viewsData = <?php echo $viewsDataJson; ?>;
    const categoryLabels = <?php echo $categoryLabelsJson; ?>;
    const categoryData = <?php echo $categoryDataJson; ?>;

    // Graphique de vues
    const ctx = document.getElementById('viewsChart').getContext('2d');
    const viewsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: viewsLabels,
            datasets: [{
                label: 'Vues',
                data: viewsData,
                borderColor: '#1e2a5e',
                backgroundColor: 'rgba(30, 42, 94, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique de répartition par catégorie
    const ctx2 = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: ['#1e2a5e', '#55679c', '#7c93c3', '#9eb4d9', '#c4d5ee', '#e6ecf5']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php require_once 'includes/admin-footer.php'; ?>