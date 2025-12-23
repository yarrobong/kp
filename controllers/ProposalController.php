<?php
namespace Controllers;

use Models\Product;
use Models\Proposal;
use TCPDF;

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Proposal.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../controllers/AuthController.php';

/**
 * Контроллер коммерческих предложений
 */
class ProposalController extends \Core\Controller {

    public function __construct() {
        // Проверяем авторизацию для создания/редактирования предложений
        $action = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($action, '/proposals/create') !== false ||
            strpos($action, '/proposals/') !== false && strpos($action, '/edit') !== false) {
            AuthController::requireAuth();
        }
    }

    /**
     * Список всех предложений
     */
    public function index() {
        // Только авторизованные пользователи могут видеть предложения
        $user = AuthController::getCurrentUser();
        if (!$user) {
            $this->render('auth/redirect', [
                'title' => 'Требуется авторизация',
                'redirectUrl' => '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']),
                'message' => 'Для просмотра предложений необходимо войти в систему.'
            ]);
            return;
        }

        $proposals = Proposal::getAllWithFallback($user['id']);

        $this->render('proposals/index', [
            'title' => 'Коммерческие предложения - КП Генератор',
            'description' => 'Управление коммерческими предложениями в системе КП Генератор. Создавайте, редактируйте и отслеживайте статус ваших КП.',
            'keywords' => 'коммерческие предложения, КП, управление предложениями, создание КП, статус предложений, PDF экспорт',
            'robots' => 'index, follow',
            'og_type' => 'website',
            'og_title' => 'Коммерческие предложения',
            'og_description' => 'Управление коммерческими предложениями. Создавайте профессиональные КП с автоматическим расчетом сумм.',
            'og_url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'proposals' => $proposals,
            'user' => $user
        ]);
    }

    /**
     * Форма создания предложения
     */
    public function create() {
        $user = AuthController::getCurrentUser();
        $products = Product::getAllWithFallback($user['id']);

        $this->render('proposals/create', [
            'title' => 'Создать коммерческое предложение - КП Генератор',
            'description' => 'Создайте профессиональное коммерческое предложение онлайн. Выберите товары из каталога, укажите количество и автоматически рассчитайте сумму.',
            'keywords' => 'создать КП, новое предложение, коммерческое предложение, генератор КП, расчет суммы, выбор товаров',
            'robots' => 'index, follow',
            'og_type' => 'website',
            'og_title' => 'Создать коммерческое предложение',
            'og_description' => 'Создайте профессиональное коммерческое предложение с автоматическим расчетом суммы и экспортом в PDF.',
            'og_url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'products' => $products,
            'user' => $user
        ]);
    }

    /**
     * Сохранение нового предложения
     */
    public function store() {
        $user = AuthController::getCurrentUser();
        $data = $this->getPostData();

        // Валидация
        if (empty($data['client_name'])) {
            $this->redirect('/proposals/create', 'Имя клиента обязательно', 'error');
            return;
        }

        if (empty($data['offer_date'])) {
            $this->redirect('/proposals/create', 'Дата предложения обязательна', 'error');
            return;
        }

        // Обработка товаров
        $proposalItems = $this->processProposalItems($data);
        if (empty($proposalItems)) {
            $this->redirect('/proposals/create', 'Необходимо выбрать хотя бы один товар', 'error');
            return;
        }

        // Подготовка данных предложения
        $proposalData = [
            'user_id' => $user['id'],
            'title' => 'Коммерческое предложение для ' . $data['client_name'],
            'offer_date' => $data['offer_date'],
            'client_info' => json_encode([
                'client_name' => $data['client_name'],
                'products' => $proposalItems
            ], JSON_UNESCAPED_UNICODE),
            'total' => $this->calculateTotal($proposalItems)
        ];

        $proposalId = Proposal::createProposal($proposalData);

        if ($proposalId) {
            $this->redirect("/proposals/{$proposalId}", 'Предложение успешно создано', 'success');
        } else {
            $this->redirect('/proposals/create', 'Ошибка при создании предложения', 'error');
        }
    }

    /**
     * Просмотр предложения
     */
    public function show($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $user = AuthController::getCurrentUser();
        $proposal = Proposal::findWithFallback($id);
        if (!$proposal) {
            $this->error404('Предложение не найдено');
            return;
        }

        // Проверяем права доступа
        if (!$this->canAccessProposal($proposal, $user)) {
            $this->error403('Доступ к предложению запрещен');
            return;
        }

        // Декодируем информацию о клиенте
        $clientInfo = json_decode($proposal['client_info'], true);

        // Структурированные данные для предложения
        $structuredData = [
            "@type" => "Quotation",
            "name" => $proposal['title'],
            "description" => 'Коммерческое предложение №' . $proposal['offer_number'],
            "dateCreated" => $proposal['created_at'],
            "dateModified" => $proposal['updated_at'],
            "identifier" => $proposal['offer_number'],
            "totalPaymentDue" => [
                "@type" => "MonetaryAmount",
                "value" => $proposal['total'],
                "currency" => "RUB"
            ],
            "customer" => [
                "@type" => "Person",
                "name" => $clientInfo['client_name']
            ],
            "seller" => [
                "@type" => "Organization",
                "name" => "КП Генератор"
            ]
        ];

        $this->render('proposals/show', [
            'title' => $proposal['title'] . ' - КП Генератор',
            'description' => 'Просмотр коммерческого предложения: ' . $proposal['title'] . '. Номер: ' . $proposal['offer_number'] . ', сумма: ' . number_format($proposal['total'], 0, ',', ' ') . ' ₽',
            'keywords' => 'просмотр КП, коммерческое предложение, ' . $proposal['title'] . ', предложение номер ' . $proposal['offer_number'],
            'robots' => 'noindex, nofollow', // Не индексируем индивидуальные предложения
            'og_type' => 'article',
            'og_title' => $proposal['title'],
            'og_description' => 'Коммерческое предложение на сумму ' . number_format($proposal['total'], 0, ',', ' ') . ' ₽',
            'og_url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'structured_data' => $structuredData,
            'proposal' => $proposal,
            'clientInfo' => $clientInfo,
            'user' => $user
        ]);
    }

    /**
     * Форма редактирования предложения
     */
    public function edit($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $user = AuthController::getCurrentUser();
        $proposal = Proposal::findWithFallback($id);
        if (!$proposal) {
            $this->error404('Предложение не найдено');
            return;
        }

        // Проверяем права доступа (владелец или админ)
        if (!$this->canAccessProposal($proposal, $user)) {
            $this->error403('Доступ к предложению запрещен');
            return;
        }

        $products = Product::getAllWithFallback($user['id']);
        $clientInfo = json_decode($proposal['client_info'], true);

        $this->render('proposals/edit', [
            'title' => 'Редактировать предложение',
            'proposal' => $proposal,
            'clientInfo' => $clientInfo,
            'products' => $products,
            'user' => $user
        ]);
    }

    /**
     * Обновление предложения
     */
    public function update($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $user = AuthController::getCurrentUser();
        $proposal = Proposal::findWithFallback($id);
        if (!$proposal) {
            $this->error404('Предложение не найдено');
            return;
        }

        // Проверяем права доступа
        if (!$this->canEditProposal($proposal, $user)) {
            $this->error403('Доступ к редактированию предложения запрещен');
            return;
        }

        $data = $this->getPostData();

        // Валидация
        if (empty($data['client_name'])) {
            $this->redirect("/proposals/{$id}/edit", 'Имя клиента обязательно', 'error');
            return;
        }

        // Обработка товаров
        $proposalItems = $this->processProposalItems($data);

        // Подготовка данных предложения
        $proposalData = [
            'title' => 'Коммерческое предложение для ' . $data['client_name'],
            'offer_date' => $data['offer_date'],
            'client_info' => json_encode([
                'client_name' => $data['client_name'],
                'products' => $proposalItems
            ], JSON_UNESCAPED_UNICODE),
            'total' => $this->calculateTotal($proposalItems)
        ];

        $success = Proposal::updateProposal($id, $proposalData);

        if ($success) {
            $this->redirect("/proposals/{$id}", 'Предложение успешно обновлено', 'success');
        } else {
            $this->redirect("/proposals/{$id}/edit", 'Ошибка при обновлении предложения', 'error');
        }
    }

    /**
     * Удаление предложения
     */
    public function delete($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $user = AuthController::getCurrentUser();
        $proposal = Proposal::findWithFallback($id);
        if (!$proposal) {
            $this->error404('Предложение не найдено');
            return;
        }

        // Проверяем права доступа
        if (!$this->canEditProposal($proposal, $user)) {
            $this->error403('Доступ к удалению предложения запрещен');
            return;
        }

        $success = Proposal::deleteProposal($id);

        if ($success) {
            $this->redirect('/proposals', 'Предложение успешно удалено', 'success');
        } else {
            $this->redirect('/proposals', 'Ошибка при удалении предложения', 'error');
        }
    }

    /**
     * Генерация PDF
     */
    public function pdf($params) {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->error404();
            return;
        }

        $user = AuthController::getCurrentUser();
        $proposal = Proposal::findWithFallback($id);
        if (!$proposal) {
            $this->error404('Предложение не найдено');
            return;
        }

        // Проверяем права доступа
        if (!$this->canAccessProposal($proposal, $user)) {
            $this->error403('Доступ к предложению запрещен');
            return;
        }

        $clientInfo = json_decode($proposal['client_info'], true);

        // Генерация HTML для PDF
        $html = $this->generateProposalHtml($proposal, $clientInfo);

        // Генерация PDF
        $this->generatePdf($html, $proposal['offer_number']);
    }

    /**
     * Обработка товаров из формы
     */
    private function processProposalItems($data) {
        $items = [];

        if (isset($data['proposal_items']) && is_array($data['proposal_items'])) {
            foreach ($data['proposal_items'] as $rowId => $item) {
                // Проверяем, что выбран товар и указано количество
                if (!empty($item['product_id']) && !empty($item['quantity']) && $item['quantity'] > 0) {
                    $product = Product::findWithFallback($item['product_id']);
                    if ($product) {
                        $items[] = [
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'description' => $product['description'],
                            'price' => $product['price'],
                            'quantity' => $item['quantity'],
                            'total' => $product['price'] * $item['quantity']
                        ];
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Расчет общей суммы
     */
    private function calculateTotal($items) {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['total'];
        }
        return $total;
    }

    /**
     * Генерация HTML для предложения
     */
    private function generateProposalHtml($proposal, $clientInfo) {
        ob_start();
        ?>
        <html>
        <head>
            <meta charset="utf-8">
            <title><?php echo htmlspecialchars($proposal['title']); ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .client-info { margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .total { text-align: right; font-weight: bold; font-size: 18px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><?php echo htmlspecialchars($proposal['title']); ?></h1>
                <p>Номер: <?php echo htmlspecialchars($proposal['offer_number']); ?></p>
                <p>Дата: <?php echo htmlspecialchars($proposal['offer_date']); ?></p>
            </div>

            <div class="client-info">
                <h2>Информация о клиенте</h2>
                <p><strong>Клиент:</strong> <?php echo htmlspecialchars($clientInfo['client_name']); ?></p>
            </div>

            <h2>Состав предложения</h2>
            <table>
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientInfo['products'] as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><?php echo number_format($product['total'], 0, ',', ' '); ?> ₽</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total">
                Итого: <?php echo number_format($proposal['total'], 0, ',', ' '); ?> ₽
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Генерация PDF файла
     */
    private function generatePdf($html, $filename) {
        require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

        // Используем строковые значения вместо констант
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('КП Генератор');
        $pdf->SetAuthor('КП Генератор');
        $pdf->SetTitle($filename);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($filename . '.pdf', 'D');
        exit;
    }

    /**
     * Проверить права доступа к предложению
     */
    private function canAccessProposal($proposal, $user) {
        if (!$user) return false;

        // Админ может видеть все предложения
        if ($user['role'] === 'admin') {
            return true;
        }

        // Пользователь может видеть только свои предложения
        return $proposal['user_id'] == $user['id'];
    }

    /**
     * Проверить права редактирования предложения
     */
    private function canEditProposal($proposal, $user) {
        if (!$user) return false;

        // Админ может редактировать все предложения
        if ($user['role'] === 'admin') {
            return true;
        }

        // Пользователь может редактировать только свои предложения
        return $proposal['user_id'] == $user['id'];
    }
}
