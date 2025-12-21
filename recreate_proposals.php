<?php
include 'index.php';

$db = getDB();
if ($db) {
    try {
        $db->exec('DROP TABLE IF EXISTS proposals');
        echo "Таблица proposals удалена\n";

        $db->exec("CREATE TABLE proposals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL DEFAULT 1,
            template_id INT NULL,
            title VARCHAR(255) NOT NULL,
            offer_number VARCHAR(50) NOT NULL,
            offer_date DATE NOT NULL,
            client_info TEXT,
            status ENUM('draft', 'sent', 'accepted', 'rejected') DEFAULT 'draft',
            total DECIMAL(10,2) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        echo "Таблица proposals создана заново\n";
    } catch (Exception $e) {
        echo "Ошибка: " . $e->getMessage() . "\n";
    }
}
