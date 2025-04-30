<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once '../config/Database.php';
include_once '../models/Student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new Database();
    $db = $db->connect();

    $student = new Student($db);

    // Decode the incoming JSON request body
    $data = json_decode(file_get_contents("php://input"));

    // Assign data to the student object
    $student->name = $data->name;
    $student->address = $data->address;
    $student->age = $data->age;
    $student->email = $data->email;  // New field
    $student->password = password_hash($data->password, PASSWORD_DEFAULT);  // Hash the password

    if ($student->postData()) {
        echo json_encode(array('message' => 'Student added'));
    } else {
        echo json_encode(array('message' => 'Student Not added, try again!'));
    }
} else {
    echo json_encode(array('message' => "Error: incorrect Method!"));
}
?>
