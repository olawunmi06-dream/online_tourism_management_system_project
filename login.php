<?php
// Include database connection
require('connect.db');

// Start session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $response = [
            'success' => false,
            'message' => 'Please enter both email and password.'
        ];
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                $response = [
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => 'index.php'
                ];
            } else {
                // Password is incorrect
                $response = [
                    'success' => false,
                    'message' => 'Invalid email or password.'
                ];
            }
        } else {
            // User does not exist
            $response = [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

