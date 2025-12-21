<?php

use App\Database\Database;

class CreateProductsTable
{
    public function up()
    {
        $db = new Database();

        $sql = "CREATE TABLE `products` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL,
            `price` decimal(10,2) NOT NULL,
            `photo` varchar(255) DEFAULT NULL,
            `description` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `products_user_id_foreign` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->query($sql);
    }

    public function down()
    {
        $db = new Database();
        $db->query("DROP TABLE IF EXISTS `products`");
    }
}
