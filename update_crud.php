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

// Get and sanitize input data
$destination_name = sanitize_input($_POST['destination_name']);
$city = sanitize_input($_POST['city']);
$description = sanitize_input($_POST['description']);

// Validate required fields
if (empty($destination_name) || empty($city) || empty($description)) {
    json_response(false, 'All fields are required');
}

// Handle image upload if provided
$image_url = null;
if (isset($_FILES['destination_image']) && $_FILES['destination_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../uploads/destinations/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES['destination_image']['name']);
    $target_file = $upload_dir . $file_name;
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES['destination_image']['tmp_name']);
    if ($check === false) {
        json_response(false, 'File is not an image');
    }
    
    // Check file size (limit to 5MB)
    if ($_FILES['destination_image']['size'] > 5000000) {
        json_response(false, 'File is too large (max 5MB)');
    }
    
    // Allow certain file formats
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif") {
        json_response(false, 'Only JPG, JPEG, PNG & GIF files are allowed');
    }
    
    // Upload file
    if (move_uploaded_file($_FILES['destination_image']['tmp_name'], $target_file)) {
        $image_url = '/uploads/destinations/' . $file_name;
    } else {
        json_response(false, 'Error uploading file');
    }
}

// Update destination in database
$sql = "UPDATE destinations SET 
        destination_name = ?, 
        city = ?, 
        description = ?";

$params = [$destination_name, $city, $description];
$types = "sss";

// Add image_url to update if provided
if ($image_url !== null) {
    $sql .= ", image_url = ?";
    $params[] = $image_url;
    $types .= "s";
}

$sql .= " WHERE destination_id = ?";
$params[] = $destination_id;
$types .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    json_response(true, 'Destination updated successfully');
} else {
    json_response(false, 'Error updating destination: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>

