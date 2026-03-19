<?php
// Database configuration - adjust these to match your XAMPP setup
$host = "localhost";
$db_name = "aguiland_db";
$db_username = "root"; // Default XAMPP username
$db_password = ""; // Default XAMPP password is empty

// Set response header
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the form data
$name = trim($_POST['bookingName'] ?? '');
$email = trim($_POST['bookingEmail'] ?? '');
$phone = trim($_POST['bookingPhone'] ?? '');
$date = trim($_POST['bookingDate'] ?? '');
$guests = trim($_POST['bookingGuests'] ?? '');
$message = trim($_POST['bookingMessage'] ?? '');

// Validate input
if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($guests)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please fill in all required fields'
    ]);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please enter a valid email address'
    ]);
    exit;
}

// Database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Insert booking into database
try {
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, phone, tour_date, guests, message) VALUES (:name, :email, :phone, :date, :guests, :message)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':guests', $guests);
    $stmt->bindParam(':message', $message);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you for your booking! We will contact you shortly.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Booking failed. Please try again.'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Booking failed. Please try again.'
    ]);
}
