<?php
// Include database connection
require_once 'connect.php';

// Start session
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log all requests for debugging
file_put_contents('booking_log.txt', 
    date('Y-m-d H:i:s') . " - Request method: " . $_SERVER['REQUEST_METHOD'] . "\n" .
    "POST data: " . print_r($_POST, true) . "\n", 
    FILE_APPEND);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with error message
    header('Location: index.php?login=required&redirect=booking-form.php');
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $user_id = $_SESSION['user_id'];
    $tour_id = isset($_POST['tour_id']) ? sanitize_input($_POST['tour_id']) : '';
    $booking_date = date('Y-m-d H:i:s'); // Current date and time
    $travel_date = isset($_POST['travel_date']) ? sanitize_input($_POST['travel_date']) : '';
    $num_travelers = isset($_POST['num_travelers']) ? sanitize_input($_POST['num_travelers']) : '';
    $emergency_contact = isset($_POST['emergency_contact']) ? sanitize_input($_POST['emergency_contact']) : '';
    $special_request = isset($_POST['special_request']) ? sanitize_input($_POST['special_request']) : '';
    
    // Log the received data
    file_put_contents('booking_log.txt', 
        "Processed data: user_id=$user_id, tour_id=$tour_id, travel_date=$travel_date\n", 
        FILE_APPEND);
    
    // Validate input
    $errors = [];
    
    if (empty($tour_id)) {
        $errors[] = "Please select a tour package.";
    }
    
    if (empty($travel_date)) {
        $errors[] = "Please select a travel date.";
    } else {
        // Check if travel date is in the future
        $current_date = date('Y-m-d');
        if ($travel_date < $current_date) {
            $errors[] = "Travel date must be in the future.";
        }
    }
    
    if (empty($num_travelers) || $num_travelers < 1) {
        $errors[] = "Number of travelers must be at least 1.";
    }
    
    if (empty($emergency_contact)) {
        $errors[] = "Emergency contact information is required.";
    }
    
    // If there are validation errors
    if (!empty($errors)) {
        // Store errors in session
        $_SESSION['booking_errors'] = $errors;
        $_SESSION['booking_form_data'] = $_POST; // Store form data for repopulation
        
        // Redirect back to form
        header('Location: booking-form.php');
        exit;
    }
    
    // Get tour details to calculate total price
    $tour_query = "SELECT price, duration FROM tour_packages WHERE tour_package_id = ?";
    $stmt = $conn->prepare($tour_query);
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $tour = $result->fetch_assoc();
        $price_per_person = $tour['price'];
        $duration = $tour['duration'];
        $total_price = $price_per_person * $num_travelers;
        
        // Insert booking into database
        $insert_sql = "INSERT INTO bookings (user_id, tour_id, booking_date, travel_date, number_of_travelers, number_of_days, total_price, emergency_contact, booking_status, special_request) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iissiiiss", $user_id, $tour_id, $booking_date, $travel_date, $num_travelers, $duration, $total_price, $emergency_contact, $special_request);
        
        if ($insert_stmt->execute()) {
            $booking_id = $insert_stmt->insert_id;
            
            // Store success message in session
            $_SESSION['booking_success'] = true;
            $_SESSION['booking_id'] = $booking_id;
            $_SESSION['booking_total'] = $total_price;
            
            // Log success
            file_put_contents('booking_log.txt', 
                date('Y-m-d H:i:s') . " - Booking successful. Booking ID: " . $booking_id . "\n", 
                FILE_APPEND);
            
            // Redirect to booking confirmation page
            header('Location: booking-confirmation.php');
            exit;
        } else {
            // Store error in session
            $_SESSION['booking_errors'] = ["Database error: " . $insert_stmt->error];
            $_SESSION['booking_form_data'] = $_POST; // Store form data for repopulation
            
            // Log error
            file_put_contents('booking_log.txt', 
                date('Y-m-d H:i:s') . " - Booking failed: " . $insert_stmt->error . "\n", 
                FILE_APPEND);
            
            // Redirect back to form
            header('Location: booking-form.php');
            exit;
        }
    } else {
        // Store error in session
        $_SESSION['booking_errors'] = ["Tour package not found."];
        $_SESSION['booking_form_data'] = $_POST; // Store form data for repopulation
        
        // Log error
        file_put_contents('booking_log.txt', 
            date('Y-m-d H:i:s') . " - Tour not found: " . $tour_id . "\n", 
            FILE_APPEND);
        
        // Redirect back to form
        header('Location: booking-form.php');
        exit;
    }
} else {
    // If not POST request, redirect to form
    header('Location: booking-form.php');
    exit;
}
?>

