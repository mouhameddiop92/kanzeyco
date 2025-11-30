<?php

/**
 * Fonctions pour gérer les articles depuis la base de données
 */

require_once __DIR__ . '/database.php';

/**
 * Récupère tous les articles publiés
 * @param string|null $category Filtrer par catégorie
 * @param int|null $limit Limite le nombre de résultats
 * @return array
 */
function getArticles($category = null, $limit = null)
{
    $pdo = getDBConnection();
    if (!$pdo) {
        // Fallback vers le fichier si la BDD n'est pas disponible
        return include __DIR__ . '/articles-data.php';
    }

    try {
        $sql = "SELECT * FROM articles WHERE status = 'published'";
        $params = [];

        if ($category) {
            $sql .= " AND category = :category";
            $params[':category'] = $category;
        }

        $sql .= " ORDER BY date_published DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }

        $stmt = $pdo->prepare($sql);

        // Bind des paramètres
        foreach ($params as $key => $value) {
            if ($key === ':limit') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        $articles = $stmt->fetchAll();

        // Convertir les données JSON
        foreach ($articles as &$article) {
            $article['content'] = json_decode($article['content'], true);
            $article['tags'] = json_decode($article['tags'], true) ?? [];

            // Formater la date
            $date = new DateTime($article['date_published']);
            $article['date'] = $date->format('d F Y');
        }

        return $articles;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des articles: " . $e->getMessage());
        // Fallback vers le fichier
        return include __DIR__ . '/articles-data.php';
    }
}

/**
 * Récupère un article par son slug
 * @param string $slug
 * @return array|null
 */
function getArticleBySlug($slug)
{
    $pdo = getDBConnection();
    if (!$pdo) {
        // Fallback vers le fichier
        $articles = include __DIR__ . '/articles-data.php';
        foreach ($articles as $article) {
            if ($article['slug'] === $slug) {
                return $article;
            }
        }
        return null;
    }

    try {
        $sql = "SELECT * FROM articles WHERE slug = :slug AND status = 'published' LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();

        $article = $stmt->fetch();

        if ($article) {
            // Convertir les données JSON
            $article['content'] = json_decode($article['content'], true);
            $article['tags'] = json_decode($article['tags'], true) ?? [];

            // Formater la date
            $date = new DateTime($article['date_published']);
            $article['date'] = $date->format('d F Y');

            // Incrémenter les vues
            incrementArticleViews($article['article_id']);
        }

        return $article ?: null;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'article: " . $e->getMessage());
        return null;
    }
}

/**
 * Incrémente le compteur de vues d'un article
 * @param int $articleId
 */
function incrementArticleViews($articleId)
{
    $pdo = getDBConnection();
    if (!$pdo) return;

    try {
        // Mettre à jour le compteur dans la table articles
        $sql = "UPDATE articles SET views = views + 1 WHERE article_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        // Enregistrer la vue dans article_views pour statistiques détaillées
        $sql = "INSERT INTO article_views (article_id, ip_address, user_agent) 
                VALUES (:id, :ip, :user_agent)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? null);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de l'incrémentation des vues: " . $e->getMessage());
    }
}

/**
 * Récupère les commentaires d'un article
 * @param int $articleId
 * @param string $status Statut des commentaires à récupérer ('approved', 'pending', 'all', ou null pour tous sauf 'deleted')
 * @return array
 */
function getArticleComments($articleId, $status = 'all')
{
    $pdo = getDBConnection();
    if (!$pdo) return [];

    try {
        $sql = "SELECT * FROM comments 
                WHERE article_id = :id";

        // Filtrer par statut si spécifié
        if ($status === 'approved') {
            $sql .= " AND status = 'approved'";
        } elseif ($status === 'pending') {
            $sql .= " AND status = 'pending'";
        } elseif ($status !== 'all') {
            $sql .= " AND status = :status";
        } else {
            // 'all' = tous les commentaires (pas de filtre supplémentaire)
        }

        $sql .= " ORDER BY date_created DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
        if ($status !== 'approved' && $status !== 'pending' && $status !== 'all') {
            $stmt->bindValue(':status', $status);
        }
        $stmt->execute();

        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("getArticleComments: article_id=$articleId, status=$status, count=" . count($comments));
        return $comments;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des commentaires: " . $e->getMessage());
        return [];
    }
}

/**
 * Ajoute un commentaire à un article
 * @param int $articleId
 * @param string $nom
 * @param string $email
 * @param string $message
 * @return bool
 */
function addComment($articleId, $nom, $email, $message)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;

    try {
        $sql = "INSERT INTO comments (article_id, nom, email, message, status) 
                VALUES (:article_id, :nom, :email, :message, 'pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':message', $message);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout du commentaire: " . $e->getMessage());
        return false;
    }
}
