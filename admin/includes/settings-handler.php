<?php

/**
 * Fonctions de gestion des paramètres du site
 */
require_once __DIR__ . '/../../includes/database.php';

/**
 * Initialise la table settings si elle n'existe pas
 */
function initSettingsTable()
{
    $pdo = getDBConnection();
    if (!$pdo) return false;

    try {
        // Vérifier si la table existe
        $result = $pdo->query("SHOW TABLES LIKE 'settings'")->fetch();
        if ($result) return true;

        // Créer la table
        $sql = "CREATE TABLE settings (
            setting_id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(255) NOT NULL UNIQUE,
            setting_value LONGTEXT,
            setting_type VARCHAR(50) DEFAULT 'string',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $pdo->exec($sql);
        error_log("Table settings créée");
        return true;
    } catch (PDOException $e) {
        error_log("Erreur création table settings: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère un paramètre
 * @param string $key Clé du paramètre
 * @param mixed $default Valeur par défaut
 * @return mixed
 */
function getSetting($key, $default = null)
{
    $pdo = getDBConnection();
    if (!$pdo) return $default;

    try {
        $sql = "SELECT setting_value, setting_type FROM settings WHERE setting_key = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$key]);
        $result = $stmt->fetch();

        if (!$result) return $default;

        // Décoder selon le type
        $value = $result['setting_value'];
        if ($result['setting_type'] === 'json') {
            $value = json_decode($value, true);
        } elseif ($result['setting_type'] === 'boolean') {
            $value = (bool)$value;
        } elseif ($result['setting_type'] === 'integer') {
            $value = (int)$value;
        }

        return $value;
    } catch (PDOException $e) {
        error_log("Erreur getSetting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Récupère tous les paramètres
 * @return array
 */
function getAllSettings()
{
    $pdo = getDBConnection();
    if (!$pdo) return [];

    try {
        $sql = "SELECT setting_key, setting_value, setting_type FROM settings";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($results as $row) {
            $value = $row['setting_value'];
            if ($row['setting_type'] === 'json') {
                $value = json_decode($value, true);
            } elseif ($row['setting_type'] === 'boolean') {
                $value = (bool)$value;
            } elseif ($row['setting_type'] === 'integer') {
                $value = (int)$value;
            }
            $settings[$row['setting_key']] = $value;
        }

        return $settings;
    } catch (PDOException $e) {
        error_log("Erreur getAllSettings: " . $e->getMessage());
        return [];
    }
}

/**
 * Sauvegarde un paramètre
 * @param string $key Clé du paramètre
 * @param mixed $value Valeur du paramètre
 * @param string $type Type du paramètre (string, json, boolean, integer)
 * @return bool
 */
function saveSetting($key, $value, $type = 'string')
{
    $pdo = getDBConnection();
    if (!$pdo) return false;

    // Encoder selon le type
    $encoded = $value;
    if ($type === 'json') {
        $encoded = json_encode($value);
    } elseif ($type === 'boolean') {
        $encoded = $value ? '1' : '0';
    }

    try {
        // Essayer une insertion d'abord (INSERT IGNORE ou UPDATE)
        $sql = "INSERT INTO settings (setting_key, setting_value, setting_type) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                setting_type = VALUES(setting_type)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$key, $encoded, $type]);

        error_log("Paramètre sauvegardé: $key");
        return true;
    } catch (PDOException $e) {
        error_log("Erreur saveSetting: " . $e->getMessage());
        return false;
    }
}

/**
 * Sauvegarde plusieurs paramètres à la fois
 * @param array $settings Tableau clé => valeur
 * @return bool
 */
function saveSettings($settings)
{
    $success = true;
    foreach ($settings as $key => $value) {
        if (!saveSetting($key, $value)) {
            $success = false;
        }
    }
    return $success;
}

/**
 * Supprime un paramètre
 * @param string $key
 * @return bool
 */
function deleteSetting($key)
{
    $pdo = getDBConnection();
    if (!$pdo) return false;

    try {
        $sql = "DELETE FROM settings WHERE setting_key = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$key]);
    } catch (PDOException $e) {
        error_log("Erreur deleteSetting: " . $e->getMessage());
        return false;
    }
}
