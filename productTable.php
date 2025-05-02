<?php
require_once './config/Database.php';

$db = new Database();
$conn = $db->connect();

$dropSql = "DROP TABLE IF EXISTS products";

$createSql = "
    CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        price DECIMAL(10, 2) DEFAULT NULL,
        image VARCHAR(255) DEFAULT NULL,
        type TINYINT NOT NULL DEFAULT 1 COMMENT '1: Writing Tool, 2: Mathematical Tool, 3: Paper Product',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
";

try {
    $conn->exec($dropSql);
    $conn->exec($createSql);
    echo "Table 'products' dropped and recreated successfully.";
} catch (PDOException $e) {
    echo "Error processing table: " . $e->getMessage();
}
