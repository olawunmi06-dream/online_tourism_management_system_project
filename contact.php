<?php
// Include database connection
require('connect.db');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    $submission_date = date('Y-m-d H:i:s'); // Current date and time
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $response = [
            'success' => false,
            'message' => 'Please fill in all required fields.'
        ];
    } else {
        // Insert contact message into database
        $sql = "INSERT INTO contact_messages (name, email, subject, message, submission_date) 
                VALUES ('$name', '$email', '$subject', '$message', '$submission_date')";
        
        if ($conn->query($sql) === TRUE) {
            // Send email notification to admin (optional)
            $to = "admin@travelease.com";
            $email_subject = "New Contact Form Submission: $subject";
            $email_body = "You have received a new message from your website contact form.\n\n"
                        . "Name: $name\n"
                        . "Email: $email\n"
                        . "Subject: $subject\n"
                        . "Message:\n$message\n";
            $headers = "From: noreply@travelease.com";
            
            // Uncomment to enable email sending
            // mail($to, $email_subject, $email_body, $headers);
            
            // Success response
            $response = [
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you soon.'
            ];
        } else {
            // Error response
            $response = [
                'success' => false,
                'message' => 'Error: ' . $conn->error
            ];
        }
    }
    
    // If it's an AJAX request, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        // If it's a regular form submission, redirect with message
        if ($response['success']) {
            header('Location: index.html?contact=success');
        } else {
            header('Location: index.html?contact=error&message=' . urlencode($response['message']));
        }
        exit;
    }
}
?>

