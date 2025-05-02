<?php
// Handle CORS and preflight requests
header('Access-Control-Allow-Origin: http://localhost:3000'); // or '*', but localhost:3000 is safer for dev
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Respond to OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../config/Database.php';
include_once '../models/Student.php';

function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new Database();
    $conn = $db->connect();

    $data = json_decode(file_get_contents("php://input"));

    $email = $data->email ?? '';
    $password = $data->password ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => false, 'message' => 'Email and password are required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM students WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            $token = generateToken();

            echo json_encode([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Incorrect password']);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'User not found']);
    }

} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
}
