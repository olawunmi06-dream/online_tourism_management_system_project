<?php
// Include database connection
require('connect.db');

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response = [
        'success' => false,
        'message' => 'Please login to make a payment.'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $booking_id = sanitize_input($_POST['bookingId']);
    $payment_method = sanitize_input($_POST['paymentMethod']);
    $payment_date = date('Y-m-d H:i:s'); // Current date and time
    
    // Get booking details to verify amount
    $booking_query = "SELECT total_price FROM bookings WHERE booking_id = '$booking_id' AND user_id = '{$_SESSION['user_id']}'";
    $booking_result = $conn->query($booking_query);
    
    if ($booking_result->num_rows == 1) {
        $booking = $booking_result->fetch_assoc();
        $amount = $booking['total_price'];
        
        // Insert payment into database
        $sql = "INSERT INTO payments (booking_id, payment_date, amount, payment_method, payment_status) 
                VALUES ('$booking_id', '$payment_date', '$amount', '$payment_method', 'completed')";
        
        if ($conn->query($sql) === TRUE) {
            // Update booking status
            $update_booking = "UPDATE bookings SET booking_status = 'confirmed' WHERE booking_id = '$booking_id'";
            $conn->query($update_booking);
            
            // Payment successful
            $response = [
                'success' => true,
                'message' => 'Payment successful! Your booking is now confirmed.',
                'payment_id' => $conn->insert_id
            ];
        } else {
            // Payment failed
            $response = [
                'success' => false,
                'message' => 'Error: ' . $conn->error
            ];
        }
    } else {
        // Booking not found or doesn't belong to user
        $response = [
            'success' => false,
            'message' => 'Booking not found or unauthorized access.'
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

