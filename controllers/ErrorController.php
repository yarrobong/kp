<?php
namespace Controllers;

require_once __DIR__ . '/../core/Controller.php';

/**
 * Контроллер для обработки ошибок
 */
class ErrorController extends \Core\Controller {

    /**
     * Обработка ошибки 404
     */
    public function error404($message = 'Страница не найдена') {
        http_response_code(404);
        
        // Получаем текущего пользователя для отображения соответствующих ссылок
        require_once __DIR__ . '/AuthController.php';
        $user = \Controllers\AuthController::getCurrentUser();
        
        $this->render('errors/404', [
            'title' => '404 - Страница не найдена | КП Генератор',
            'description' => 'Запрашиваемая страница не найдена. Вернитесь на главную страницу или воспользуйтесь поиском товаров.',
            'keywords' => '404, страница не найдена, ошибка, поиск товаров, КП генератор',
            'robots' => 'noindex, nofollow',
            'og_type' => 'website',
            'og_title' => '404 - Страница не найдена',
            'og_description' => 'Запрашиваемая страница не существует. Вернитесь на главную страницу.',
            'og_url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'message' => $message,
            'user' => $user
        ]);
    }

    /**
     * Обработка ошибки 403
     */
    public function error403($message = 'Доступ запрещен') {
        http_response_code(403);
        $this->render('errors/403', [
            'title' => '403 - Доступ запрещен | КП Генератор',
            'description' => 'У вас нет доступа к этой странице.',
            'robots' => 'noindex, nofollow',
            'message' => $message
        ]);
    }

    /**
     * Обработка ошибки 500
     */
    public function error500($message = 'Внутренняя ошибка сервера') {
        http_response_code(500);
        $this->render('errors/500', [
            'title' => '500 - Ошибка сервера | КП Генератор',
            'description' => 'Произошла внутренняя ошибка сервера.',
            'robots' => 'noindex, nofollow',
            'message' => $message
        ]);
    }
}
