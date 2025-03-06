<?php
// Include database connection
require_once 'connect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $first_name = sanitize_input($_POST['firstName']);
    $last_name = sanitize_input($_POST['lastName']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $password = $_POST['password']; // Will be hashed
    $confirm_password = $_POST['confirmPassword'];
    
    // Validate input
    $errors = [];
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already exists. Please use a different email.";
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Set default role as 'user'
        $role = 'user';
        
        // Insert user into database
        $sql = "INSERT INTO users (first_name, last_name, email, phone, password, role) 
                VALUES ('$first_name', '$last_name', '$email', '$phone', '$hashed_password', '$role')";
        
        if ($conn->query($sql) === TRUE) {
            // Registration successful
            $response = [
                'success' => true,
                'message' => 'Registration successful! You can now login.'
            ];
        } else {
            // Registration failed
            $response = [
                'success' => false,
                'message' => 'Error: ' . $conn->error
            ];
        }
    } else {
        // Return errors
        $response = [
            'success' => false,
            'message' => implode('<br>', $errors)
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

