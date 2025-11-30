<?php

/**
 * Handler pour la table newsletter
 */
require_once __DIR__ . '/../../includes/database.php';

function getSubscribers($search = '', $status = '', $page = 1, $perPage = 50)
{
    $pdo = getDBConnection();
    if (!$pdo) return ['data' => [], 'total' => 0];

    $offset = max(0, ($page - 1) * $perPage);
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $like = '%' . $search . '%';
        $conditions[] = "(email LIKE ?)";
        $params[] = $like;
    }

    if (!empty($status)) {
        $conditions[] = "status = ?";
        $params[] = $status;
    }

    $where = '';
    if (count($conditions) > 0) $where = ' WHERE ' . implode(' AND ', $conditions);

    try {
        $countSql = "SELECT COUNT(*) FROM newsletter" . $where;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT * FROM newsletter" . $where . " ORDER BY date_subscribed DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $execParams = $params;
        $execParams[] = (int)$perPage;
        $execParams[] = (int)$offset;
        $stmt->execute($execParams);
        $data = $stmt->fetchAll();

        return ['data' => $data, 'total' => $total];
    } catch (PDOException $e) {
        error_log('Erreur getSubscribers: ' . $e->getMessage());
        return ['data' => [], 'total' => 0];
    }
}

function getSubscriberById($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return null;
    try {
        $sql = "SELECT * FROM newsletter WHERE newsletter_id = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Erreur getSubscriberById: ' . $e->getMessage());
        return null;
    }
}

function unsubscribeSubscriber($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE newsletter SET status = 'inactive', date_unsubscribed = NOW() WHERE newsletter_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur unsubscribeSubscriber: ' . $e->getMessage());
        return false;
    }
}

function reactivateSubscriber($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "UPDATE newsletter SET status = 'active', date_subscribed = NOW(), date_unsubscribed = NULL WHERE newsletter_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur reactivateSubscriber: ' . $e->getMessage());
        return false;
    }
}

function deleteSubscriber($id)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;
    try {
        $sql = "DELETE FROM newsletter WHERE newsletter_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur deleteSubscriber: ' . $e->getMessage());
        return false;
    }
}

function getNewsletterStats()
{
    $pdo = getDBConnection();
    if (!$pdo) return ['total' => 0, 'active' => 0, 'inactive' => 0];
    try {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM newsletter")->fetchColumn();
        $active = (int)$pdo->query("SELECT COUNT(*) FROM newsletter WHERE status = 'active'")->fetchColumn();
        $inactive = (int)$pdo->query("SELECT COUNT(*) FROM newsletter WHERE status != 'active'")->fetchColumn();
        return ['total' => $total, 'active' => $active, 'inactive' => $inactive];
    } catch (PDOException $e) {
        error_log('Erreur getNewsletterStats: ' . $e->getMessage());
        return ['total' => 0, 'active' => 0, 'inactive' => 0];
    }
}

function exportSubscribersCSV($status = '')
{
    $pdo = getDBConnection();
    if (!$pdo) return '';
    try {
        if ($status) {
            $stmt = $pdo->prepare("SELECT email, status, date_subscribed, date_unsubscribed FROM newsletter WHERE status = ? ORDER BY date_subscribed DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query("SELECT email, status, date_subscribed, date_unsubscribed FROM newsletter ORDER BY date_subscribed DESC");
        }
        $rows = $stmt->fetchAll();
        $out = fopen('php://memory', 'r+');
        fputcsv($out, ['email', 'status', 'date_subscribed', 'date_unsubscribed']);
        foreach ($rows as $r) fputcsv($out, [$r['email'], $r['status'], $r['date_subscribed'], $r['date_unsubscribed']]);
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $csv;
    } catch (PDOException $e) {
        error_log('Erreur exportSubscribersCSV: ' . $e->getMessage());
        return '';
    }
}
