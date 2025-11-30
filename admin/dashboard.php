<?php
$pageTitle = "Tableau de bord";
require_once 'includes/admin-header.php';
require_once '../includes/articles-data.php';

$stats = getDashboardStats();
error_log("Dashboard stats: " . json_encode($stats));

$articles = require '../includes/articles-data.php';
$recentArticles = array_slice($articles, 0, 5);

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
?>

<div class="page-header">
    <h1 class="page-title">Tableau de bord</h1>
    <p class="page-subtitle">Vue d'ensemble de votre site</p>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
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

    <div class="col-md-3 mb-3">
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

    <div class="col-md-3 mb-3">
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

    <div class="col-md-3 mb-3">
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
                                        <a href="../article-detail.php?slug=<?php echo $article['slug']; ?>" target="_blank" class="btn-icon" title="Voir">
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
                    <a href="statistics.php" class="quick-action-btn">
                        <i class="fas fa-chart-bar"></i>
                        <span>Voir statistiques</span>
                    </a>
                    <a href="settings.php" class="quick-action-btn">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="../index.php" target="_blank" class="quick-action-btn">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Voir le site</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Graphique de vues
    const ctx = document.getElementById('viewsChart').getContext('2d');
    const viewsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Vues',
                data: [450, 520, 480, 610, 580, 730, 680],
                borderColor: '#1e2a5e',
                backgroundColor: 'rgba(30, 42, 94, 0.1)',
                tension: 0.4,
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