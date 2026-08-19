<?php
/**
 * Login API
 * Handles user authentication
 */

// Include database configuration
include 'db/db.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'user' => null,
    'token' => null
];

// Create database connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    $response['message'] = 'Database connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Set charset
$conn->set_charset("utf8mb4");

// Handle GET request - API status check
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response['success'] = true;
    $response['message'] = 'Login API is working';
    $response['timestamp'] = date('Y-m-d H:i:s');
    echo json_encode($response);
    $conn->close();
    exit();
}

// Handle POST request - Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        // Validate JSON
        if (!$data) {
            throw new Exception('Invalid JSON data received');
        }
        
        // Validate required fields
        if (!isset($data['email']) || empty($data['email'])) {
            throw new Exception('Email is required');
        }
        
        if (!isset($data['password']) || empty($data['password'])) {
            throw new Exception('Password is required');
        }
        
        // Sanitize inputs
        $email = $conn->real_escape_string(trim($data['email']));
        $password = $data['password'];
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // Query user by email
        $sql = "SELECT id, fname, mname, lname, employee_id, email, password, date_created 
                FROM users 
                WHERE email = ? 
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Check if user exists
        if (!$user) {
            $response['message'] = 'User not found';
            echo json_encode($response);
            $conn->close();
            exit();
        }
        
        // Verify password using the same hashing method: md5(md5($user_id . $password))
        $hashed_password = md5(md5($user['id'] . $password));
        
        if ($hashed_password !== $user['password']) {
            $response['message'] = 'Invalid password';
            echo json_encode($response);
            $conn->close();
            exit();
        }
        
        // Login successful - Prepare user data
        $user_data = [
            'id' => (int)$user['id'],
            'fname' => $user['fname'],
            'mname' => $user['mname'] ?? '',
            'lname' => $user['lname'],
            'fullname' => trim($user['fname'] . ' ' . ($user['mname'] ?? '') . ' ' . $user['lname']),
            'employee_id' => $user['employee_id'] ?? '',
            'email' => $user['email'],
            'date_created' => $user['date_created']
        ];
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        
        // Optional: Update last login timestamp
        $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            $update_stmt->close();
        }
        
        // Prepare success response
        $response['success'] = true;
        $response['message'] = 'Login successful';
        $response['user'] = $user_data;
        $response['token'] = $token;
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Login error: " . $e->getMessage());
    }
    
    echo json_encode($response);
    $conn->close();
    exit();
}

// Method not allowed
http_response_code(405);
$response['message'] = 'Method not allowed';
echo json_encode($response);
$conn->close();
?>