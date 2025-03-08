<?php
require_once '../config.php'; 


// Check if request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(false, 'Invalid request method');
}

// Get destination ID if provided
$destination_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;

if ($destination_id) {
    // Get specific destination
    $sql = "SELECT * FROM destinations WHERE destination_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $destination = $result->fetch_assoc();
        json_response(true, 'Destination retrieved successfully', $destination);
    } else {
        json_response(false, 'Destination not found');
    }
    
    $stmt->close();
} else {
    // Get all destinations with optional filters
    $where_clauses = [];
    $params = [];
    $types = "";
    
    // Filter by city
    if (isset($_GET['city']) && !empty($_GET['city'])) {
        $city = sanitize_input($_GET['city']);
        $where_clauses[] = "city LIKE ?";
        $params[] = "%$city%";
        $types .= "s";
    }
    
    // Filter by name
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $name = sanitize_input($_GET['name']);
        $where_clauses[] = "destination_name LIKE ?";
        $params[] = "%$name%";
        $types .= "s";
    }
    
    // Build the query
    $sql = "SELECT * FROM destinations";
    
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    // Add sorting
    $sort_by = isset($_GET['sort_by']) ? sanitize_input($_GET['sort_by']) : 'destination_id';
    $sort_order = isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'DESC' ? 'DESC' : 'ASC';
    
    // Validate sort_by to prevent SQL injection
    $allowed_sort_columns = ['destination_id', 'destination_name', 'city'];
    if (!in_array($sort_by, $allowed_sort_columns)) {
        $sort_by = 'destination_id';
    }
    
    $sql .= " ORDER BY $sort_by $sort_order";
    
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
    
    $destinations = [];
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
    
    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM destinations";
    
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
    
    json_response(true, 'Destinations retrieved successfully', [
        'destinations' => $destinations,
        'pagination' => $pagination
    ]);
    
    $stmt->close();
    $count_stmt->close();
}

?>

