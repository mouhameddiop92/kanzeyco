<?php

/**
 * Fonctions d'accès aux contacts
 */
require_once __DIR__ . '/../../includes/database.php';

function getContacts($search = '', $status = '', $page = 1, $perPage = 20)
{
    $pdo = getDBConnection();
    if (!$pdo) {
        return ['data' => [], 'total' => 0];
    }

    $offset = max(0, ($page - 1) * $perPage);
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $like = '%' . $search . '%';
        $conditions[] = "(nom LIKE ? OR email LIKE ? OR message LIKE ? OR entreprise LIKE ? OR telephone LIKE ? )";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if (!empty($status)) {
        $conditions[] = "status = ?";
        $params[] = $status;
    }

    $where = '';
    if (count($conditions) > 0) {
        $where = ' WHERE ' . implode(' AND ', $conditions);
    }

    try {
        // total
        $countSql = "SELECT COUNT(*) as total FROM contacts" . $where;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // data
        $dataSql = "SELECT * FROM contacts" . $where . " ORDER BY date_created DESC LIMIT ? OFFSET ?";
        $dataStmt = $pdo->prepare($dataSql);
        $dataParams = $params;
        $dataParams[] = (int)$perPage;
        $dataParams[] = (int)$offset;
        $dataStmt->execute($dataParams);
        $data = $dataStmt->fetchAll();

        return ['data' => $data, 'total' => $total];
    } catch (PDOException $e) {
        error_log('Erreur getContacts: ' . $e->getMessage());
        return ['data' => [], 'total' => 0];
    }
}

function getContactById($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return null;

    try {
        $sql = "SELECT * FROM contacts WHERE contact_id = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Erreur getContactById: ' . $e->getMessage());
        return null;
    }
}

function markContactRead($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE contacts SET status = 'read' WHERE contact_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur markContactRead: ' . $e->getMessage());
        return false;
    }
}

function markContactUnread($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE contacts SET status = 'new' WHERE contact_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur markContactUnread: ' . $e->getMessage());
        return false;
    }
}

function archiveContact($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE contacts SET status = 'archived' WHERE contact_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur archiveContact: ' . $e->getMessage());
        return false;
    }
}

function deleteContact($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "DELETE FROM contacts WHERE contact_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur deleteContact: ' . $e->getMessage());
        return false;
    }
}

function markContactReplied($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE contacts SET status = 'replied' WHERE contact_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur markContactReplied: ' . $e->getMessage());
        return false;
    }
}
