<?php
// Database configuration
include 'db/db.php';
// api/login.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit();
}

// For GET requests, return success (for testing)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true, 
        'message' => 'Login API is working',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

// Handle POST requests (login)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Check if data is valid
    if (!$data) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid JSON data received'
        ]);
        exit();
    }
    
    // Check if email and password are provided
    if (!isset($data['email']) || !isset($data['password'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Missing email or password'
        ]);
        exit();
    }
    
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password'];
    
    // Query user by email using your actual column names
    $sql = "SELECT id, fname, mname, lname, employee_id, email, password, date_created 
            FROM users 
            WHERE email = ? 
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    // If user exists, verify password
    if (!empty($row)) {
        // Use the same hashing method: md5(md5($user_id . $password))
        $hashed_password = md5(md5($row['id'] . $password));
        
        if ($hashed_password === $row['password']) {
            // Login successful
            $user = [
                'id'         => $row['id'],
                'fname'      => $row['fname'],
                'mname'      => $row['mname'] ?? '',
                'lname'      => $row['lname'],
                'fullname'   => trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . $row['lname']),
                'employee_id'=> $row['employee_id'] ?? '',
                'email'      => $row['email']
            ];
            
            // Update last login (if you have this column, otherwise skip)
            // $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            // $update_stmt = $conn->prepare($update_sql);
            // $update_stmt->bind_param("i", $user['id']);
            // $update_stmt->execute();
            // $update_stmt->close();
            
            // Generate token
            $token = bin2hex(random_bytes(32));
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid password'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'User not found'
        ]);
    }
    
    $conn->close();
    exit();
}

// If method not allowed
http_response_code(405);
echo json_encode([
    'success' => false, 
    'message' => 'Method not allowed'
]);
$conn->close();
?>