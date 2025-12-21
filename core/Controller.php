<?php
namespace Core;

/**
 * Базовый класс контроллера
 * Предоставляет общую функциональность для всех контроллеров
 */
abstract class Controller {
    /**
     * Рендеринг представления
     */
    protected function render($view, $data = []) {
        // Извлекаем данные в переменные
        extract($data);

        // Определяем путь к представлению
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewPath)) {
            // Начинаем буферизацию вывода
            ob_start();
            include $viewPath;
            $content = ob_get_clean();

            // Если это не AJAX запрос, оборачиваем в layout
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
                $layoutPath = __DIR__ . '/../views/layouts/main.php';
                if (file_exists($layoutPath)) {
                    $title = $data['title'] ?? 'КП Генератор';
                    include $layoutPath;
                } else {
                    echo $content;
                }
            } else {
                echo $content;
            }
        } else {
            $this->error404('Представление не найдено: ' . $view);
        }
    }

    /**
     * Перенаправление
     */
    protected function redirect($url, $message = '', $type = 'success') {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $type;
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * JSON ответ
     */
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Получение POST данных
     */
    protected function getPostData() {
        return $_POST;
    }

    /**
     * Получение GET данных
     */
    protected function getQueryData() {
        return $_GET;
    }

    /**
     * Проверка CSRF токена (можно добавить позже)
     */
    protected function validateCsrf() {
        return true;
    }

    /**
     * Обработка ошибок 404
     */
    protected function error404($message = 'Страница не найдена') {
        http_response_code(404);
        $this->render('errors/404', [
            'title' => '404 - Страница не найдена',
            'message' => $message
        ]);
    }

    /**
     * Обработка ошибок 403
     */
    protected function error403($message = 'Доступ запрещен') {
        http_response_code(403);
        $this->render('errors/403', [
            'title' => '403 - Доступ запрещен',
            'message' => $message
        ]);
    }

    /**
     * Обработка ошибок 500
     */
    protected function error500($message = 'Внутренняя ошибка сервера') {
        http_response_code(500);
        $this->render('errors/500', [
            'title' => '500 - Ошибка сервера',
            'message' => $message
        ]);
    }
}
