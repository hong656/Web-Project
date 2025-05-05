<?php
header("Access-Control-Allow-Origin: https://school-supply-store.vercel.app");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config/Database.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? null;
$price = $_POST['price'] ?? null;
$type = isset($_POST['type']) ? (int) $_POST['type'] : null;

if (!$id || empty($title)) {
    echo json_encode(['status' => false, 'message' => 'ID and title are required']);
    exit;
}

if (!in_array($type, [1, 2, 3])) {
    echo json_encode(['status' => false, 'message' => 'Invalid or missing product type']);
    exit;
}

$existingStmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
$existingStmt->bindParam(':id', $id);
$existingStmt->execute();
$existingProduct = $existingStmt->fetch(PDO::FETCH_ASSOC);

if (!$existingProduct) {
    echo json_encode(['status' => false, 'message' => 'Product not found']);
    exit;
}

$imagePath = $existingProduct['image'];
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = basename($_FILES['image']['name']);
    $targetPath = $uploadDir . uniqid() . '_' . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        if (!empty($imagePath) && file_exists('../' . $imagePath)) {
            unlink('../' . $imagePath);
        }

        $imagePath = str_replace('../', '', $targetPath);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to upload new image']);
        exit;
    }
}

try {
    $stmt = $conn->prepare("
        UPDATE products 
        SET title = :title, description = :description, type = :type , price = :price, image = :image 
        WHERE id = :id
    ");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':image', $imagePath);
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    echo json_encode(['status' => true, 'message' => 'Product updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
