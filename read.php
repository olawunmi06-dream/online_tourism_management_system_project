<?php
require_once '../config.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(false, 'Please login to view bookings');
}

// Check if request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(false, 'Invalid request method');
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get booking ID if provided
$booking_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;

if ($booking_id) {
    // Get specific booking
    $sql = "SELECT b.*, t.tour_package_name, t.image_url, t.price, t.duration, t.tour_status,
            d.destination_name, d.city, u.first_name, u.last_name, u.email, u.phone
            FROM bookings b
            JOIN tour_packages t ON b.tour_id = t.tour_package_id
            JOIN destinations d ON t.destination_id = d.destination_id
            JOIN users u ON b.user_id = u.user_id
            WHERE b.booking_id = ?";
    
    // If not admin, restrict to user's own bookings
    if (!$is_admin) {
        $sql .= " AND b.user_id = ?";
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$is_admin) {
        $stmt->bind_param("ii", $booking_id, $user_id);
    } else {
        $stmt->bind_param("i", $booking_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        json_response(true, 'Booking retrieved successfully', $booking);
    } else {
        json_response(false, 'Booking not found or you do not have permission to view it');
    }
    
    $stmt->close();
} else {
    // Get all bookings with optional filters
    $where_clauses = [];
    $params = [];
    $types = "";
    
    // If not admin, restrict to user's own bookings
    if (!$is_admin) {
        $where_clauses[] = "b.user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    } else if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
        // Admin can filter by user ID
        $filter_user_id = sanitize_input($_GET['user_id']);
        $where_clauses[] = "b.user_id = ?";
        $params[] = $filter_user_id;
        $types .= "i";
    }
    
    // Filter by tour ID
    if (isset($_GET['tour_id']) && !empty($_GET['tour_id'])) {
        $tour_id = sanitize_input($_GET['tour_id']);
        $where_clauses[] = "b.tour_id = ?";
        $params[] = $tour_id;
        $types .= "i";
    }
    
    // Filter by booking status
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $status = sanitize_input($_GET['status']);
        $where_clauses[] = "b.booking_status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    // Filter by date range
    if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
        $start_date = sanitize_input($_GET['start_date']);
        $where_clauses[] = "b.booking_date >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
        $end_date = sanitize_input($_GET['end_date']);
        $where_clauses[] = "b.booking_date <= ?";
        $params[] = $end_date . ' 23:59:59';
        $types .= "s";
    }
    
    // Build the query
    $sql = "SELECT b.*, t.tour_package_name, t.image_url, d.destination_name, d.city
            FROM bookings b
            JOIN tour_packages t ON b.tour_id = t.tour_package_id
            JOIN destinations d ON t.destination_id = d.destination_id";
    
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    // Add sorting
    $sort_by = isset($_GET['sort_by']) ? sanitize_input($_GET['sort_by']) : 'booking_date';
    $sort_order = isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort_by to prevent SQL injection
    $allowed_sort_columns = ['booking_id', 'booking_date', 'travel_date', 'total_price', 'booking_status'];
    if (!in_array($sort_by, $allowed_sort_columns)) {
        $sort_by = 'booking_date';
    }
    
    $sql .= " ORDER BY b.$sort_by $sort_order";
    
    // Add pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;
    
    $sql .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;
    $types .= "ii";
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM bookings b";
    
    if (!empty($where_clauses)) {
        $count_sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    $count_stmt = $conn->prepare($count_sql);
    
    if (!empty($params) && count($where_clauses) > 0) {
        // Remove the last two parameters (offset and limit)
        array_pop($params);
        array_pop($params);
        $count_types = substr($types, 0, -2);
        
        if (!empty($params)) {
            $count_stmt->bind_param($count_types, ...$params);
        }
    }
    
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total = $count_result->fetch_assoc()['total'];
    
    $pagination = [
        'total' => intval($total),
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit)
    ];
    
    json_response(true, 'Bookings retrieved successfully', [
        'bookings' => $bookings,
        'pagination' => $pagination
    ]);
    
    $stmt->close();
    $count_stmt->close();
}


?>

