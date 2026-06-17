<?php
// Database configuration
include 'db/db.php';


// Set header to return JSON
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

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

// Get target table from metadata
$target_table = isset($metadata['target_table']) ? $metadata['target_table'] : 'assessment_certificates';

// Define allowed tables
$allowed_tables = ['records', 'assessment_certificates'];

// Validate target table
if (!in_array($target_table, $allowed_tables)) {
    $response['message'] = "Invalid target table: $target_table";
    echo json_encode($response);
    exit();
}

// If no records, return early
if (empty($records)) {
    $response['message'] = 'No records to insert';
    echo json_encode($response);
    exit();
}

// Check if import_metadata table exists, if not create it
$metadata_table_check = $conn->query("SHOW TABLES LIKE 'import_metadata'");
if ($metadata_table_check->num_rows == 0) {
    $create_metadata_sql = "CREATE TABLE import_metadata (
        id INT AUTO_INCREMENT PRIMARY KEY,
        target_table VARCHAR(50),
        source_file VARCHAR(255),
        total_records INT,
        records_imported INT,
        import_timestamp VARCHAR(50),
        import_status VARCHAR(50),
        error_message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($create_metadata_sql);
}

// Check if target table has import_id column, if not add it
$import_id_check = $conn->query("SHOW COLUMNS FROM $target_table LIKE 'import_id'");
if ($import_id_check->num_rows == 0) {
    $alter_sql = "ALTER TABLE $target_table ADD COLUMN import_id INT, ADD INDEX (import_id)";
    $conn->query($alter_sql);
}

// Start transaction
$conn->begin_transaction();

