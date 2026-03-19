<?php
session_start();

$host = "localhost";
$db_name = "aguiland_db";
$db_username = "root"; 
$db_password = ""; 

// Create PDO connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed.'
    ]);
    exit;
}

// Set response header
header('Content-Type: application/json');

// Get the action from POST data
$action = $_POST['action'] ?? '';

// Handle Login
if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please enter both username and password'
        ]);
        exit;
    }
    
    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $password === $user['password']) {
            // Login successful - set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful! Welcome back, ' . htmlspecialchars($user['username']) . '!',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            // Login failed
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid username or password'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Login failed. Please try again.'
        ]);
    }
    exit;
}

// Handle Registration
if ($action === 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill in all fields'
        ]);
        exit;
    }
    
    if ($password !== $confirm_password) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Passwords do not match'
        ]);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password must be at least 6 characters'
        ]);
        exit;
    }
    
    if (strlen($username) < 3) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username must be at least 3 characters'
        ]);
        exit;
    }
    
    try {
        // Check if username already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $check_stmt->bindParam(':username', $username);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Username already exists. Please choose another.'
            ]);
            exit;
        }
        
        // Insert new user and password
        $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $insert_stmt->bindParam(':username', $username);
        $insert_stmt->bindParam(':password', $password);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Registration successful! You can now login.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Registration failed. Please try again.'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Registration failed. Please try again.'
        ]);
    }
    exit;
}

// Invalid action
echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request'
]);
