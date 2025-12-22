<?php
namespace Controllers;

use Models\User;
use Models\Product;
use Models\Proposal;

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Proposal.php';

/**
 * Контроллер админ панели
 */
class AdminController extends \Core\Controller {

    public function __construct() {
        // Проверяем авторизацию и права админа
        AuthController::requireAdmin();
    }

    /**
     * Главная страница админ панели
     */
    public function index() {
        // Получаем статистику
        $stats = $this->getSystemStats();

        $this->render('admin/index', [
            'title' => 'Админ панель',
            'stats' => $stats
        ]);
    }

    /**
     * Управление пользователями
     */
    public function users() {
        $users = User::getAllUsers();

        $this->render('admin/users', [
            'title' => 'Управление пользователями',
            'users' => $users
        ]);
    }

    /**
     * Изменить роль пользователя
     */
    public function changeUserRole($params) {
        $userId = $params['id'] ?? null;
        $data = $this->getPostData();

        if (!$userId || !isset($data['role'])) {
            $this->redirect('/admin/users', 'Неверные параметры', 'error');
            return;
        }

        $success = User::changeUserRole($userId, $data['role']);

        if ($success) {
            $this->redirect('/admin/users', 'Роль пользователя успешно изменена', 'success');
        } else {
            $this->redirect('/admin/users', 'Ошибка при изменении роли', 'error');
        }
    }

    /**
     * Удалить пользователя
     */
    public function deleteUser($params) {
        $userId = $params['id'] ?? null;

        if (!$userId) {
            $this->redirect('/admin/users', 'Пользователь не найден', 'error');
            return;
        }

        // Нельзя удалить самого себя
        if ($userId == $_SESSION['user_id']) {
            $this->redirect('/admin/users', 'Нельзя удалить самого себя', 'error');
            return;
        }

        $success = User::deleteUser($userId);

        if ($success) {
            $this->redirect('/admin/users', 'Пользователь успешно удален', 'success');
        } else {
            $this->redirect('/admin/users', 'Ошибка при удалении пользователя', 'error');
        }
    }

    /**
     * Просмотр товаров пользователя
     */
    public function userProducts($params) {
        $userId = $params['id'] ?? null;

        if (!$userId) {
            $this->error404('Пользователь не найден');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error404('Пользователь не найден');
            return;
        }

        $products = Product::getAll($userId);

        $this->render('admin/user-products', [
            'title' => 'Товары пользователя: ' . $user['name'],
            'user' => $user,
            'products' => $products
        ]);
    }

    /**
     * Просмотр предложений пользователя
     */
    public function userProposals($params) {
        $userId = $params['id'] ?? null;

        if (!$userId) {
            $this->error404('Пользователь не найден');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error404('Пользователь не найден');
            return;
        }

        $proposals = Proposal::getAll($userId);

        $this->render('admin/user-proposals', [
            'title' => 'Предложения пользователя: ' . $user['name'],
            'user' => $user,
            'proposals' => $proposals
        ]);
    }

    /**
     * Получить системную статистику
     */
    private function getSystemStats() {
        $db = User::getDB();
        if (!$db) return [];

        try {
            $stats = [];

            // Статистика пользователей
            $stmt = $db->query("SELECT COUNT(*) as total FROM users");
            $stats['users'] = $stmt->fetch()['total'];

            $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
            $stats['admins'] = $stmt->fetch()['total'];

            // Статистика товаров
            $stmt = $db->query("SELECT COUNT(*) as total FROM products");
            $stats['products'] = $stmt->fetch()['total'];

            // Статистика предложений
            $stmt = $db->query("SELECT COUNT(*) as total FROM proposals");
            $stats['proposals'] = $stmt->fetch()['total'];

            // Предложения по статусам
            $stmt = $db->query("SELECT status, COUNT(*) as count FROM proposals GROUP BY status");
            $stats['proposals_by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            return $stats;
        } catch (Exception $e) {
            return [];
        }
    }
}
