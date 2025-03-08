<?php
require_once '../config.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(false, 'Please login to book a tour');
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get and sanitize input data
$tour_id = sanitize_input($_POST['tour_id']);
$booking_date = date('Y-m-d H:i:s'); // Current date and time
$travel_date = sanitize_input($_POST['travel_date']);
$num_travelers = sanitize_input($_POST['num_travelers']);
$emergency_contact = sanitize_input($_POST['emergency_contact']);
$special_request = isset($_POST['special_request']) ? sanitize_input($_POST['special_request']) : '';

// Validate required fields
if (empty($tour_id) || empty($travel_date) || empty($num_travelers) || empty($emergency_contact)) {
    json_response(false, 'All required fields must be filled');
}

// Check if tour exists and get details
$tour_sql = "SELECT * FROM tour_packages WHERE tour_package_id = ?";
$tour_stmt = $conn->prepare($tour_sql);
$tour_stmt->bind_param("i", $tour_id);
$tour_stmt->execute();
$tour_result = $tour_stmt->get_result();

if ($tour_result->num_rows === 0) {
    $tour_stmt->close();
    json_response(false, 'Tour package not found');
}

$tour = $tour_result->fetch_assoc();
$tour_stmt->close();

// Check if tour is active
if ($tour['tour_status'] !== 'active') {
    json_response(false, 'This tour package is not available for booking');
}

// Check if travel date is valid
$current_date = date('Y-m-d');
if ($travel_date < $current_date) {
    json_response(false, 'Travel date cannot be in the past');
}

if ($travel_date < $tour['start_date'] || $travel_date > $tour['end_date']) {
    json_response(false, 'Travel date must be within the tour package dates');
}

// Check if number of travelers is valid
if ($num_travelers < 1) {
    json_response(false, 'Number of travelers must be at least 1');
}

// Check if there's enough capacity
$booked_travelers_sql = "SELECT SUM(number_of_travelers) as booked FROM bookings WHERE tour_id = ? AND booking_status IN ('pending', 'confirmed')";
$booked_stmt = $conn->prepare($booked_travelers_sql);
$booked_stmt->bind_param("i", $tour_id);
$booked_stmt->execute();
$booked_result = $booked_stmt->get_result();
$booked_travelers = $booked_result->fetch_assoc()['booked'] ?? 0;
$booked_stmt->close();

$available_capacity = $tour['capacity'] - $booked_travelers;
if ($num_travelers > $available_capacity) {
    json_response(false, "Sorry, only $available_capacity spots available for this tour");
}

// Calculate total price
$price_per_person = $tour['price'];
$total_price = $price_per_person * $num_travelers;
$duration = $tour['duration'];

// Insert booking into database
$sql = "INSERT INTO bookings (user_id, tour_id, booking_date, travel_date, number_of_travelers, number_of_days, total_price, emergency_contact, booking_status, special_request) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iissiiiss", $user_id, $tour_id, $booking_date, $travel_date, $num_travelers, $duration, $total_price, $emergency_contact, $special_request);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id;
    json_response(true, 'Booking successful! Your booking ID is: ' . $booking_id, [
        'booking_id' => $booking_id,
        'total_price' => $total_price
    ]);
} else {
    json_response(false, 'Error creating booking: ' . $stmt->error);
}



?>

