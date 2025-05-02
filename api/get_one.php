<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/Database.php';
include_once '../models/Student.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $db = new Database();
    $db = $db->connect();

    $student = new Student($db);

    if (isset($_GET['id'])) {
        $student->id = $_GET['id'];

        if ($student->fetchOne()) {

            echo json_encode(array(
                'id' => $student->id,
                'name' => $student->name,
                'address' => $student->address,
                'email' => $student->email // password excluded
            ));

        } else {
            echo json_encode(array('message' => "No records found!"));
        }

    } else {
        echo json_encode(array('message' => "Error: Student ID is missing in query!"));
    }

} else {
    echo json_encode(array('message' => "Error: incorrect Method!"));
}
?>
