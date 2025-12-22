<?php
namespace Controllers;

use Models\Product;
use Models\Proposal;
use Models\User;

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Proposal.php';

/**
 * Контроллер личного кабинета
 */
class UserController extends \Core\Controller {

    public function __construct() {
        // Проверяем авторизацию
        AuthController::requireAuth();
    }

    /**
     * Личный кабинет пользователя
     */
    public function index() {
        $user = AuthController::getCurrentUser();
        $userId = $user['id'];

        // Получаем статистику пользователя
        $stats = [
            'products' => Product::getUserStats($userId),
            'proposals' => Proposal::getUserStats($userId)
        ];

        // Получаем последние товары и предложения
        $recentProducts = array_slice(Product::getAll($userId), 0, 5);
        $recentProposals = array_slice(Proposal::getAll($userId), 0, 5);

        $this->render('user/index', [
            'title' => 'Личный кабинет',
            'user' => $user,
            'stats' => $stats,
            'recentProducts' => $recentProducts,
            'recentProposals' => $recentProposals
        ]);
    }

    /**
     * Форма редактирования профиля
     */
    public function edit() {
        $user = AuthController::getCurrentUser();

        $this->render('user/edit', [
            'title' => 'Редактирование профиля',
            'user' => $user
        ]);
    }

    /**
     * Обновление профиля
     */
    public function update() {
        $user = AuthController::getCurrentUser();
        $data = $this->getPostData();

        // Валидация
        if (empty($data['name']) || empty($data['email'])) {
            $this->redirect('/user/edit', 'Заполните обязательные поля', 'error');
            return;
        }

        // Проверяем email на уникальность (если изменился)
        if ($data['email'] !== $user['email']) {
            $existingUser = User::findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $user['id']) {
                $this->redirect('/user/edit', 'Пользователь с таким email уже существует', 'error');
                return;
            }
        }

        // Проверяем пароль (если указан)
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $this->redirect('/user/edit', 'Пароль должен содержать минимум 6 символов', 'error');
                return;
            }

            if ($data['password'] !== $data['password_confirmation']) {
                $this->redirect('/user/edit', 'Пароли не совпадают', 'error');
                return;
            }
        }

        // Обновляем данные
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email']
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $success = User::updateUser($user['id'], $updateData);

        if ($success) {
            // Обновляем сессию
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];

            $this->redirect('/user', 'Профиль успешно обновлен', 'success');
        } else {
            $this->redirect('/user/edit', 'Ошибка при обновлении профиля', 'error');
        }
    }

    /**
     * Мои товары
     */
    public function products() {
        $user = AuthController::getCurrentUser();
        $products = Product::getAll($user['id']);

        $this->render('user/products', [
            'title' => 'Мои товары',
            'user' => $user,
            'products' => $products
        ]);
    }

    /**
     * Мои предложения
     */
    public function proposals() {
        $user = AuthController::getCurrentUser();
        $proposals = Proposal::getAll($user['id']);

        $this->render('user/proposals', [
            'title' => 'Мои предложения',
            'user' => $user,
            'proposals' => $proposals
        ]);
    }
}
