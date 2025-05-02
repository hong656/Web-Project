<?php

class Student {

    private $conn;
    
    public $id;
    public $name;
    public $address;
    public $email;
    public $password;

    public function __construct($db){
        $this->conn = $db;
    }

    // Fetch all students
    public function fetchAll() {
        $stmt = $this->conn->prepare('SELECT * FROM students');
        $stmt->execute();
        return $stmt;
    }

    // Fetch one student by id
    public function fetchOne() {
        $stmt = $this->conn->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->bindParam(1, $this->id);
        $stmt->execute();        

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->address = $row['address'];
            $this->email = $row['email']; // Add email
            $this->password = $row['password']; // Add password

            return TRUE;
        }

        return FALSE;
    }

    // Insert new student data (with email and password)
    public function postData() {
        $stmt = $this->conn->prepare('INSERT INTO students SET name = :name, address = :address, age = :age, email = :email, password = :password');

        // Bind the parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password); // Ensure password is hashed

        // Execute the statement
        if($stmt->execute()) {
            return TRUE;
        }

        return FALSE;
    }

    // Update student data (with email and password)
    public function putData() {
        // Prepare the query
        $stmt = $this->conn->prepare('UPDATE students SET name = :name, address = :address, age = :age, email = :email, password = :password WHERE id = :id');

        // Bind the parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password); // Ensure password is hashed
        $stmt->bindParam(':id', $this->id);

        // Execute the statement
        if($stmt->execute()) {
            return TRUE;
        }

        return FALSE;
    }

    // Delete a student by ID
    public function delete() {
        $stmt = $this->conn->prepare('DELETE FROM students WHERE id = :id');
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return TRUE;
        }

        return FALSE;
    }
}
?>