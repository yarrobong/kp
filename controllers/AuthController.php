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
        try {
            // PHP автоматически заполняет $_POST для multipart/form-data
            $data = $_POST;

        // Валидация
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing email or password']);
            return;
        }

        // Прямой запрос к базе данных для диагностики
        try {
            $db = new \PDO('mysql:host=localhost;dbname=commercial_proposals;charset=utf8', 'appuser', 'apppassword');
            $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(401);
                echo json_encode(['error' => 'Пользователь не найден']);
                return;
            }

            if (!password_verify($data['password'], $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Неверный пароль']);
                return;
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
            return;
        }

        // Сохраняем данные пользователя в сессии
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // Возвращаем успешный ответ
        echo json_encode([
            'success' => true,
            'message' => 'Добро пожаловать, ' . $user['name'] . '!',
            'redirect' => $_GET['redirect'] ?? '/'
        ]);

        } catch (\Exception $e) {
            echo "FATAL ERROR: " . $e->getMessage() . "\n";
        }
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
            http_response_code(400);
            echo json_encode(['error' => 'Заполните все поля']);
            return;
        }

        if ($data['password'] !== $data['password_confirmation']) {
            http_response_code(400);
            echo json_encode(['error' => 'Пароли не совпадают']);
            return;
        }

        if (strlen($data['password']) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'Пароль должен содержать минимум 6 символов']);
            return;
        }

        // Проверяем, существует ли пользователь с таким email
        if (User::findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Пользователь с таким email уже существует']);
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

            echo json_encode([
                'success' => true,
                'message' => 'Регистрация прошла успешно!',
                'redirect' => '/'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка при регистрации']);
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
