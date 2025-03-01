<?php
define('DB_SERVER', 'localhost');
define('DB_USER' ,'root');
define('DB_PASS', '');
define('DB_NAME', 'online_tourism_management_system_db');

$connect_db = mysqli_connect (DB_SERVER, DB_USER, DB_PASS, DB_NAME); 


if($connect_db == true){
    echo "database connected!";
}else{
    echo "database not connected!";
} 
// CRUD for User
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getUsers') {
    $sql = "SELECT * FROM User";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createUser') {
    $data = json_decode(file_get_contents("php://input"), true);
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $phone_number = $data['phone_number'];
    $job = $data['job'];

    $sql = "INSERT INTO User (first_name, last_name, email, password, phone_number, job) VALUES ('$first_name', '$last_name', '$email', '$password', '$phone_number', '$job')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateUser') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $data['user_id'];
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $email = $data['email'];
    $phone_number = $data['phone_number'];
    $job = $data['job'];

    $sql = "UPDATE User SET first_name='$first_name', last_name='$last_name', email='$email', phone_number='$phone_number', job='$job' WHERE user_id=$user_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteUser') {
    $user_id = $_GET['user_id'];
    $sql = "DELETE FROM User WHERE user_id=$user_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}  
// CRUD for TourPackage
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getTourPackages') {
    $sql = "SELECT * FROM TourPackage";
    $result = $conn->query($sql);
    $tourPackages = [];
    while ($row = $result->fetch_assoc()) {
        $tourPackages[] = $row;
    }
    echo json_encode($tourPackages);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createTourPackage') {
    $data = getJsonInput();
    $tour_package_name = $data['tour_package_name'];
    $description = $data['description'];
    $destination_id = $data['destination_id'];
    $agent_id = $data['agent_id'];
    $duration = $data['duration'];
    $price = $data['price'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $capacity = $data['capacity'];
    $tour_status = $data['tour_status'];
    $tour_language = $data['tour_language'];

    $sql = "INSERT INTO TourPackage (tour_package_name, description, destination_id, agent_id, duration, price, start_date, end_date, capacity, tour_status, tour_language) VALUES ('$tour_package_name', '$description', $destination_id, $agent_id, $duration, $price, '$start_date', '$end_date', $capacity, '$tour_status', '$tour_language')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Tour package created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateTourPackage') {
    $data = getJsonInput();
    $tour_package_id = $data['tour_package_id'];
    $tour_package_name = $data['tour_package_name'];
    $description = $data['description'];
    $destination_id = $data['destination_id'];
    $agent_id = $data['agent_id'];
    $duration = $data['duration'];
    $price = $data['price'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $capacity = $data['capacity'];
    $tour_status = $data['tour_status'];
    $tour_language = $data['tour_language'];

    $sql = "UPDATE TourPackage SET tour_package_name='$tour_package_name', description='$description', destination_id=$destination_id, agent_id=$agent_id, duration=$duration, price=$price, start_date='$start_date', end_date='$end_date', capacity=$capacity, tour_status='$tour_status', tour_language='$tour_language' WHERE tour_package_id=$tour_package_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Tour package updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteTourPackage') {
    $tour_package_id = $_GET['tour_package_id'];
    $sql = "DELETE FROM TourPackage WHERE tour_package_id=$tour_package_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Tour package deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }



// CRUD for Booking
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getBookings') {
    $sql = "SELECT * FROM Booking";
    $result = $conn->query($sql);
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    echo json_encode($bookings);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createBooking') {
    $data = getJsonInput();
    $user_id = $data['user_id'];
    $tour_id = $data['tour_id'];
    $booking_date = $data['booking_date'];
    $number_of_days = $data['number_of_days'];
    $total_price = $data['total_price'];
    $emergency_contact = $data['emergency_contact'];
    $booking_status = $data['booking_status'];
    $special_request = $data['special_request'];
    $tour_guide = $data['tour_guide'];
    $discount = $data['discount'];
    $check_in_status = $data['check_in_status'];
    $check_out_status = $data['check_out_status'];
    $cancellation_reason = $data['cancellation_reason'];

    $sql = "INSERT INTO Booking (user_id, tour_id, booking_date, number_of_days, total_price, emergency_contact, booking_status, special_request, tour_guide, discount, check_in_status, check_out_status, cancellation_reason) VALUES ($user_id, $tour_id, '$booking_date', $number_of_days, $total_price, '$emergency_contact', '$booking_status', '$special_request', '$tour_guide', $discount, '$check_in_status', '$check_out_status', '$cancellation_reason')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateBooking') {
    $data = getJsonInput();
    $booking_id = $data['booking_id'];
    $user_id = $data['user_id'];
    $tour_id = $data['tour_id'];
    $booking_date = $data['booking_date'];
    $number_of_days = $data['number_of_days'];
    $total_price = $data['total_price'];
    $emergency_contact = $data['emergency_contact'];
    $booking_status = $data['booking_status'];
    $special_request = $data['special_request'];
    $tour_guide = $data['tour_guide'];
    $discount = $data['discount'];
    $check_in_status = $data['check_in_status'];
    $check_out_status = $data['check_out_status'];
    $cancellation_reason = $data['cancellation_reason'];

    $sql = "UPDATE Booking SET user_id=$user_id, tour_id=$tour_id, booking_date='$booking_date', number_of_days=$number_of_days, total_price=$total_price, emergency_contact='$emergency_contact', booking_status='$booking_status', special_request='$special_request', tour_guide='$tour_guide', discount=$discount, check_in_status='$check_in_status', check_out_status='$check_out_status', cancellation_reason='$cancellation_reason' WHERE booking_id=$booking_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteBooking') {
    $booking_id = $_GET['booking_id'];
    $sql = "DELETE FROM Booking WHERE booking_id=$booking_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}
// CRUD for Payment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getPayments') {
    $sql = "SELECT * FROM Payment";
    $result = $conn->query($sql);
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    echo json_encode($payments);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createPayment') {
    $data = getJsonInput();
    $booking_id = $data['booking_id'];
    $payment_date = $data['payment_date'];
    $amount = $data['amount'];
    $payment_method = $data['payment_method'];
    $payment_status = $data['payment_status'];
    $refund_amount = $data['refund_amount'];
    $refund_date = $data['refund_date'];
    $refund_reason = $data['refund_reason'];

    $sql = "INSERT INTO Payment (booking_id, payment_date, amount, payment_method, payment_status, refund_amount, refund_date, refund_reason) VALUES ($booking_id, '$payment_date', $amount, '$payment_method', '$payment_status', $refund_amount, '$refund_date', '$refund_reason')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Payment created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updatePayment') {
    $data = getJsonInput();
    $payment_id = $data['payment_id'];
    $booking_id = $data['booking_id'];
    $payment_date = $data['payment_date'];
    $amount = $data['amount'];
    $payment_method = $data['payment_method'];
    $payment_status = $data['payment_status'];
    $refund_amount = $data['refund_amount'];
    $refund_date = $data['refund_date'];
    $refund_reason = $data['refund_reason'];

    $sql = "UPDATE Payment SET booking_id=$booking_id, payment_date='$payment_date', amount=$amount, payment_method='$payment_method', payment_status='$payment_status', refund_amount=$refund_amount, refund_date='$refund_date', refund_reason='$refund_reason' WHERE payment_id=$payment_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Payment updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deletePayment') {
    $payment_id = $_GET['payment_id'];
    $sql = "DELETE FROM Payment WHERE payment_id=$payment_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Payment deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}
// CRUD for Review
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getReviews') {
    $sql = "SELECT * FROM Review";
    $result = $conn->query($sql);
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    echo json_encode($reviews);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createReview') {
    $data = getJsonInput();
    $agent_id = $data['agent_id'];
    $user_id = $data['user_id'];
    $agency_name = $data['agency_name'];
    $contact_info = $data['contact_info'];
    $review_text = $data['review_text'];
    $rating = $data['rating'];
    $review_date = $data['review_date'];

    $sql = "INSERT INTO Review (agent_id, user_id, agency_name, contact_info, review_text, rating, review_date) VALUES ($agent_id, $user_id, '$agency_name', '$contact_info', '$review_text', $rating, '$review_date')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Review created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateReview') {
    $data = getJsonInput();
    $review_id = $data['review_id'];
    $agent_id = $data['agent_id'];
    $user_id = $data['user_id'];
    $agency_name = $data['agency_name'];
    $contact_info = $data['contact_info'];
    $review_text = $data['review_text'];
    $rating = $data['rating'];
    $review_date = $data['review_date'];

    $sql = "UPDATE Review SET agent_id=$agent_id, user_id=$user_id, agency_name='$agency_name', contact_info='$contact_info', review_text='$review_text', rating=$rating, review_date='$review_date' WHERE review_id=$review_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Review updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteReview') {
    $review_id = $_GET['review_id'];
    $sql = "DELETE FROM Review WHERE review_id=$review_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Review deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

    // CRUD for Destinations
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getDestinations') {
    $sql = "SELECT * FROM Destinations";
    $result = $conn->query($sql);
    $destinations = [];
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
    echo json_encode($destinations);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createDestination') {
    $data = getJsonInput();
    $destination_name = $data['destination_name'];
    $city = $data['city'];
    $description = $data['description'];
    $image_url = $data['image_url'];

    $sql = "INSERT INTO Destinations (destination_name, city, description, image_url) VALUES ('$destination_name', '$city', '$description', '$image_url')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Destination created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateDestination') {
    $data = getJsonInput();
    $destination_id = $data['destination_id'];
    $destination_name = $data['destination_name'];
    $city = $data['city'];
    $description = $data['description'];
    $image_url = $data['image_url'];

    $sql = "UPDATE Destinations SET destination_name='$destination_name', city='$city', description='$description', image_url='$image_url' WHERE destination_id=$destination_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Destination updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteDestination') {
    $destination_id = $_GET['destination_id'];
    $sql = "DELETE FROM Destinations WHERE destination_id=$destination_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Destination deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}





// CRUD for Transport
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getTransports') {
    $sql = "SELECT * FROM Transport";
    $result = $conn->query($sql);
    $transports = [];
    while ($row = $result->fetch_assoc()) {
        $transports[] = $row;
    }
    echo json_encode($transports);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createTransport') {
    $data = getJsonInput();
    $type = $data['type'];
    $departure_location = $data['departure_location'];
    $arrival_location = $data['arrival_location'];
    $departure_time = $data['departure_time'];
    $arrival_time = $data['arrival_time'];

    $sql = "INSERT INTO Transport (type, departure_location, arrival_location, departure_time, arrival_time) VALUES ('$type', '$departure_location', '$arrival_location', '$departure_time', '$arrival_time')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Transport created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateTransport') {
    $data = getJsonInput();
    $transport_id = $data['transport_id'];
    $type = $data['type'];
    $departure_location = $data['departure_location'];
    $arrival_location = $data['arrival_location'];
    $departure_time = $data['departure_time'];
    $arrival_time = $data['arrival_time'];

    $sql = "UPDATE Transport SET type='$type', departure_location='$departure_location', arrival_location='$arrival_location', departure_time='$departure_time', arrival_time='$arrival_time' WHERE transport_id=$transport_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Transport updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteTransport') {
    $transport_id = $_GET['transport_id'];
    $sql = "DELETE FROM Transport WHERE transport_id=$transport_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Transport deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}
// CRUD for Accommodation
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAccommodations') {
    $sql = "SELECT * FROM Accommodation";
    $result = $conn->query($sql);
    $accommodations = [];
    while ($row = $result->fetch_assoc()) {
        $accommodations[] = $row;
    }
    echo json_encode($accommodations);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'createAccommodation') {
    $data = getJsonInput();
    $type=$data['type '];
    $name = $data['name'];
    $address = $data['address'];
    $destination_id = $data['destination_id'];
    $price_per_night = $data['price_per_night'];

    $sql = "INSERT INTO Accommodation (name, address, destination_id, price_per_night) VALUES ('$name', '$address', $destination_id, $price_per_night)";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Accommodation created successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateAccommodation') {
    $data = getJsonInput();
    $type=$data['type '];
    $accommodation_id = $data['accommodation_id'];
    $name = $data['name'];
    $address = $data['address'];
    $destination_id = $data['destination_id'];
    $price_per_night = $data['price_per_night'];

    $sql = "UPDATE Accommodation SET name='$name', address='$address', destination_id=$destination_id, price_per_night=$price_per_night WHERE accommodation_id=$accommodation_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Accommodation updated successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteAccommodation') {
    $accommodation_id = $_GET['accommodation_id'];
    $sql = "DELETE FROM Accommodation WHERE accommodation_id=$accommodation_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Accommodation deleted successfully"]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
} 
} 
?>

