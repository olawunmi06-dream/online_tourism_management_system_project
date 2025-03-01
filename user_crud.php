<?php

require('db.php');


// CRUD for User
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getUsers') {
    $sql = "SELECT * FROM User";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createUser') {
    $data = json_decode(file_get_contents("php://input"), true);
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $phone_number = $data['phone_number'];
    $job = $data['job'];

    $sql = "INSERT INTO User (first_name, last_name, email, password, phone_number, job) VALUES ('$first_name', '$last_name', '$email', '$password', '$phone_number', '$job')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateUser') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $data['user_id'];
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $email = $data['email'];
    $phone_number = $data['phone_number'];
    $job = $data['job'];

    $sql = "UPDATE User SET first_name='$first_name', last_name='$last_name', email='$email', phone_number='$phone_number', job='$job' WHERE user_id=$user_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteUser') {
    $user_id = $_GET['user_id'];
    $sql = "DELETE FROM User WHERE user_id=$user_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}