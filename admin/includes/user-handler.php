<?php

/**
 * Gestion des utilisateurs - Fonctions CRUD
 */

require_once __DIR__ . '/../../includes/database.php';

/**
 * Créer un nouvel utilisateur
 */
function createUser($username, $email, $password, $role = 'author', $status = 'active')
{
    $pdo = getDBConnection();

    if (!$pdo) {
        return ['success' => false, 'message' => 'Erreur de connexion à la base de données'];
    }

    try {
        // Vérifier si l'utilisateur existe déjà
        $checkSql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$username, $email]);

        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'Cet utilisateur ou email existe déjà'];
        }

        // Hasher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insérer l'utilisateur
        $sql = "INSERT INTO users (username, email, password, role, status, date_created) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword, $role, $status]);

        return ['success' => true, 'message' => 'Utilisateur créé avec succès'];
    } catch (PDOException $e) {
        error_log("Erreur création utilisateur: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la création de l\'utilisateur'];
    }
}

/**
 * Mettre à jour un utilisateur
 */
function updateUser($userId, $username, $email, $role, $status)
{
    $pdo = getDBConnection();

    if (!$pdo) {
        return ['success' => false, 'message' => 'Erreur de connexion à la base de données'];
    }

    try {
        // Vérifier si le nouvel email/username n'existe pas ailleurs
        $checkSql = "SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$username, $email, $userId]);

        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'Cet utilisateur ou email existe déjà'];
        }

        // Mettre à jour l'utilisateur
        $sql = "UPDATE users SET username = ?, email = ?, role = ?, status = ?, date_updated = NOW() 
                WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $role, $status, $userId]);

        return ['success' => true, 'message' => 'Utilisateur mis à jour avec succès'];
    } catch (PDOException $e) {
        error_log("Erreur mise à jour utilisateur: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
    }
}

/**
 * Supprimer un utilisateur
 */
function deleteUser($userId)
{
    $pdo = getDBConnection();

    if (!$pdo) {
        return ['success' => false, 'message' => 'Erreur de connexion à la base de données'];
    }

    try {
        // Empêcher la suppression du dernier admin
        $adminCheckSql = "SELECT COUNT(*) as total FROM users WHERE role = 'admin' AND status = 'active'";
        $adminCheckStmt = $pdo->query($adminCheckSql);
        $adminCount = $adminCheckStmt->fetch()['total'];

        // Récupérer le rôle de l'utilisateur à supprimer
        $userSql = "SELECT role FROM users WHERE user_id = ?";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        }

        if ($user['role'] === 'admin' && $adminCount <= 1) {
            return ['success' => false, 'message' => 'Impossible de supprimer le dernier administrateur'];
        }

        // Supprimer l'utilisateur
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);

        return ['success' => true, 'message' => 'Utilisateur supprimé avec succès'];
    } catch (PDOException $e) {
        error_log("Erreur suppression utilisateur: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }
}

/**
 * Réinitialiser le mot de passe d'un utilisateur
 */
function resetUserPassword($userId, $newPassword)
{
    $pdo = getDBConnection();

    if (!$pdo) {
        return ['success' => false, 'message' => 'Erreur de connexion à la base de données'];
    }

    try {
        // Valider la longueur du mot de passe
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Le mot de passe doit faire au moins 6 caractères'];
        }

        // Hasher le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Mettre à jour le mot de passe
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expire = NULL, date_updated = NOW() 
                WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$hashedPassword, $userId]);

        return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès'];
    } catch (PDOException $e) {
        error_log("Erreur réinitialisation mot de passe: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la réinitialisation'];
    }
}

/**
 * Récupérer les détails d'un utilisateur
 */
function getUserById($userId)
{
    $pdo = getDBConnection();

    if (!$pdo) {
        return null;
    }

    try {
        $sql = "SELECT user_id, username, email, role, status, date_created FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erreur récupération utilisateur: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtenir tous les utilisateurs avec pagination
 */
function getAllUsers($page = 1, $perPage = 50)
{
    $pdo = getDBConnection();

    if (!$pdo) {
        return ['users' => [], 'total' => 0];
    }

    try {
        $offset = ($page - 1) * $perPage;

        // Récupérer le total
        $countSql = "SELECT COUNT(*) as total FROM users";
        $countStmt = $pdo->query($countSql);
        $total = $countStmt->fetch()['total'];

        // Récupérer les utilisateurs
        $sql = "SELECT user_id, username, email, role, status, last_login, date_created 
                FROM users 
                ORDER BY date_created DESC 
                LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return ['users' => $stmt->fetchAll(), 'total' => $total];
    } catch (PDOException $e) {
        error_log("Erreur récupération utilisateurs: " . $e->getMessage());
        return ['users' => [], 'total' => 0];
    }
}

/**
 * Compter les utilisateurs par statut et rôle
 */
function getUsersStats()
{
    $pdo = getDBConnection();

    if (!$pdo) {
        return [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'admins' => 0,
            'editors' => 0,
            'authors' => 0
        ];
    }

    try {
        $stats = [];

        // Total utilisateurs
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $stats['total'] = (int)$stmt->fetch()['total'];

        // Utilisateurs actifs
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
        $stats['active'] = (int)$stmt->fetch()['total'];

        // Utilisateurs inactifs
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status = 'inactive'");
        $stats['inactive'] = (int)$stmt->fetch()['total'];

        // Par rôle
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
        $stats['admins'] = (int)$stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'editor'");
        $stats['editors'] = (int)$stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'author'");
        $stats['authors'] = (int)$stmt->fetch()['total'];

        return $stats;
    } catch (PDOException $e) {
        error_log("Erreur stats utilisateurs: " . $e->getMessage());
        return [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'admins' => 0,
            'editors' => 0,
            'authors' => 0
        ];
    }
}
