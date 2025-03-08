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

// Get tour ID
$tour_id = sanitize_input($_POST['tour_id']);

if (empty($tour_id)) {
    json_response(false, 'Tour ID is required');
}

// Check if tour exists
$check_sql = "SELECT * FROM tour_packages WHERE tour_package_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $tour_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    json_response(false, 'Tour package not found');
}

$check_stmt->close();

// Check if there are any bookings for this tour
$booking_sql = "SELECT COUNT(*) as booking_count FROM bookings WHERE tour_id = ?";
$booking_stmt = $conn->prepare($booking_sql);
$booking_stmt->bind_param("i", $tour_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();
$booking_count = $booking_result->fetch_assoc()['booking_count'];

if ($booking_count > 0) {
    $booking_stmt->close();
    json_response(false, 'Cannot delete tour package with existing bookings. Consider marking it as inactive instead.');
}

$booking_stmt->close();

// Delete tour package
$sql = "DELETE FROM tour_packages WHERE tour_package_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tour_id);

if ($stmt->execute()) {
    json_response(true, 'Tour package deleted successfully');
} else {
    json_response(false, 'Error deleting tour package: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>

