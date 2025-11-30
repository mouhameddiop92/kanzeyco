<?php

/**
 * Fonctions d'accès aux commentaires
 */
require_once __DIR__ . '/../../includes/database.php';

function getComments($search = '', $articleId = '', $status = '', $page = 1, $perPage = 20)
{
    $pdo = getDBConnection();
    if (!$pdo) return ['data' => [], 'total' => 0];

    $offset = max(0, ($page - 1) * $perPage);
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $like = '%' . $search . '%';
        $conditions[] = "(c.nom LIKE ? OR c.email LIKE ? OR c.message LIKE ?)";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if (!empty($articleId)) {
        $conditions[] = "c.article_id = ?";
        $params[] = $articleId;
    }

    if (!empty($status)) {
        $conditions[] = "c.status = ?";
        $params[] = $status;
    }

    $where = '';
    if (count($conditions) > 0) {
        $where = ' WHERE ' . implode(' AND ', $conditions);
    }

    try {
        $countSql = "SELECT COUNT(*) FROM comments c" . $where;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT c.*, a.title as article_title FROM comments c LEFT JOIN articles a ON c.article_id = a.article_id" . $where . " ORDER BY c.date_created DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $execParams = $params;
        $execParams[] = (int)$perPage;
        $execParams[] = (int)$offset;
        $stmt->execute($execParams);
        $data = $stmt->fetchAll();

        return ['data' => $data, 'total' => $total];
    } catch (PDOException $e) {
        error_log('Erreur getComments: ' . $e->getMessage());
        return ['data' => [], 'total' => 0];
    }
}

function getCommentById($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return null;
    try {
        $sql = "SELECT c.*, a.title as article_title FROM comments c LEFT JOIN articles a ON c.article_id = a.article_id WHERE c.comment_id = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        // Normaliser les noms de colonnes pour le template
        if ($result) {
            $result['author_name'] = $result['nom'] ?? '';
            $result['author_email'] = $result['email'] ?? '';
            $result['content'] = $result['message'] ?? '';
            $result['created_at'] = $result['date_created'] ?? '';
        }
        return $result;
    } catch (PDOException $e) {
        error_log('Erreur getCommentById: ' . $e->getMessage());
        return null;
    }
}

function deleteComment($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "DELETE FROM comments WHERE comment_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur deleteComment: ' . $e->getMessage());
        return false;
    }
}

function approveComment($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE comments SET status = 'approved' WHERE comment_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur approveComment: ' . $e->getMessage());
        return false;
    }
}

function unapproveComment($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE comments SET status = 'pending' WHERE comment_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur unapproveComment: ' . $e->getMessage());
        return false;
    }
}
