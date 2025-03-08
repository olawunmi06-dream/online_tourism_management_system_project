<?php
require_once '../config.php';

// Check if user is admin
if (!is_admin()) {
    json_response(false, 'Unauthorized access');
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

// Get booking ID
$booking_id = sanitize_input($_POST['booking_id']);

if (empty($booking_id)) {
    json_response(false, 'Booking ID is required');
}

// Check if booking exists
$check_sql = "SELECT * FROM bookings WHERE booking_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $booking_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    json_response(false, 'Booking not found');
}

$booking = $check_result->fetch_assoc();
$check_stmt->close();

// Check if there are any payments for this booking
$payment_sql = "SELECT COUNT(*) as payment_count FROM payments WHERE booking_id = ?";
$payment_stmt = $conn->prepare($payment_sql);
$payment_stmt->bind_param("i", $booking_id);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();
$payment_count = $payment_result->fetch_assoc()['payment_count'];

if ($payment_count > 0) {
    $payment_stmt->close();
    json_response(false, 'Cannot delete booking with existing payments. Consider marking it as cancelled instead.');
}

$payment_stmt->close();

// Delete booking
$sql = "DELETE FROM bookings WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    json_response(true, 'Booking deleted successfully');
} else {
    json_response(false, 'Error deleting booking: ' . $stmt->error);
}

?>

