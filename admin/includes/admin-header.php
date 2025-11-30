<?php
require_once __DIR__ . '/config.php';
requireLogin();

// Calculer le nombre de notifications (messages non lus + commentaires en attente)
$unreadContacts = 0;
$pendingComments = 0;
$notificationCount = 0;
$pdo = null;
try {
    $pdo = getDBConnection();
    if ($pdo) {
        // messages non lus (status = 'new')
        $stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'");
        $unreadContacts = (int) $stmt->fetchColumn();

        // commentaires en attente (status = 'pending')
        $stmt2 = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'");
        $pendingComments = (int) $stmt2->fetchColumn();

        $notificationCount = $unreadContacts + $pendingComments;
    }
} catch (Exception $e) {
    error_log('Erreur lecture notifications admin-header: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Dashboard Admin - KANZEYCO</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body class="admin-body">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../assets/images/Logo Kanzey Co.png" alt="KANZEYCO Logo" class="sidebar-logo">
            <h3 class="sidebar-title">KANZEYCO</h3>
            <p class="sidebar-subtitle">Administration</p>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="articles.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'articles.php' ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>Articles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="realisations.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'realisations.php' ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>Réalisations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="statistics.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'statistics.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistiques</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../index.php" class="nav-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Voir le site</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation -->
        <header class="top-navbar">
            <div class="navbar-content">
                <button class="btn-toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-right">
                    <div class="notifications">
                        <button class="btn-notification" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge"><?php echo (int)$notificationCount; ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <li><a class="dropdown-item" href="./contacts.php">Nouveau message<?php echo $unreadContacts ? ' (' . $unreadContacts . ')' : ''; ?></a></li>
                            <hr class="dropdown-divider">
                            <li><a class="dropdown-item" href="./comments.php">Nouveaux commentaires<?php echo $pendingComments ? ' (' . $pendingComments . ')' : ''; ?></a></li>
                            <hr class="dropdown-divider">
                            <li><a class="dropdown-item" href="./newsletter.php">Newsletter</li>
                                
                        </ul>
                    </div>

                    <div class="admin-profile dropdown">
                        <button class="btn-profile dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="content-wrapper">