<?php

/**
 * Handler pour les réalisations (cas clients)
 */
require_once __DIR__ . '/../../includes/database.php';

function getRealisations($search = '', $status = '', $page = 1, $perPage = 20)
{
    $pdo = getDBConnection();
    if (!$pdo) return ['data' => [], 'total' => 0];

    $offset = max(0, ($page - 1) * $perPage);
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $like = '%' . $search . '%';
        $conditions[] = "(title LIKE ? OR description LIKE ? OR content LIKE ?)";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if (!empty($status)) {
        $conditions[] = "status = ?";
        $params[] = $status;
    }

    $where = '';
    if (count($conditions) > 0) $where = ' WHERE ' . implode(' AND ', $conditions);

    try {
        $countSql = "SELECT COUNT(*) FROM realisations" . $where;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT * FROM realisations" . $where . " ORDER BY date_created DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $execParams = $params;
        $execParams[] = (int)$perPage;
        $execParams[] = (int)$offset;
        $stmt->execute($execParams);
        $data = $stmt->fetchAll();

        return ['data' => $data, 'total' => $total];
    } catch (PDOException $e) {
        error_log('Erreur getRealisations: ' . $e->getMessage());
        return ['data' => [], 'total' => 0];
    }
}

function getRealisationById($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return null;
    try {
        $sql = "SELECT * FROM realisations WHERE realisation_id = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Erreur getRealisationById: ' . $e->getMessage());
        return null;
    }
}

function createRealisation($title, $description, $content, $imagePath = null, $status = 'draft')
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "INSERT INTO realisations (title, description, content, image, status, date_created) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$title, $description, $content, $imagePath, $status]);
    } catch (PDOException $e) {
        error_log('Erreur createRealisation: ' . $e->getMessage());
        return false;
    }
}

function updateRealisation($id, $title, $description, $content, $imagePath = null, $status = 'draft')
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        if ($imagePath !== null) {
            $sql = "UPDATE realisations SET title = ?, description = ?, content = ?, image = ?, status = ?, date_updated = NOW() WHERE realisation_id = ?";
            $params = [$title, $description, $content, $imagePath, $status, $id];
        } else {
            $sql = "UPDATE realisations SET title = ?, description = ?, content = ?, status = ?, date_updated = NOW() WHERE realisation_id = ?";
            $params = [$title, $description, $content, $status, $id];
        }
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log('Erreur updateRealisation: ' . $e->getMessage());
        return false;
    }
}

function deleteRealisation($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        // optionnel : récupérer le chemin image pour le supprimer
        $row = getRealisationById($id);
        if ($row && !empty($row['image'])) {
            $full = __DIR__ . '/../../' . $row['image'];
            if (file_exists($full)) @unlink($full);
        }

        $sql = "DELETE FROM realisations WHERE realisation_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur deleteRealisation: ' . $e->getMessage());
        return false;
    }
}
