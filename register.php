<<<<<<< HEAD
<?php
// Include database connection
require_once 'connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log all requests for debugging
file_put_contents('register_log.txt', 
    date('Y-m-d H:i:s') . " - Request method: " . $_SERVER['REQUEST_METHOD'] . "\n" .
    "POST data: " . print_r($_POST, true) . "\n", 
    FILE_APPEND);

// Get form data and sanitize
$first_name = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$last_name = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Log the received data
file_put_contents('register_log.txt', 
    "Processed data: first_name=$first_name, last_name=$last_name, email=$email\n", 
    FILE_APPEND);

// Validate input
$errors = [];

if (empty($first_name)) {
    $errors[] = "First name is required.";
}

if (empty($last_name)) {
    $errors[] = "Last name is required.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (empty($phone)) {
    $errors[] = "Phone number is required.";
}

if (empty($password)) {
    $errors[] = "Password is required.";
}

// If no errors, proceed with registration
if (empty($errors)) {
    try {
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
            
            // Log success
            file_put_contents('register_log.txt', 
                date('Y-m-d H:i:s') . " - Registration successful. User ID: " . $conn->insert_id . "\n", 
                FILE_APPEND);
        } else {
            // Registration failed
            $response = [
                'success' => false,
                'message' => 'Database error: ' . $conn->error
            ];
            
            // Log error
            file_put_contents('register_log.txt', 
                date('Y-m-d H:i:s') . " - Registration failed: " . $conn->error . "\n", 
                FILE_APPEND);
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage()
        ];
        
        // Log exception
        file_put_contents('register_log.txt', 
            date('Y-m-d H:i:s') . " - Exception: " . $e->getMessage() . "\n", 
            FILE_APPEND);
    }
} else {
    // Return errors
    $response = [
        'success' => false,
        'message' => implode('<br>', $errors)
    ];
    
    // Log errors
    file_put_contents('register_log.txt', 
        date('Y-m-d H:i:s') . " - Validation errors: " . implode(', ', $errors) . "\n", 
        FILE_APPEND);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
=======
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

>>>>>>> 0261556fb3140e1ffc4222436a3097e0b74834d8
