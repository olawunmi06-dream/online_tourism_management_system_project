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

// Get and sanitize input data
$tour_name = sanitize_input($_POST['tour_package_name']);
$description = sanitize_input($_POST['description']);
$destination_id = sanitize_input($_POST['destination_id']);
$agent_id = isset($_POST['agent_id']) ? sanitize_input($_POST['agent_id']) : null;
$duration = sanitize_input($_POST['duration']);
$price = sanitize_input($_POST['price']);
$start_date = sanitize_input($_POST['start_date']);
$end_date = sanitize_input($_POST['end_date']);
$capacity = sanitize_input($_POST['capacity']);
$tour_status = sanitize_input($_POST['tour_status']);
$tour_language = sanitize_input($_POST['tour_language']);

// Validate required fields
if (empty($tour_name) || empty($description) || empty($destination_id) || 
    empty($duration) || empty($price) || empty($start_date) || 
    empty($end_date) || empty($capacity) || empty($tour_status) || empty($tour_language)) {
    json_response(false, 'All fields are required');
}

// Handle image upload if provided
$image_url = '/placeholder.svg?height=250&width=350'; // Default placeholder
if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../uploads/tours/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES['tour_image']['name']);
    $target_file = $upload_dir . $file_name;
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES['tour_image']['tmp_name']);
    if ($check === false) {
        json_response(false, 'File is not an image');
    }
    
    // Check file size (limit to 5MB)
    if ($_FILES['tour_image']['size'] > 5000000) {
        json_response(false, 'File is too large (max 5MB)');
    }
    
    // Allow certain file formats
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif") {
        json_response(false, 'Only JPG, JPEG, PNG & GIF files are allowed');
    }
    
    // Upload file
    if (move_uploaded_file($_FILES['tour_image']['tmp_name'], $target_file)) {
        $image_url = '/uploads/tours/' . $file_name;
    } else {
        json_response(false, 'Error uploading file');
    }
}

// Insert tour package into database
$sql = "INSERT INTO tour_packages (tour_package_name, description, destination_id, agent_id, 
        duration, price, start_date, end_date, capacity, tour_status, tour_language, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiidssisss", $tour_name, $description, $destination_id, $agent_id, 
                 $duration, $price, $start_date, $end_date, $capacity, $tour_status, $tour_language, $image_url);

if ($stmt->execute()) {
    $tour_id = $stmt->insert_id;
    json_response(true, 'Tour package created successfully', ['tour_id' => $tour_id]);
} else {
    json_response(false, 'Error creating tour package: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>

