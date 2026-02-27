<?php
// Database configuration
$servername = "mysql_db";
$username = "root";
$password = "root";
$dbname = "db_sris";

// Set header to return JSON
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response = [
        'success' => false,
        'message' => 'Database connection failed',
        'error' => $conn->connect_error
    ];
    echo json_encode($response);
    exit();
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Get the JSON data from the request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'records_processed' => 0,
    'errors' => [],
    'import_id' => null
];

// Check if data is valid
if (!$data || !isset($data['data']) || !is_array($data['data'])) {
    $response['message'] = 'Invalid or empty data received';
    echo json_encode($response);
    exit();
}

$records = $data['data'];
$metadata = isset($data['metadata']) ? $data['metadata'] : [];

// First, let's check if the records table exists
$table_check = $conn->query("SHOW TABLES LIKE 'records'");
if ($table_check->num_rows == 0) {
    $response['message'] = 'Table "records" does not exist';
    echo json_encode($response);
    exit();
}

// Check if records table has import_id column, if not add it
$import_id_check = $conn->query("SHOW COLUMNS FROM records LIKE 'import_id'");
if ($import_id_check->num_rows == 0) {
    $alter_sql = "ALTER TABLE records ADD COLUMN import_id INT, ADD INDEX (import_id)";
    if (!$conn->query($alter_sql)) {
        error_log("Failed to add import_id column: " . $conn->error);
    }
}

// Check if import_metadata table exists, if not create it
$metadata_table_check = $conn->query("SHOW TABLES LIKE 'import_metadata'");
if ($metadata_table_check->num_rows == 0) {
    $create_metadata_sql = "CREATE TABLE import_metadata (
        id INT AUTO_INCREMENT PRIMARY KEY,
        source_file VARCHAR(255),
        total_records INT,
        records_imported INT,
        import_timestamp VARCHAR(50),
        import_status VARCHAR(50),
        error_message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($create_metadata_sql)) {
        error_log("Failed to create metadata table: " . $conn->error);
    }
}

// Start transaction
$conn->begin_transaction();

