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

// Get destination ID
$destination_id = sanitize_input($_POST['destination_id']);

if (empty($destination_id)) {
    json_response(false, 'Destination ID is required');
}

// Check if destination exists
$check_sql = "SELECT * FROM destinations WHERE destination_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $destination_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    json_response(false, 'Destination not found');
}

$check_stmt->close();

// Check if there are any tour packages for this destination
$tour_sql = "SELECT COUNT(*) as tour_count FROM tour_packages WHERE destination_id = ?";
$tour_stmt = $conn->prepare($tour_sql);
$tour_stmt->bind_param("i", $destination_id);
$tour_stmt->execute();
$tour_result = $tour_stmt->get_result();
$tour_count = $tour_result->fetch_assoc()['tour_count'];

if ($tour_count > 0) {
    $tour_stmt->close();
    json_response(false, 'Cannot delete destination with existing tour packages');
}

$tour_stmt->close();

// Delete destination
$sql = "DELETE FROM destinations WHERE destination_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $destination_id);

if ($stmt->execute()) {
    json_response(true, 'Destination deleted successfully');
} else {
    json_response(false, 'Error deleting destination: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>

