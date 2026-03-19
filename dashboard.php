<?php
// Start session
session_start();

// Database configuration
$host = "localhost";
$db_name = "aguiland_db";
$db_username = "root";
$db_password = "";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login
    header('Location: index.html#login');
    exit;
}

// Database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.html');
    exit;
}

// Get all bookings
try {
    $stmt = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $bookings = [];
    $error = "Failed to load bookings";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Aguiland</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .dashboard-header h1 {
            margin: 0;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 36px;
        }
        .stat-card p {
            margin: 5px 0 0;
            color: #7f8c8d;
        }
        .bookings-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .bookings-table th {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: left;
        }
        .bookings-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        .bookings-table tr:hover {
            background: #f8f9fa;
        }
        .status-pending {
            background: #f39c12;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-confirmed {
            background: #27ae60;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="index.html" class="back-link">← Back to Website</a>
        
        <div class="dashboard-header">
            <h1>Employee Dashboard</h1>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h3><?php echo count($bookings); ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card">
                <h3><?php 
                    $upcoming = 0;
                    foreach($bookings as $b) {
                        if(strtotime($b['tour_date']) >= strtotime('today')) {
                            $upcoming++;
                        }
                    }
                    echo $upcoming;
                ?></h3>
                <p>Upcoming Tours</p>
            </div>
            <div class="stat-card">
                <h3><?php 
                    $totalGuests = 0;
                    foreach($bookings as $b) {
                        $totalGuests += $b['guests'];
                    }
                    echo $totalGuests;
                ?></h3>
                <p>Total Guests</p>
            </div>
        </div>

        <h2>All Bookings</h2>
        
        <?php if (empty($bookings)): ?>
            <div class="no-bookings">
                <p>No bookings found.</p>
            </div>
        <?php else: ?>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Tour Date</th>
                        <th>Guests</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                            <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['tour_date'])); ?></td>
                            <td><?php echo $booking['guests']; ?></td>
                            <td><?php echo htmlspecialchars($booking['message'] ?? '-'); ?></td>
                            <td>
                                <?php if(strtotime($booking['tour_date']) >= strtotime('today')): ?>
                                    <span class="status-pending">Pending</span>
                                <?php else: ?>
                                    <span class="status-confirmed">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
