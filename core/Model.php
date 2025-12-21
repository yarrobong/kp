<?php
/**
 * Базовый класс модели
 * Предоставляет базовую функциональность для работы с базой данных
 */
abstract class Model {
    protected static $db = null;
    protected static $table;

    /**
     * Получение соединения с базой данных
     */
    protected static function getDB() {
        if (self::$db === null) {
            try {
                $configPath = dirname(__DIR__) . '/config/database.php';
                if (!file_exists($configPath)) {
                    throw new Exception("Config file not found: $configPath");
                }
                $config = require $configPath;
                $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
                self::$db = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                // Fallback to JSON if database connection fails
                self::$db = false;
                error_log("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$db;
    }

    /**
     * Найти запись по ID
     */
    public static function find($id) {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Получить все записи
     */
    public static function all($conditions = []) {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $query = "SELECT * FROM " . static::$table;
            $params = [];

            if (!empty($conditions)) {
                $where = [];
                foreach ($conditions as $key => $value) {
                    $where[] = "$key = ?";
                    $params[] = $value;
                }
                $query .= " WHERE " . implode(" AND ", $where);
            }

            $query .= " ORDER BY created_at DESC";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Создать новую запись
     */
    public static function create($data) {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $columns = array_keys($data);
            $placeholders = str_repeat('?,', count($columns) - 1) . '?';

            $query = "INSERT INTO " . static::$table . " (" . implode(',', $columns) . ") VALUES ($placeholders)";
            $stmt = $db->prepare($query);
            $stmt->execute(array_values($data));

            return $db->lastInsertId();
        } catch (Exception $e) {
            error_log("Create failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Обновить запись
     */
    public static function update($id, $data) {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $set = [];
            $params = [];
            foreach ($data as $key => $value) {
                $set[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $id;

            $query = "UPDATE " . static::$table . " SET " . implode(',', $set) . " WHERE id = ?";
            $stmt = $db->prepare($query);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Удалить запись
     */
    public static function delete($id) {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Delete failed: " . $e->getMessage());
            return false;
        }
    }
}
