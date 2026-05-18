<?php

/**
 * Configuration et authentification du dashboard administrateur
 */

// Démarrer la session (seulement si pas déjà démarrée)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la configuration de la base de données et la config globale
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/config.php';

// Configuration de la base de données (utilise les constantes de database.php)
// Les constantes sont déjà définies dans includes/database.php

// Vérifier si l'utilisateur est connecté
function isAdminLoggedIn()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Rediriger vers la page de connexion si non connecté
function requireLogin()
{
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Fonction de connexion avec vérification dans la base de données
function adminLogin($username, $password)
{
    $pdo = getDBConnection();

    if ($pdo === null) {
        error_log("Erreur de connexion BDD dans adminLogin().");
        return false;
    }

    try {
        // Essayer d'abord par username
        $sql = "SELECT user_id, username, email, password, role, status 
                FROM users 
                WHERE username = :username 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch();

        // Si pas trouvé par username, essayer par email
        if (!$user) {
            $sql2 = "SELECT user_id, username, email, password, role, status 
                     FROM users 
                     WHERE email = :email 
                     LIMIT 1";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindValue(':email', $username);
            $stmt2->execute();
            $user = $stmt2->fetch();
        }

        // Si l'utilisateur existe et que le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {

            // Créer la session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_user_id'] = $user['user_id'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_login_time'] = time();

            // Mettre à jour la date de dernière connexion (optionnel, ne bloque pas la connexion)
            try {
                $updateSql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$user['user_id']]);
            } catch (PDOException $e) {
                // Ignorer l'erreur de mise à jour de last_login, ce n'est pas critique
                error_log("Note: Erreur lors de la mise à jour de last_login: " . $e->getMessage());
            }

            return true;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Erreur lors de la connexion: " . $e->getMessage());
        return false;
    }
}

// Fonction de déconnexion
function adminLogout()
{
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Récupérer les statistiques du dashboard depuis la base de données
function getDashboardStats()
{
    $pdo = getDBConnection();

    // Si la BDD n'est pas disponible, retourner des valeurs par défaut
    if (!$pdo) {
        return [
            'total_articles' => 6,
            'total_views' => 12450,
            'total_users' => 1,
            'new_comments' => 0,
            'total_newsletter' => 0,
            'recent_articles' => 3,
            'popular_articles' => 5
        ];
    }

    try {
        // Total d'articles publiés
        $sql = "SELECT COUNT(*) as total FROM articles WHERE status = 'published'";
        $stmt = $pdo->query($sql);
        $totalArticles = $stmt->fetch()['total'];

        // Total de vues : privilégier article_views si elle existe, sinon articles.views
        $totalViews = 0;
        $tableExists = (bool)$pdo->query("SHOW TABLES LIKE 'article_views'")->fetch();
        if ($tableExists) {
            $sql = "SELECT COUNT(*) as total FROM article_views";
            $stmt = $pdo->query($sql);
            $totalViews = (int)($stmt->fetch()['total'] ?? 0);
        }
        // Si article_views n'existe pas ou est vide, utiliser SUM(views) de articles
        if ($totalViews === 0) {
            $sql = "SELECT SUM(views) as total FROM articles";
            $stmt = $pdo->query($sql);
            $totalViews = (int)($stmt->fetch()['total'] ?? 0);
        }

        // Total d'utilisateurs
        $sql = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
        $stmt = $pdo->query($sql);
        $totalUsers = $stmt->fetch()['total'];

        // Nouveaux commentaires en attente
        $sql = "SELECT COUNT(*) as total FROM comments WHERE status = 'pending'";
        $stmt = $pdo->query($sql);
        $newComments = $stmt->fetch()['total'];

        return [
            'total_articles' => (int)$totalArticles,
            'total_views' => (int)$totalViews,
            'total_users' => (int)$totalUsers,
            'new_comments' => (int)$newComments,
            'total_newsletter' => (int)($pdo->query("SELECT COUNT(*) as total FROM newsletter WHERE status = 'active'")->fetch()['total'] ?? 0),
            'recent_articles' => (int)$totalArticles > 3 ? 3 : (int)$totalArticles,
            'popular_articles' => 5
        ];
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
        return [
            'total_articles' => 0,
            'total_views' => 0,
            'total_users' => 0,
            'new_comments' => 0,
            'recent_articles' => 0,
            'popular_articles' => 0
        ];
    }
}
