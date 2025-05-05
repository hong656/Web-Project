<?php
header('Access-Control-Allow-Origin: https://school-supply-store.vercel.app');
header('Content-Type: application/json');

require_once '../config/Database.php';

$db = new Database();
$conn = $db->connect();

try {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id ASC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepend base URL to image if present
    $baseURL = 'http://localhost:8000/';
    foreach ($products as &$product) {
        if (!empty($product['image'])) {
            $product['image'] = $baseURL . $product['image'];
        }
    }

    echo json_encode([
        'status' => true,
        'products' => $products
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