try {
    $import_id = null;
    
    // Insert metadata and get import_id
    $import_metadata_sql = "INSERT INTO import_metadata (
        target_table,
        source_file, 
        total_records, 
        records_imported, 
        import_timestamp, 
        import_status, 
        error_message
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $meta_stmt = $conn->prepare($import_metadata_sql);
    if ($meta_stmt) {
        $source_file = isset($metadata['source_file']) ? $metadata['source_file'] : 'unknown';
        $total_records = isset($metadata['total_records']) ? (int)$metadata['total_records'] : count($records);
        $records_imported = 0;
        $import_timestamp = isset($metadata['timestamp']) ? $metadata['timestamp'] : date('Y-m-d H:i:s');
        $import_status = 'processing';
        $error_message = '';
        
        $meta_stmt->bind_param('ssiisss', 
            $target_table,
            $source_file, 
            $total_records, 
            $records_imported,
            $import_timestamp,
            $import_status,
            $error_message
        );
        
        if ($meta_stmt->execute()) {
            $import_id = $conn->insert_id;
            error_log("Created import metadata with ID: $import_id for table: $target_table");
        }
        error_log(htmlspecialchars($meta_stmt->error));
        $meta_stmt->close();
    }
    
    // Get table columns
    $columns_result = $conn->query("SHOW COLUMNS FROM $target_table");
    $columns = [];
    while ($col = $columns_result->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    error_log("Table columns for $target_table: " . json_encode($columns));
    
    // Build INSERT statement
    $insert_columns = $columns;
    $column_names = implode(', ', $insert_columns);
    $placeholders = implode(', ', array_fill(0, count($insert_columns), '?'));
    $sql = "INSERT INTO $target_table ($column_names) VALUES ($placeholders)";
    
    error_log("SQL Query: " . $sql);
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }
    
    $placeholder_count = count($insert_columns);
    $types = str_repeat('s', $placeholder_count);
    $params = array_fill(0, $placeholder_count, null);
    
    // Create references for bind_param
    $refs = array();
    $refs[] = $types;
    foreach ($params as $i => $value) {
        $refs[] = &$params[$i];
    }
    call_user_func_array(array($stmt, 'bind_param'), $refs);
    
    $records_processed = 0;
    $failed_records = [];
    
    // Define field mapping based on target table
    if ($target_table == 'records') {
        // Records table mapping
        $field_mapping = [
            'region' => 'Region',
            'province_m' => 'Province',
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
            'email' => 'E mail Address',
            'street_no_and_street_address' => 'Street No and Street address',
            'barangay' => 'Barangay',
            'municipality' => 'Municipality City_2',
            'district' => 'District',
            'province' => 'Province_2',
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
            'address_of_employer' => 'Address',
            'classification_of_employer' => 'Classification',
            'monthly_salary' => 'Salary'
        ];
    } else {
        // assessment_certificates table mapping based on EXACT Excel headers
        $field_mapping = [
            'region' => 'Region',
            'province' => 'Province',
            'reference_number' => 'Reference Number',
            'learner_id' => 'Learner ID',
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'mi' => 'MI',
            'extension_name' => 'Ext name',
            'date_of_birth' => 'Date of Birth',
            'modality' => 'Modality',
            'client_type' => 'Client Type',
            'address' => 'Address',
            'contact_no' => 'Contact No',
            'sex' => 'Sex',
            'educational_attainment' => 'Educational Attainment',
            'training_completed' => 'Training Completed',
            'institution_school' => 'Institution/School',
            'company' => 'Company',
            'date_of_application' => 'Date Of Application',
            'date_of_assessment' => 'Date Of Assessment',
            'assessment_center' => 'Assessment Center',
            'assessor_name' => 'Assessor\'s Name\'',
            'assessor_accreditation_number' => 'Assessor\'s Accreditation Numner\'',
            'sector' => 'Sector',
            'type_of_certificate' => 'Type Of Certificate',
            'nc_title' => 'NC Title',
            'coc_title' => 'COC Title',
            'assessment_result' => 'Assessment Result',
            'certificate_number' => 'Certificate_Number',
            'date_of_certificate' => 'Date Of Certificate',
            'valid_until' => 'Valid Until'
        ];
    }
    
    // Log first record keys for debugging
    if (!empty($records)) {
        error_log("First record keys: " . json_encode(array_keys($records[0])));
    }
    
    foreach ($records as $index => $record) {
        error_log("Processing record $index for table: $target_table");
        
        // Reset params
        $params = array_fill(0, $placeholder_count, null);
        
        // Map each column
        foreach ($insert_columns as $position => $db_column) {
            if ($db_column === 'import_id' && $import_id !== null) {
                $params[$position] = (string)$import_id;
                error_log("  Setting import_id = $import_id");
            } elseif (isset($field_mapping[$db_column])) {
                $csv_field = $field_mapping[$db_column];
                if (isset($record[$csv_field]) && $record[$csv_field] !== null && $record[$csv_field] !== '') {
                    $value = (string)$record[$csv_field];
                    
                    // Special handling for date fields
                    if ($db_column === 'valid_until' && !empty($value)) {
                        $timestamp = strtotime($value);
                        if ($timestamp !== false) {
                            $value = date('Y-m-d', $timestamp);
                        }
                    }
                    // Handle date_of_birth
                    if ($db_column === 'date_of_birth' && !empty($value)) {
                        $timestamp = strtotime($value);
                        if ($timestamp !== false) {
                            $value = date('Y-m-d', $timestamp);
                        }
                    }
                    
                    $params[$position] = $value;
                    error_log("  Mapped $db_column <- $csv_field = " . substr($value, 0, 50));
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
        
        if (!$stmt->execute()) {
            $error_msg = "Failed to insert record at index $index: " . $stmt->error;
            error_log($error_msg);
            $failed_records[] = ['index' => $index, 'error' => $stmt->error];
        } else {
            $records_processed++;
            error_log("Successfully inserted record $index");
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
    $response['message'] = "$records_processed records saved successfully to $target_table";
    $response['records_processed'] = $records_processed;
    $response['import_id'] = $import_id;
    $response['target_table'] = $target_table;
    
    if (!empty($failed_records)) {
        $response['failed_records'] = count($failed_records);
        $response['errors'] = $failed_records;
    }
    
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = 'Failed to save records';
    $response['error'] = $e->getMessage();
    $response['records_processed'] = isset($records_processed) ? $records_processed : 0;
    error_log("PHP Exception: " . $e->getMessage());
}

// Close connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

echo json_encode($response);
?>