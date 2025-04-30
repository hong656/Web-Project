<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/Database.php';
include_once '../models/Student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new Database();
    $conn = $db->connect();

    $data = json_decode(file_get_contents("php://input"));

    $email = $data->email ?? '';
    $password = $data->password ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['message' => 'Email and password are required']);
        exit;
    }

    // Prepare statement
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (password_verify($password, $user['password'])) {
            echo json_encode([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode(['message' => 'Incorrect password']);
        }
    } else {
        echo json_encode(['message' => 'User not found']);
    }

} else {
    echo json_encode(['message' => 'Invalid request method']);
}
?>
