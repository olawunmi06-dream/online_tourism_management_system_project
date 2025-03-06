<?php
require('connect.db');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getBookings') {
    $sql = "SELECT * FROM Booking";
    $result = $conn->query($sql);
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    echo json_encode($bookings);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createBooking') {
    $data = getJsonInput();
    $user_id = $data['user_id'];
    $tour_id = $data['tour_id'];
    $booking_date = $data['booking_date'];
    $number_of_days = $data['number_of_days'];
    $total_price = $data['total_price'];
    $emergency_contact = $data['emergency_contact'];
    $booking_status = $data['booking_status'];
    $special_request = $data['special_request'];
    $tour_guide = $data['tour_guide'];
    $discount = $data['discount'];
    $check_in_status = $data['check_in_status'];
    $check_out_status = $data['check_out_status'];
    $cancellation_reason = $data['cancellation_reason'];

    $sql = "INSERT INTO Booking (user_id, tour_id, booking_date, number_of_days, total_price, emergency_contact, booking_status, special_request, tour_guide, discount, check_in_status, check_out_status, cancellation_reason) VALUES ($user_id, $tour_id, '$booking_date', $number_of_days, $total_price, '$emergency_contact', '$booking_status', '$special_request', '$tour_guide', $discount, '$check_in_status', '$check_out_status', '$cancellation_reason')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateBooking') {
    $data = getJsonInput();
    $booking_id = $data['booking_id'];
    $user_id = $data['user_id'];
    $tour_id = $data['tour_id'];
    $booking_date = $data['booking_date'];
    $number_of_days = $data['number_of_days'];
    $total_price = $data['total_price'];
    $emergency_contact = $data['emergency_contact'];
    $booking_status = $data['booking_status'];
    $special_request = $data['special_request'];
    $tour_guide = $data['tour_guide'];
    $discount = $data['discount'];
    $check_in_status = $data['check_in_status'];
    $check_out_status = $data['check_out_status'];
    $cancellation_reason = $data['cancellation_reason'];

    $sql = "UPDATE Booking SET user_id=$user_id, tour_id=$tour_id, booking_date='$booking_date', number_of_days=$number_of_days, total_price=$total_price, emergency_contact='$emergency_contact', booking_status='$booking_status', special_request='$special_request', tour_guide='$tour_guide', discount=$discount, check_in_status='$check_in_status', check_out_status='$check_out_status', cancellation_reason='$cancellation_reason' WHERE booking_id=$booking_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteBooking') {
    $booking_id = $_GET['booking_id'];
    $sql = "DELETE FROM Booking WHERE booking_id=$booking_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
} 
?>