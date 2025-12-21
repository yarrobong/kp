<?php
namespace Models;

require_once __DIR__ . '/../core/Model.php';

/**
 * Модель коммерческого предложения
 */
class Proposal extends \Core\Model {
    protected static $table = 'proposals';

    /**
     * Получить все предложения
     */
    public static function getAll($userId = null) {
        $conditions = [];
        if ($userId) {
            $conditions['user_id'] = $userId;
        }
        return self::all($conditions);
    }

    /**
     * Получить статистику предложений пользователя
     */
    public static function getUserStats($userId) {
        $db = self::getDB();
        if (!$db) return ['total' => 0, 'by_status' => []];

        try {
            // Общее количество предложений
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM " . self::$table . " WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetch()['total'];

            // Статистика по статусам
            $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM " . self::$table . " WHERE user_id = ? GROUP BY status");
            $stmt->execute([$userId]);
            $byStatus = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            return [
                'total' => $total,
                'by_status' => $byStatus
            ];
        } catch (Exception $e) {
            return ['total' => 0, 'by_status' => []];
        }
    }

    /**
     * Получить предложения по статусу
     */
    public static function getByStatus($status, $userId = null) {
        $conditions = ['status' => $status];
        if ($userId) {
            $conditions['user_id'] = $userId;
        }
        return self::all($conditions);
    }

    /**
     * Создать предложение
     */
    public static function createProposal($data) {
        // Устанавливаем значения по умолчанию
        $data = array_merge([
            'user_id' => 1,
            'template_id' => null,
            'status' => 'draft',
            'total' => 0
        ], $data);

        // Генерируем номер предложения если не указан
        if (!isset($data['offer_number']) || empty($data['offer_number'])) {
            $data['offer_number'] = self::generateOfferNumber();
        }

        return self::create($data);
    }

    /**
     * Обновить предложение
     */
    public static function updateProposal($id, $data) {
        return self::update($id, $data);
    }

    /**
     * Удалить предложение
     */
    public static function deleteProposal($id) {
        return self::delete($id);
    }

    /**
     * Генерация номера предложения
     */
    private static function generateOfferNumber() {
        $date = date('Ymd');
        $db = self::getDB();
        if (!$db) return "KP-{$date}-001";

        try {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM " . self::$table . " WHERE DATE(created_at) = CURDATE()");
            $stmt->execute();
            $result = $stmt->fetch();
            $count = $result['count'] + 1;
            return "KP-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            return "KP-{$date}-001";
        }
    }

    /**
     * Получить предложение с fallback на JSON
     */
    public static function findWithFallback($id) {
        $proposal = self::find($id);
        if ($proposal) {
            return $proposal;
        }

        // Fallback to JSON
        $dataFile = __DIR__ . '/../proposals.json';
        if (file_exists($dataFile)) {
            $proposals = json_decode(file_get_contents($dataFile), true);
            if (is_array($proposals)) {
                foreach ($proposals as $proposal) {
                    if ($proposal['id'] == $id) {
                        return $proposal;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Получить все предложения с fallback на JSON
     */
    public static function getAllWithFallback($userId = null) {
        $proposals = self::getAll($userId);
        if (!empty($proposals)) {
            return $proposals;
        }

        // Fallback to JSON
        $dataFile = __DIR__ . '/../proposals.json';
        if (file_exists($dataFile)) {
            $proposals = json_decode(file_get_contents($dataFile), true);
            if (is_array($proposals)) {
                return $proposals;
            }
        }

        return [];
    }
}
