<?php
require('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getTransports') {
    $sql = "SELECT * FROM Transport";
    $result = $conn->query($sql);
    $transports = [];
    while ($row = $result->fetch_assoc()) {
        $transports[] = $row;
    }
    echo json_encode($transports);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createTransport') {
    $data = getJsonInput();
    $type = $data['type'];
    $departure_location = $data['departure_location'];
    $arrival_location = $data['arrival_location'];
    $departure_time = $data['departure_time'];
    $arrival_time = $data['arrival_time'];

    $sql = "INSERT INTO Transport (type, departure_location, arrival_location, departure_time, arrival_time) VALUES ('$type', '$departure_location', '$arrival_location', '$departure_time', '$arrival_time')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Transport created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateTransport') {
    $data = getJsonInput();
    $transport_id = $data['transport_id'];
    $type = $data['type'];
    $departure_location = $data['departure_location'];
    $arrival_location = $data['arrival_location'];
    $departure_time = $data['departure_time'];
    $arrival_time = $data['arrival_time'];

    $sql = "UPDATE Transport SET type='$type', departure_location='$departure_location', arrival_location='$arrival_location', departure_time='$departure_time', arrival_time='$arrival_time' WHERE transport_id=$transport_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Transport updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteTransport') {
    $transport_id = $_GET['transport_id'];
    $sql = "DELETE FROM Transport WHERE transport_id=$transport_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Transport deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    } 
} 
    ?>
    