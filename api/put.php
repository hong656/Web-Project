<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');

    include_once '../config/Database.php';
    include_once '../models/Student.php';

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

        $db = new Database();
        $db = $db->connect();

        $student = new Student($db);

        $data = json_decode(file_get_contents("php://input"));

        $student->id = isset($data->id) ? $data->id : null;
        $student->name = $data->name;
        $student->address = $data->address;
        $student->email = $data->email;

        // Only hash password if it's provided
        if (!empty($data->password)) {
            $student->password = password_hash($data->password, PASSWORD_DEFAULT);
        } else {
            echo json_encode(['message' => 'Password is required for update.']);
            exit;
        }

        if (!is_null($student->id)) {
            if ($student->putData()) {
                echo json_encode(['message' => 'Student updated']);
            } else {
                echo json_encode(['message' => 'Student Not updated, try again!']);
            }
        } else {
            echo json_encode(['message' => 'Error: Student ID is missing!']);
        }

    } else {
        echo json_encode(['message' => 'Error: incorrect Method!']);
    }
?>
