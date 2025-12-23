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
            'title' => 'КП Генератор - Создание коммерческих предложений онлайн',
            'description' => 'КП Генератор - современная система для автоматизации создания профессиональных коммерческих предложений. Управляйте каталогом товаров, формируйте КП с автоматическим расчетом сумм и экспортируйте в PDF формат.',
            'keywords' => 'КП генератор, коммерческое предложение, создание КП онлайн, автоматизация продаж, генератор предложений, PDF предложения, управление товарами, CRM система, бизнес предложения',
            'robots' => 'index, follow',
            'og_type' => 'website',
            'og_title' => 'КП Генератор - Автоматизация коммерческих предложений',
            'og_description' => 'Создавайте профессиональные коммерческие предложения за минуты. Управляйте товарами, рассчитывайте суммы автоматически, экспортируйте в PDF.',
            'og_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/',
            'og_image' => '/css/placeholder-product.svg',
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
