<?php

require('db.php');

// CRUD for TourPackage
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getTourPackages') {
    $sql = "SELECT * FROM TourPackage";
    $result = $conn->query($sql);
    $tourPackages = [];
    while ($row = $result->fetch_assoc()) {
        $tourPackages[] = $row;
    }
    echo json_encode($tourPackages);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createTourPackage') {
    $data = getJsonInput();
    $tour_package_name = $data['tour_package_name'];
    $description = $data['description'];
    $destination_id = $data['destination_id'];
    $agent_id = $data['agent_id'];
    $duration = $data['duration'];
    $price = $data['price'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $capacity = $data['capacity'];
    $tour_status = $data['tour_status'];
    $tour_language = $data['tour_language'];

    $sql = "INSERT INTO TourPackage (tour_package_name, description, destination_id, agent_id, duration, price, start_date, end_date, capacity, tour_status, tour_language) VALUES ('$tour_package_name', '$description', $destination_id, $agent_id, $duration, $price, '$start_date', '$end_date', $capacity, '$tour_status', '$tour_language')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Tour package created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
} 
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateTourPackage') {
    $data = getJsonInput();
    $tour_package_id = $data['tour_package_id'];
    $tour_package_name = $data['tour_package_name'];
    $description = $data['description'];
    $destination_id = $data['destination_id'];
    $agent_id = $data['agent_id'];
    $duration = $data['duration'];
    $price = $data['price'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $capacity = $data['capacity'];
    $tour_status = $data['tour_status'];
    $tour_language = $data['tour_language'];

    $sql = "UPDATE TourPackage SET tour_package_name='$tour_package_name', description='$description', destination_id=$destination_id, agent_id=$agent_id, duration=$duration, price=$price, start_date='$start_date', end_date='$end_date', capacity=$capacity, tour_status='$tour_status', tour_language='$tour_language' WHERE tour_package_id=$tour_package_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Tour package updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteTourPackage') {
    $tour_package_id = $_GET['tour_package_id'];
    $sql = "DELETE FROM TourPackage WHERE tour_package_id=$tour_package_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Tour package deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
} 
?>    