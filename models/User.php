<?php
namespace Models;

require_once __DIR__ . '/../core/Model.php';

/**
 * Модель пользователя
 */
class User extends \Core\Model {
    protected static $table = 'users';

    /**
     * Найти пользователя по email
     */
    public static function findByEmail($email) {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM " . self::$table . " WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Проверить пароль
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Хешировать пароль
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Создать пользователя
     */
    public static function createUser($data) {
        // Хешируем пароль
        if (isset($data['password'])) {
            $data['password'] = self::hashPassword($data['password']);
        }

        // Устанавливаем значения по умолчанию
        $data = array_merge([
            'name' => '',
            'role' => 'user'
        ], $data);

        return self::create($data);
    }

    /**
     * Обновить пользователя
     */
    public static function updateUser($id, $data) {
        // Хешируем пароль если он предоставлен
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = self::hashPassword($data['password']);
        } elseif (isset($data['password'])) {
            unset($data['password']); // Не обновляем пустой пароль
        }

        return self::update($id, $data);
    }

    /**
     * Проверить, является ли пользователь админом
     */
    public function isAdmin() {
        return isset($this->role) && $this->role === 'admin';
    }

    /**
     * Получить всех пользователей (только для админов)
     */
    public static function getAllUsers() {
        return self::all();
    }

    /**
     * Изменить роль пользователя
     */
    public static function changeUserRole($userId, $role) {
        if (!in_array($role, ['user', 'admin'])) {
            return false;
        }

        return self::update($userId, ['role' => $role]);
    }

    /**
     * Удалить пользователя
     */
    public static function deleteUser($id) {
        return self::delete($id);
    }
}
