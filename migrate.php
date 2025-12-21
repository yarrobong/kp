<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Database\Database;

// Создание таблиц напрямую через SQL
$db = Database::connection();

$tables = [
    // Users table
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        email_verified_at TIMESTAMP NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('guest', 'user', 'admin') DEFAULT 'user',
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Password reset tokens table
    "CREATE TABLE IF NOT EXISTS password_reset_tokens (
        email VARCHAR(255) PRIMARY KEY,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // Templates table
    "CREATE TABLE IF NOT EXISTS templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        name VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        body_html TEXT NOT NULL,
        is_system BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",

    // Proposals table
    "CREATE TABLE IF NOT EXISTS proposals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        template_id INT NULL,
        title VARCHAR(255) NOT NULL,
        offer_number VARCHAR(50) NOT NULL,
        offer_date DATE NOT NULL,
        client_info TEXT,
        status ENUM('draft', 'sent', 'accepted', 'rejected') DEFAULT 'draft',
        total DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE SET NULL
    )",

    // Proposal files table
    "CREATE TABLE IF NOT EXISTS proposal_files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        proposal_id INT NOT NULL,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        path VARCHAR(500) NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        size INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE CASCADE
    )",

    // Personal access tokens table
    "CREATE TABLE IF NOT EXISTS personal_access_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tokenable_type VARCHAR(255) NOT NULL,
        tokenable_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        token VARCHAR(64) UNIQUE NOT NULL,
        abilities TEXT NULL,
        last_used_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Products table
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        price DECIMAL(10,2) NOT NULL,
        category VARCHAR(100) NULL,
        image VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $tableName => $sql) {
    echo "Creating table " . ($tableName + 1) . "...\n";
    try {
        $db->exec($sql);
        echo "Table created successfully\n";
    } catch (Exception $e) {
        echo "Error creating table: " . $e->getMessage() . "\n";
    }
}

echo "All tables created!\n";

// Создаем админа, если его нет
try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@example.com']);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $password = password_hash('password', PASSWORD_DEFAULT);
        $stmt->execute(['Администратор', 'admin@example.com', $password, 'admin']);
        echo "Admin user created: admin@example.com / password\n";
    } else {
        echo "Admin user already exists\n";
    }
} catch (Exception $e) {
    echo "Error creating admin user: " . $e->getMessage() . "\n";
}
