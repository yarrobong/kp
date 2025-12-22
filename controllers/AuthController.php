<?php
namespace Controllers;

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

/**
 * Контроллер аутентификации
 */
class AuthController extends \Core\Controller {

    public function __construct() {
        // Запускаем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Показать форму входа
     */
    public function login() {
        // Если пользователь уже авторизован, перенаправляем
        if ($this->isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        $this->render('auth/login', [
            'title' => 'Вход в систему'
        ]);
    }

    /**
     * Обработка входа
     */
    public function authenticate() {
        $data = $_POST;
        error_log("Auth authenticate called, raw POST data: " . json_encode($data));
        error_log("Auth authenticate called, getPostData: " . json_encode($this->getPostData()));

        // Валидация
        if (empty($data['email']) || empty($data['password'])) {
            error_log("Auth validation failed: missing email or password");
            $this->redirect('/login', 'Заполните все поля', 'error');
            return;
        }

        // Поиск пользователя
        $user = User::findByEmail($data['email']);
        error_log("Auth login: email=" . $data['email'] . ", user=" . ($user ? 'found' : 'not found'));

        if (!$user || empty($user['password'])) {
            error_log("Auth failed: user not found or no password");
            $this->redirect('/login', 'Неверный email или пароль', 'error');
            return;
        }

        if (!User::verifyPassword($data['password'], $user['password'])) {
            error_log("Auth failed: password verification failed");
            $this->redirect('/login', 'Неверный email или пароль', 'error');
            return;
        }

        // Сохраняем данные пользователя в сессии
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // Возвращаем успешный JSON ответ вместо редиректа
        $this->json([
            'success' => true,
            'message' => 'Добро пожаловать, ' . $user['name'] . '!',
            'redirect' => $_GET['redirect'] ?? '/'
        ]);
    }

    /**
     * Показать форму регистрации
     */
    public function register() {
        // Если пользователь уже авторизован, перенаправляем
        if ($this->isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        $this->render('auth/register', [
            'title' => 'Регистрация'
        ]);
    }

    /**
     * Обработка регистрации
     */
    public function store() {
        $data = $this->getPostData();

        // Валидация
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $this->redirect('/register', 'Заполните все поля', 'error');
            return;
        }

        if ($data['password'] !== $data['password_confirmation']) {
            $this->redirect('/register', 'Пароли не совпадают', 'error');
            return;
        }

        if (strlen($data['password']) < 6) {
            $this->redirect('/register', 'Пароль должен содержать минимум 6 символов', 'error');
            return;
        }

        // Проверяем, существует ли пользователь с таким email
        if (User::findByEmail($data['email'])) {
            $this->redirect('/register', 'Пользователь с таким email уже существует', 'error');
            return;
        }

        // Создаем пользователя
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ];

        $userId = User::createUser($userData);

        if ($userId) {
            // Автоматически авторизуем пользователя
            $user = User::find($userId);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            $this->redirect('/', 'Регистрация прошла успешно!', 'success');
        } else {
            $this->redirect('/register', 'Ошибка при регистрации', 'error');
        }
    }

    /**
     * Выход из системы
     */
    public function logout() {
        session_destroy();
        $this->redirect('/', 'Вы успешно вышли из системы', 'success');
    }

    /**
     * Проверить, авторизован ли пользователь
     */
    private function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Получить текущего пользователя
     */
    public static function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }

    /**
     * Проверить, является ли текущий пользователь админом
     */
    public static function isCurrentUserAdmin() {
        $user = self::getCurrentUser();
        return $user && $user['role'] === 'admin';
    }

    /**
     * Middleware для проверки авторизации
     */
    public static function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Middleware для проверки прав админа
     */
    public static function requireAdmin() {
        self::requireAuth();

        if (!self::isCurrentUserAdmin()) {
            http_response_code(403);
            echo 'Доступ запрещен';
            exit;
        }
    }
}
