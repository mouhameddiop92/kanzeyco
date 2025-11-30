<?php
$pageTitle = "Statistiques";
require_once 'includes/admin-header.php';

$stats = getDashboardStats();
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
                    <i class="fas fa-chart-area me-2"></i>Évolution des vues (30 derniers jours)
                </h5>
                <div class="card-actions">
                    <select class="form-select form-select-sm" id="chartPeriod">
                        <option value="7">7 jours</option>
                        <option value="30" selected>30 jours</option>
                        <option value="90">90 jours</option>
                    </select>
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
                    <div class="top-item">
                        <div class="top-rank">1</div>
                        <div class="top-content">
                            <strong>Comment la digitalisation transforme l'événementiel</strong>
                            <small class="text-muted">2,450 vues</small>
                        </div>
                    </div>
                    <div class="top-item">
                        <div class="top-rank">2</div>
                        <div class="top-content">
                            <strong>PropTech 2025 : les tendances</strong>
                            <small class="text-muted">1,890 vues</small>
                        </div>
                    </div>
                    <div class="top-item">
                        <div class="top-rank">3</div>
                        <div class="top-content">
                            <strong>Automatisation et IA : boostez votre productivité</strong>
                            <small class="text-muted">1,520 vues</small>
                        </div>
                    </div>
                    <div class="top-item">
                        <div class="top-rank">4</div>
                        <div class="top-content">
                            <strong>Success Story : EventPro multiplie ses ventes</strong>
                            <small class="text-muted">1,280 vues</small>
                        </div>
                    </div>
                    <div class="top-item">
                        <div class="top-rank">5</div>
                        <div class="top-content">
                            <strong>Marketing digital : 10 stratégies efficaces</strong>
                            <small class="text-muted">1,150 vues</small>
                        </div>
                    </div>
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
                            <small class="text-muted d-block">Article "PropTech 2025" - Il y a 5 min</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-success">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="activity-content">
                            <strong>Nouveau commentaire</strong>
                            <small class="text-muted d-block">Sur "Digitalisation événementiel" - Il y a 15 min</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-info">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-content">
                            <strong>Nouvel utilisateur</strong>
                            <small class="text-muted d-block">Inscription - Il y a 1h</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-warning">
                            <i class="fas fa-share"></i>
                        </div>
                        <div class="activity-content">
                            <strong>Article partagé</strong>
                            <small class="text-muted d-block">"Automatisation et IA" - Il y a 2h</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Graphique de vues sur 30 jours
const ctx = document.getElementById('viewsChart').getContext('2d');
const viewsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['J1', 'J2', 'J3', 'J4', 'J5', 'J6', 'J7', 'J8', 'J9', 'J10', 'J11', 'J12', 'J13', 'J14', 'J15', 'J16', 'J17', 'J18', 'J19', 'J20', 'J21', 'J22', 'J23', 'J24', 'J25', 'J26', 'J27', 'J28', 'J29', 'J30'],
        datasets: [{
            label: 'Vues',
            data: [320, 450, 380, 520, 480, 610, 580, 730, 680, 720, 650, 780, 710, 820, 750, 880, 810, 920, 850, 960, 890, 1000, 930, 1020, 950, 1040, 970, 1060, 990, 1080],
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

// Graphique de répartition par catégorie
const ctx2 = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Événementiel', 'Immobilier', 'Innovation', 'Marketing', 'Analytics', 'Success Story'],
        datasets: [{
            data: [25, 20, 18, 15, 12, 10],
            backgroundColor: [
                '#1e2a5e',
                '#55679c',
                '#7c93c3',
                '#9eb4d9',
                '#c4d5ee',
                '#e6ecf5'
            ]
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

