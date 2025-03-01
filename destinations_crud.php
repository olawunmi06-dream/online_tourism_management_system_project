<?php

require('db.php');

// CRUD for Destinations
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getDestinations') {
    $sql = "SELECT * FROM Destinations";
    $result = $conn->query($sql);
    $destinations = [];
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
    echo json_encode($destinations);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createDestination') {
    $data = getJsonInput();
    $destination_name = $data['destination_name'];
    $city = $data['city'];
    $description = $data['description'];
    $image_url = $data['image_url'];

    $sql = "INSERT INTO Destinations (destination_name, city, description, image_url) VALUES ('$destination_name', '$city', '$description', '$image_url')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Destination created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateDestination') {
    $data = getJsonInput();
    $destination_id = $data['destination_id'];
    $destination_name = $data['destination_name'];
    $city = $data['city'];
    $description = $data['description'];
    $image_url = $data['image_url'];

    $sql = "UPDATE Destinations SET destination_name='$destination_name', city='$city', description='$description', image_url='$image_url' WHERE destination_id=$destination_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Destination updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteDestination') {
    $destination_id = $_GET['destination_id'];
    $sql = "DELETE FROM Destinations WHERE destination_id=$destination_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Destination deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}


?>