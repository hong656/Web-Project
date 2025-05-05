<?php
header('Access-Control-Allow-Origin: https://school-supply-store.vercel.app');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');

include_once '../config/Database.php';
include_once '../models/Student.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $db = new Database();
    $db = $db->connect();

    $student = new Student($db);

    $data = json_decode(file_get_contents("php://input"));

    $student->id = isset($data->id) ? $data->id : null;

    if (!is_null($student->id)) {
        if ($student->delete()) {
            echo json_encode(['message' => 'Student deleted']);
        } else {
            echo json_encode(['message' => 'Student not deleted, try again!']);
        }
    } else {
        echo json_encode(['message' => 'Error: Student ID is missing!']);
    }

} else {
    echo json_encode(['message' => 'Error: Incorrect method!']);
}
?>