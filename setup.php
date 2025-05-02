<?php
require_once './config/Database.php';

$db = new Database();
$conn = $db->connect();

// Drop the table if it exists
$dropSql = "DROP TABLE IF EXISTS students";

// Recreate the students table
$createSql = "
    CREATE TABLE students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        year TINYINT NOT NULL DEFAULT 1 COMMENT '1: Year1, 2: Year2, 3: Year3, 4: Year4',
        address VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL
    )
";

try {
    $conn->exec($dropSql);
    $conn->exec($createSql);
    echo "Table 'students' dropped and recreated successfully.";
} catch (PDOException $e) {
    echo "Error recreating table: " . $e->getMessage();
}
