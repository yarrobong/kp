<?php
namespace Controllers;

require_once __DIR__ . '/../core/Controller.php';

/**
 * Контроллер главной страницы
 */
class HomeController extends \Core\Controller {

    /**
     * Главная страница
     */
    public function index() {
        $user = AuthController::getCurrentUser();

        $this->render('home/index', [
            'title' => 'КП Генератор - Создание коммерческих предложений',
            'user' => $user
        ]);
    }

    /**
     * Health check
     */
    public function health() {
        $health = [
            'status' => 'ok',
            'timestamp' => date('c'),
            'version' => '1.0.0',
            'php' => PHP_VERSION,
            'database' => $this->checkDatabaseConnection(),
            'files' => [
                'products.json' => file_exists(__DIR__ . '/../products.json'),
                'proposals.json' => file_exists(__DIR__ . '/../proposals.json'),
            ]
        ];

        $this->json($health);
    }

    /**
     * Проверка подключения к базе данных
     */
    private function checkDatabaseConnection() {
        try {
            $db = new \PDO('mysql:host=localhost;dbname=commercial_proposals;charset=utf8', 'appuser', 'apppassword');
            $stmt = $db->query('SELECT COUNT(*) as count FROM proposals');
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return 'connected (proposals: ' . $result['count'] . ')';
        } catch (\Exception $e) {
            return 'fallback_to_json: ' . $e->getMessage();
        }
    }
}
