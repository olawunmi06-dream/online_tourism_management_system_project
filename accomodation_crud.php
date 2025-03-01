<?php
require('db.php'); 

// CRUD for Accommodation
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAccommodations') {
    $sql = "SELECT * FROM Accommodation";
    $result = $conn->query($sql);
    $accommodations = [];
    while ($row = $result->fetch_assoc()) {
        $accommodations[] = $row;
    }
    echo json_encode($accommodations);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createAccommodation') {
    $data = getJsonInput();
    $type=$data['type '];
    $name = $data['name'];
    $address = $data['address'];
    $destination_id = $data['destination_id'];
    $price_per_night = $data['price_per_night'];

    $sql = "INSERT INTO Accommodation (name, address, destination_id, price_per_night) VALUES ('$name', '$address', $destination_id, $price_per_night)";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Accommodation created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateAccommodation') {
    $data = getJsonInput();
    $type=$data['type '];
    $accommodation_id = $data['accommodation_id'];
    $name = $data['name'];
    $address = $data['address'];
    $destination_id = $data['destination_id'];
    $price_per_night = $data['price_per_night'];

    $sql = "UPDATE Accommodation SET name='$name', address='$address', destination_id=$destination_id, price_per_night=$price_per_night WHERE accommodation_id=$accommodation_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Accommodation updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteAccommodation') {
    $accommodation_id = $_GET['accommodation_id'];
    $sql = "DELETE FROM Accommodation WHERE accommodation_id=$accommodation_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Accommodation deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
} 
?>
