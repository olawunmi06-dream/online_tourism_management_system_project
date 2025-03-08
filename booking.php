<<<<<<< HEAD
<?php
// Include database connection
require('connect.db');

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response = [
        'success' => false,
        'message' => 'Please login to book a tour.'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $user_id = $_SESSION['user_id'];
    $tour_id = sanitize_input($_POST['tourPackage']);
    $booking_date = date('Y-m-d H:i:s'); // Current date and time
    $travel_date = sanitize_input($_POST['travelDate']);
    $num_travelers = sanitize_input($_POST['travelers']);
    $emergency_contact = sanitize_input($_POST['emergencyContact']);
    $special_request = sanitize_input($_POST['specialRequests']);
    
    // Get tour details to calculate total price
    $tour_query = "SELECT price, duration FROM tour_packages WHERE tour_package_id = '$tour_id'";
    $tour_result = $conn->query($tour_query);
    
    if ($tour_result->num_rows == 1) {
        $tour = $tour_result->fetch_assoc();
        $price_per_person = $tour['price'];
        $duration = $tour['duration'];
        $total_price = $price_per_person * $num_travelers;
        
        // Insert booking into database
        $sql = "INSERT INTO bookings (user_id, tour_id, booking_date, number_of_days, total_price, emergency_contact, booking_status, special_request) 
                VALUES ('$user_id', '$tour_id', '$booking_date', '$duration', '$total_price', '$emergency_contact', 'pending', '$special_request')";
        
        if ($conn->query($sql) === TRUE) {
            $booking_id = $conn->insert_id;
            
            // Booking successful
            $response = [
                'success' => true,
                'message' => 'Booking successful! Your booking ID is: ' . $booking_id,
                'booking_id' => $booking_id,
                'total_price' => $total_price
            ];
        } else {
            // Booking failed
            $response = [
                'success' => false,
                'message' => 'Error: ' . $conn->error
            ];
        }
    } else {
        // Tour not found
        $response = [
            'success' => false,
            'message' => 'Tour package not found.'
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>


=======
<?php
// Include database connection
require('connect.db');

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response = [
        'success' => false,
        'message' => 'Please login to book a tour.'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $user_id = $_SESSION['user_id'];
    $tour_id = sanitize_input($_POST['tourPackage']);
    $booking_date = date('Y-m-d H:i:s'); // Current date and time
    $travel_date = sanitize_input($_POST['travelDate']);
    $num_travelers = sanitize_input($_POST['travelers']);
    $emergency_contact = sanitize_input($_POST['emergencyContact']);
    $special_request = sanitize_input($_POST['specialRequests']);
    
    // Get tour details to calculate total price
    $tour_query = "SELECT price, duration FROM tour_packages WHERE tour_package_id = '$tour_id'";
    $tour_result = $conn->query($tour_query);
    
    if ($tour_result->num_rows == 1) {
        $tour = $tour_result->fetch_assoc();
        $price_per_person = $tour['price'];
        $duration = $tour['duration'];
        $total_price = $price_per_person * $num_travelers;
        
        // Insert booking into database
        $sql = "INSERT INTO bookings (user_id, tour_id, booking_date, number_of_days, total_price, emergency_contact, booking_status, special_request) 
                VALUES ('$user_id', '$tour_id', '$booking_date', '$duration', '$total_price', '$emergency_contact', 'pending', '$special_request')";
        
        if ($conn->query($sql) === TRUE) {
            $booking_id = $conn->insert_id;
            
            // Booking successful
            $response = [
                'success' => true,
                'message' => 'Booking successful! Your booking ID is: ' . $booking_id,
                'booking_id' => $booking_id,
                'total_price' => $total_price
            ];
        } else {
            // Booking failed
            $response = [
                'success' => false,
                'message' => 'Error: ' . $conn->error
            ];
        }
    } else {
        // Tour not found
        $response = [
            'success' => false,
            'message' => 'Tour package not found.'
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>


>>>>>>> 0261556fb3140e1ffc4222436a3097e0b74834d8
