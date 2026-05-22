<?php
$pageTitle = "Tableau de bord";
require_once 'includes/admin-header.php';
require_once '../includes/articles-db.php';

$adminRole = $_SESSION['admin_role'] ?? 'admin';
$isAdminUser = $adminRole === 'admin';
$isAuthorUser = $adminRole === 'author';
$authorUsername = $_SESSION['admin_username'] ?? null;
$stats = getDashboardStats($isAuthorUser ? $authorUsername : null);
error_log("Dashboard stats: " . json_encode($stats));

$recentArticles = getArticles(null, 5);
if ($isAuthorUser) {
    $recentArticles = array_values(array_filter($recentArticles, function ($article) use ($authorUsername) {
        return isset($article['author']) && $article['author'] === $authorUsername;
    }));
}

$viewsChartLabels = [];
$viewsChartData = [];
$viewsChartSource = null;
$viewsChartNote = null;

// Récupérer le nombre de contacts non lus
$unreadContacts = 0;
$pdo = getDBConnection();
if ($pdo) {
    try {
        $sql = "SELECT COUNT(*) as total FROM contacts WHERE status = 'new'";
        $stmt = $pdo->query($sql);
        $unreadContacts = (int)($stmt->fetch()['total'] ?? 0);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des contacts: " . $e->getMessage());
    }
}

// Construire les données du graphique des 7 derniers jours
try {
    $days = 7;
    $tableExists = (bool)$pdo->query("SHOW TABLES LIKE 'article_views'")->fetch();
    $dateCol = null;

    if ($tableExists) {
        $cols = $pdo->query("SHOW COLUMNS FROM article_views")->fetchAll(PDO::FETCH_ASSOC);
        $fields = array_map(function ($c) {
            return $c['Field'];
        }, $cols);
        foreach (["created_at", "date_created", "created", "timestamp", "date"] as $c) {
            if (in_array($c, $fields)) {
                $dateCol = $c;
                break;
            }
        }
        $dateCol = $dateCol ?: ($fields[0] ?? 'created_at');

        if ($isAuthorUser) {
            $sql = "SELECT DATE(av.$dateCol) AS day, COUNT(*) AS views
                        FROM article_views av
                        JOIN articles a ON a.article_id = av.article_id
                        WHERE a.author = :author
                          AND a.status = 'published'
                          AND av.$dateCol >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                        GROUP BY day
                        ORDER BY day ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':author', $authorUsername);
        } else {
            $sql = "SELECT DATE($dateCol) AS day, COUNT(*) AS views
                        FROM article_views
                        WHERE $dateCol >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                        GROUP BY day
                        ORDER BY day ASC";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $r) {
            $map[$r['day']] = (int)$r['views'];
        }

        for ($i = $days - 1; $i >= 0; $i--) {
            $d = (new DateTime())->modify("-{$i} days")->format('Y-m-d');
            $viewsChartLabels[] = $d;
            $viewsChartData[] = $map[$d] ?? 0;
        }

        $viewsChartSource = 'article_views';
    } else {
        // Si pas de table article_views, repli sur la colonne views dans articles (pas d'historique)
        $colsA = $pdo->query("SHOW COLUMNS FROM articles")->fetchAll(PDO::FETCH_ASSOC);
        $fieldsA = array_map(function ($c) {
            return $c['Field'];
        }, $colsA);
        if (in_array('views', $fieldsA)) {
            if ($isAuthorUser) {
                $sql = "SELECT SUM(views) as total FROM articles WHERE author = :author AND status = 'published'";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':author', $authorUsername);
                $stmt->execute();
                $total = (int)($stmt->fetch()['total'] ?? 0);
            } else {
                $sql = "SELECT SUM(views) as total FROM articles";
                $stmt = $pdo->query($sql);
                $total = (int)($stmt->fetch()['total'] ?? 0);
            }
            // remplir par zéros (historique non disponible) et garder total dans les cards
            for ($i = 0; $i < $days; $i++) {
                $viewsChartLabels[] = (new DateTime())->modify("-{$i} days")->format('Y-m-d');
                $viewsChartData[] = 0;
            }
            $viewsChartNote = 'Historique des vues non disponible (utilisation de articles.views).';
        }
    }
} catch (PDOException $e) {
    error_log('Erreur lecture vues dashboard: ' . $e->getMessage());
}

// Si aucune donnée, remplir par des zéros pour éviter erreur JS
if (empty($viewsChartLabels)) {
    $days = $days ?? 7;
    for ($i = $days - 1; $i >= 0; $i--) {
        $d = (new DateTime())->modify("-{$i} days")->format('Y-m-d');
        $viewsChartLabels[] = $d;
        $viewsChartData[] = 0;
    }
}

?>
<div class="page-header">
    <h1 class="page-title">Tableau de bord</h1>
    <p class="page-subtitle">Vue d'ensemble de votre site</p>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo $stats['total_articles']; ?></h3>
                <p class="stat-label">Articles publiés</p>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value"><?php echo number_format((int)($stats['total_views'] ?? 0), 0, ',', ' '); ?></h3>
                <p class="stat-label">Vues totales</p>
            </div>
        </div>
    </div>

    <?php if ($isAdminUser): ?>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value"><?php echo number_format($stats['total_users'], 0, ',', ' '); ?></h3>
                    <p class="stat-label">Utilisateurs</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isAdminUser): ?>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-warning">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value"><?php echo $unreadContacts; ?></h3>
                    <p class="stat-label">Nouveaux messages</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Contenu principal -->
<div class="row">
    <!-- Articles récents -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-newspaper me-2"></i>Articles récents
                </h5>
                <a href="articles.php" class="btn-link">Voir tout</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentArticles as $article): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($article['category']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($article['date']); ?></td>
                                    <td>
                                        <a href="articles.php?action=edit&slug=<?php echo $article['slug']; ?>" class="btn-icon" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>article-detail.php?slug=<?php echo $article['slug']; ?>" target="_blank" class="btn-icon" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique et activité récente -->
    <div class="col-lg-4 mb-4">
        <!-- Graphique de vues -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-line me-2"></i>Vues (7 derniers jours)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="viewsChart" height="200"></canvas>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bolt me-2"></i>Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="articles.php?action=new" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nouvel article</span>
                    </a>
                    <?php if ($isAdminUser): ?>
                        <a href="statistics.php" class="quick-action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span>Voir statistiques</span>
                        </a>
                        <a href="settings.php" class="quick-action-btn">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>index.php" target="_blank" class="quick-action-btn">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Voir le site</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Données JS pour le graphique (générées par PHP)
    const viewsLabels = <?php echo json_encode($viewsChartLabels); ?>;
    const viewsData = <?php echo json_encode($viewsChartData); ?>;

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
                backgroundColor: 'rgba(30, 42, 94, 0.08)',
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
</script>

<?php require_once 'includes/admin-footer.php'; ?>