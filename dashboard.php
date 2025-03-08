?php
// Include database connection

require_once 'connect.php'; 


// Start session
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get statistics
$total_users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$total_users_result = $conn->query($total_users_query);
$total_users = $total_users_result->fetch_assoc()['total'];

$total_bookings_query = "SELECT COUNT(*) as total FROM bookings";
$total_bookings_result = $conn->query($total_bookings_query);
$total_bookings = $total_bookings_result->fetch_assoc()['total'];

$total_revenue_query = "SELECT SUM(amount) as total FROM payments WHERE payment_status = 'completed'";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue = $total_revenue_result->fetch_assoc()['total'] ?? 0;

$active_tours_query = "SELECT COUNT(*) as total FROM tour_packages WHERE tour_status = 'active'";
$active_tours_result = $conn->query($active_tours_query);
$active_tours = $active_tours_result->fetch_assoc()['total'];

// Get recent bookings
$recent_bookings_query = "SELECT b.booking_id, u.first_name, u.last_name, t.tour_package_name, b.booking_date, b.total_price, b.booking_status 
                         FROM bookings b 
                         JOIN users u ON b.user_id = u.user_id 
                         JOIN tour_packages t ON b.tour_id = t.tour_package_id 
                         ORDER BY b.booking_date DESC LIMIT 5";
$recent_bookings_result = $conn->query($recent_bookings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TravelEase</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="logo">
                <h1>TravelEase</h1>
                <p>Admin Panel</p>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="tours.php"><i class="fas fa-globe"></i> Tour Packages</a></li>
                    <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                    <li><a href="destinations.php"><i class="fas fa-map-marker-alt"></i> Destinations</a></li>
                    <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <header class="admin-header">
                <div class="admin-header-title">
                    <h2>Dashboard</h2>
                </div>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></span>
                    <a href="../logout.php" class="btn btn-sm">Logout</a>
                </div>
            </header>
            
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Users</h3>
                        <p><?php echo $total_users; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Bookings</h3>
                        <p><?php echo $total_bookings; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Revenue</h3>
                        <p>$<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Active Tours</h3>
                        <p><?php echo $active_tours; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="admin-recent">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>Recent Bookings</h3>
                        <a href="bookings.php" class="btn btn-sm">View All</a>
                    </div>
                    <div class="admin-card-body">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Tour Package</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($booking = $recent_bookings_result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $booking['booking_id']; ?></td>
                                    <td><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></td>
                                    <td><?php echo $booking['tour_package_name']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($booking['booking_status']); ?>">
                                            <?php echo ucfirst($booking['booking_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($recent_bookings_result->num_rows == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No bookings found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

