<?php
require_once './config/Database.php';

$db = new Database();
$conn = $db->connect();

// Create the table with email and password columns
$sql = "
    CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        address VARCHAR(255) NOT NULL,
        age INT NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )
";

try {
    $conn->exec($sql);
    echo "Table 'students' created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}

// Insert sample data including email and password
$sampleData = [
    ['admin', '123 Main St', 20, 'admin.com', 'admin123'],
];

try {
    $stmt = $conn->prepare("INSERT INTO students (name, address, age, email, password) VALUES (?, ?, ?, ?, ?)");

    foreach ($sampleData as $student) {
        $stmt->execute($student);
    }

    echo "<br>Sample student data inserted successfully.";
} catch (PDOException $e) {
    echo "<br>Error inserting sample data: " . $e->getMessage();
}
?>