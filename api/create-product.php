<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config/Database.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required fields
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? null;
$price = $_POST['price'] ?? null;

if (empty($title)) {
    echo json_encode(['status' => false, 'message' => 'Title is required']);
    exit;
}

// Handle file upload if exists
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = basename($_FILES['image']['name']);
    $targetPath = $uploadDir . uniqid() . '_' . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = str_replace('../', '', $targetPath); // store relative path
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to upload image']);
        exit;
    }
}

// Insert into DB
try {
    $stmt = $conn->prepare("INSERT INTO products (title, description, price, image) VALUES (:title, :description, :price, :image)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':image', $imagePath);

    $stmt->execute();

    echo json_encode(['status' => true, 'message' => 'Product created successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
