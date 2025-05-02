<?php
require_once './config/Database.php';

$db = new Database();
$conn = $db->connect();

$sql = "
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        price DECIMAL(10, 2) DEFAULT NULL,
        image VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
";

try {
    $conn->exec($sql);
    echo "Table 'products' created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
