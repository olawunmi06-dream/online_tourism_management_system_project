<?php
require_once '../config.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(false, 'Please login to update bookings');
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get booking ID
$booking_id = sanitize_input($_POST['booking_id']);

if (empty($booking_id)) {
    json_response(false, 'Booking ID is required');
}

// Check if booking exists and belongs to user (if not admin)
$check_sql = "SELECT * FROM bookings WHERE booking_id = ?";
if (!$is_admin) {
    $check_sql .= " AND user_id = ?";
}

$check_stmt = $conn->prepare($check_sql);

if (!$is_admin) {
    $check_stmt->bind_param("ii", $booking_id, $user_id);
} else {
    $check_stmt->bind_param("i", $booking_id);
}

$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    json_response(false, 'Booking not found or you do not have permission to update it');
}

$booking = $check_result->fetch_assoc();
$check_stmt->close();

// Check if booking can be updated (not completed or cancelled)
if ($booking['booking_status'] === 'completed' || $booking['booking_status'] === 'cancelled') {
    json_response(false, 'Cannot update a completed or cancelled booking');
}

// Get and sanitize input data
$emergency_contact = isset($_POST['emergency_contact']) ? sanitize_input($_POST['emergency_contact']) : $booking['emergency_contact'];
$special_request = isset($_POST['special_request']) ? sanitize_input($_POST['special_request']) : $booking['special_request'];

// Admin-only fields
$booking_status = $is_admin && isset($_POST['booking_status']) ? sanitize_input($_POST['booking_status']) : $booking['booking_status'];
$tour_guide = $is_admin && isset($_POST['tour_guide']) ? sanitize_input($_POST['tour_guide']) : $booking['tour_guide'];
$discount = $is_admin && isset($_POST['discount']) ? sanitize_input($_POST['discount']) : $booking['discount'];
$check_in_status = $is_admin && isset($_POST['check_in_status']) ? (int)$_POST['check_in_status'] : $booking['check_in_status'];
$check_out_status = $is_admin && isset($_POST['check_out_status']) ? (int)$_POST['check_out_status'] : $booking['check_out_status'];
$cancellation_reason = $is_admin && isset($_POST['cancellation_reason']) ? sanitize_input($_POST['cancellation_reason']) : $booking['cancellation_reason'];

// If user is cancelling their booking
$user_cancelling = isset($_POST['cancel_booking']) && $_POST['cancel_booking'] === 'true';
if ($user_cancelling) {
    $booking_status = 'cancelled';
    $cancellation_reason = isset($_POST['cancellation_reason']) ? sanitize_input($_POST['cancellation_reason']) : 'Cancelled by user';
}

// Update booking in database
$sql = "UPDATE bookings SET 
        emergency_contact = ?, 
        special_request = ?";

$params = [$emergency_contact, $special_request];
$types = "ss";

// Add admin-only fields if user is admin
if ($is_admin || $user_cancelling) {
    $sql .= ", booking_status = ?";
    $params[] = $booking_status;
    $types .= "s";
    
    if ($is_admin) {
        $sql .= ", tour_guide = ?, discount = ?, check_in_status = ?, check_out_status = ?";
        $params[] = $tour_guide;
        $params[] = $discount;
        $params[] = $check_in_status;
        $params[] = $check_out_status;
        $types .= "sdii";
    }
    
    if ($booking_status === 'cancelled') {
        $sql .= ", cancellation_reason = ?";
        $params[] = $cancellation_reason;
        $types .= "s";
    }
}

$sql .= " WHERE booking_id = ?";
$params[] = $booking_id;
$types .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    json_response(true, 'Booking updated successfully');
} else {
    json_response(false, 'Error updating booking: ' . $stmt->error);
}

?>