try {
    $import_id = null;
    
    // First, insert metadata and get the import_id
    if (!empty($metadata)) {
        $import_metadata_sql = "INSERT INTO import_metadata (
            source_file, 
            total_records, 
            records_imported, 
            import_timestamp, 
            import_status, 
            error_message
        ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $meta_stmt = $conn->prepare($import_metadata_sql);
        if ($meta_stmt) {
            $source_file = isset($metadata['source_file']) ? $metadata['source_file'] : 'unknown';
            $total_records = isset($metadata['total_records']) ? (int)$metadata['total_records'] : count($records);
            $records_imported = 0; // Will update after processing
            $import_timestamp = isset($metadata['timestamp']) ? $metadata['timestamp'] : date('Y-m-d H:i:s');
            $import_status = 'processing';
            $error_message = '';
            
            $meta_stmt->bind_param('siisss', 
                $source_file, 
                $total_records, 
                $records_imported,
                $import_timestamp,
                $import_status,
                $error_message
            );
            
            if ($meta_stmt->execute()) {
                $import_id = $conn->insert_id;
                error_log("Created import metadata with ID: $import_id");
            } else {
                error_log("Failed to save import metadata: " . $meta_stmt->error);
            }
            $meta_stmt->close();
        }
    }
    
    // Get table columns
    $columns_result = $conn->query("SHOW COLUMNS FROM records");
    $columns = [];
    while ($col = $columns_result->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    error_log("Table columns: " . json_encode($columns));
    
    // Build the INSERT statement dynamically based on actual table columns
    // Add import_id to the columns if it exists
    $insert_columns = $columns;
    $has_import_id = in_array('import_id', $columns);
    
    $column_names = implode(', ', $insert_columns);
    $placeholders = implode(', ', array_fill(0, count($insert_columns), '?'));
    $sql = "INSERT INTO records ($column_names) VALUES ($placeholders)";
    
    error_log("SQL Query: " . $sql);
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }
    
    $placeholder_count = count($insert_columns);
    error_log("Number of placeholders: " . $placeholder_count);
    
    // Bind parameters dynamically
    $types = str_repeat('s', $placeholder_count);
    $params = array_fill(0, $placeholder_count, null);
    
    // Create references for bind_param
    $refs = array();
    $refs[] = $types;
    foreach ($params as $i => $value) {
        $refs[] = &$params[$i];
    }
    
    // Use call_user_func_array to bind parameters
    call_user_func_array(array($stmt, 'bind_param'), $refs);
    
    $records_processed = 0;
    $failed_records = [];
    
    // Create a mapping from your data fields to database columns based on your sample
    $field_mapping = [
        'region' => 'Region',
        'province_m' => 'Province_M',
        'congressional_district' => 'Congressional District',
        'municipality_city' => 'Municipality City',
        'name_of_provider' => 'Name of Provider',
        'complete_address' => 'Complete Address',
        'type_of_provider' => 'Type of Provider',
        'classification_of_provider' => 'Classification of Provider',
        'industry_sector_of_qualification' => 'Industry Sector of Qualification',
        'tvet_program_registration_status' => 'TVET Program Registration Status',
        'qualification_program_title' => 'Qualification Program Title',
        'cluster' => 'Cluster',
        'ctpr' => 'CTPR',
        'training_calendar_code' => 'Training Calendar Code',
        'delivery_mode' => 'Delivery Mode',
        'last_name' => 'Last Name',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'extension_name' => 'Extension Name',
        'uli' => 'ULI',
        'contact_number' => 'Contact Number',
        'email_address' => 'E mail Address',
        'street_no_and_street_address' => 'Street No and Street address',
        'barangay' => 'Barangay',
        'municipality_city_ind' => 'Municipality City.1',
        'district' => 'District',
        'province_n' => 'Province_N',
        'sex' => 'Sex',
        'date_of_birth' => 'Date of Birth',
        'age' => 'Age',
        'civil_status' => 'Civil Status',
        'highest_grade_completed' => 'Highest Grade Completed',
        'nationality' => 'Nationality',
        'classification_of_clients' => 'Classification of Clients',
        'training_status' => 'Training Status',
        'type_of_scholarships' => 'Type of Scholarships',
        'voucher_number' => 'Voucher Number',
        'date_started' => 'Date Started',
        'date_finished' => 'Date Finished',
        'date_assessed' => 'Date Assessed',
        'assessment_results' => 'Assessment Results',
        'employment_status_before_the_training' => 'Employment Status Before the Training',
        'date_of_employment' => 'Date Of Employment',
        'occupation' => 'Occupation',
        'name_of_employer' => 'Name of Employer',
        'address' => 'Address',
        'classification' => 'Classification',
        'salary' => 'Salary'
    ];
    
    foreach ($records as $index => $record) {
        error_log("Processing record $index");
        
        // Reset params to null
        $params = array_fill(0, $placeholder_count, null);
        
        // For each database column, find the corresponding value in the record
        $param_index = 0;
        foreach ($insert_columns as $position => $db_column) {
            if ($db_column === 'import_id' && $import_id !== null) {
                // Set the import_id for this record
                $params[$position] = (string)$import_id;
                error_log("  Setting import_id = $import_id");
            } elseif (isset($field_mapping[$db_column])) {
                $data_field = $field_mapping[$db_column];
                if (isset($record[$data_field]) && $record[$data_field] !== null && $record[$data_field] !== '') {
                    $params[$position] = (string)$record[$data_field];
                    error_log("  Mapping $db_column <- $data_field = " . $params[$position]);
                }
            } else {
                // If no mapping exists, try to use the column name as is
                if (isset($record[$db_column]) && $record[$db_column] !== null && $record[$db_column] !== '') {
                    $params[$position] = (string)$record[$db_column];
                    error_log("  Direct mapping $db_column = " . $params[$position]);
                }
            }
        }
        
        // Update references
        $refs = array();
        $refs[] = $types;
        foreach ($params as $i => $value) {
            $refs[] = &$params[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $refs);
        
        // Execute the statement
        if (!$stmt->execute()) {
            $error = "Failed to insert record at index $index: " . $stmt->error;
            error_log($error);
            $failed_records[] = [
                'index' => $index,
                'error' => $stmt->error,
                'record' => $record
            ];
        } else {
            $records_processed++;
            error_log("Successfully inserted record $index with import_id: $import_id");
        }
    }
    
    // Update metadata with final status
    if ($import_id !== null) {
        $import_status = empty($failed_records) ? 'success' : 'partial';
        $error_message = empty($failed_records) ? '' : json_encode($failed_records);
        
        $update_sql = "UPDATE import_metadata SET 
                       records_imported = ?, 
                       import_status = ?, 
                       error_message = ? 
                       WHERE id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param('issi', $records_processed, $import_status, $error_message, $import_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    $response['success'] = true;
    $response['message'] = "$records_processed records saved successfully";
    $response['records_processed'] = $records_processed;
    $response['import_id'] = $import_id;
    $response['metadata_saved'] = ($import_id !== null);
    
    if (!empty($failed_records)) {
        $response['failed_records'] = count($failed_records);
        $response['errors'] = $failed_records;
    }
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    $response['message'] = 'Failed to save records';
    $response['error'] = $e->getMessage();
    $response['records_processed'] = isset($records_processed) ? $records_processed : 0;
    
    // Log the error
    error_log("PHP Error: " . $e->getMessage());
}

// Close statement if exists
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

// Return JSON response
echo json_encode($response);
?>