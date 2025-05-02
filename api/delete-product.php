<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
    exit;
}

// Read raw input for DELETE method
$input = json_decode(file_get_contents("php://input"), true);
$id = $input['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => false, 'message' => 'Product ID is required']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();

    // Optionally delete image file (optional)
    $stmtImage = $conn->prepare("SELECT image FROM products WHERE id = :id");
    $stmtImage->bindParam(":id", $id);
    $stmtImage->execute();
    $product = $stmtImage->fetch(PDO::FETCH_ASSOC);
    if ($product && !empty($product['image'])) {
        $imagePath = '../' . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete product from database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    echo json_encode(['status' => true, 'message' => 'Product deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
