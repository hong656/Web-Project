<?php
// CORS headers
header('Access-Control-Allow-Origin: http://localhost:3000'); // adjust if needed
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->connect();

    $data = json_decode(file_get_contents("php://input"));

    $name = trim($data->name ?? '');
    $address = trim($data->address ?? '');
    $email = trim($data->email ?? '');
    $password = $data->password ?? '';

    if (empty($name) || empty($address) || empty($email) || empty($password)) {
        echo json_encode(['status' => false, 'message' => 'All fields are required']);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM students WHERE email = :email");
    $check->bindParam(':email', $email);
    $check->execute();

    if ($check->rowCount() > 0) {
        echo json_encode(['status' => false, 'message' => 'Email already exists']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO students (name, address, email, password) VALUES (:name, :address, :email, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(['status' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Registration failed']);
    }

} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
}
