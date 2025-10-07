<?php
    require "db/db.php";

    function validate_registration($student) {
        $validation_errors = [];

        if (!empty($student)) {
            $uli_number = trim($student['uli_number'] ?? '');
            $entry_date = $student['entry_date'] ?? '';
            $last_name = trim($student['last_name'] ?? '');
            $first_name = trim($student['first_name'] ?? '');
    
            // ULI Number
            if (empty($uli_number)) {
                $validation_errors[] = "Unique Learner Identifier (ULI)  is required.";
            } 
    
            // Entry Date
            if (empty($entry_date)) {
                $validation_errors[] = "Entry Date is required.";
            } 

            // last name
            if (empty($last_name)) {
                $validation_errors[] = "Last name is required.";
            } 

            // First name
            if (empty($first_name)) {
                $validation_errors[] = "First name is required.";
            } 
    
        } else {
            $validation_errors[] = "No data submitted.";
        }
    
        return $validation_errors;
    }

    function save_registration($fields = [], $id = null) {
        global $conn;
        $flag = false;
    
        // Allowed columns in DB
        $allowed = [
            'uli_number', 'entry_date', 'last_name', 'first_name', 'middle_name', 
            'mail_number_st', 'mail_barangay', 'mail_district', 'mail_citymun', 'mail_province',
            'mail_region', 'email_facebook', 'contact_no', 'nationality', 'sex',
            'civil_status', 'employment_status', 'employment_type', 'dob', 'bplace_citymun',
            'bplace_province', 'bplace_region', 'educational_attainment', 'guardian_name', 'guardian_mailing_address',
            'student_classification', 'other_classification', 'type_disability', 'cause_disability', 'course_qualification',
            'type_scholarship', 'picture'
        ];
    
        // Filter only allowed keys
        $data = array_intersect_key($fields, array_flip($allowed));
    
        if (isset($data['dob']) && empty($data['dob'])) {
            $data['dob'] = null;
        }

        if ($id === null) {
            // Insert
            $data['date_created'] = date("Y-m-d H:i:s");
    
            $columns = array_keys($data);
            $placeholders = implode(",", array_fill(0, count($columns), "?"));
            $sql = "INSERT INTO registrations (`" . implode("`,`", $columns) . "`) VALUES ($placeholders)"; 

            $stmt = $conn->prepare($sql);
            $types = str_repeat("s", count($columns));
            $values = array_values($data);
    
            $stmt->bind_param($types, ...$values);
        } else {
            // Update
            $data['last_updated'] = date("Y-m-d H:i:s");
    
            $set = implode(", ", array_map(fn($col) => "`$col` = ?", array_keys($data)));

            $sql = "UPDATE registrations SET $set WHERE id = ?";
    
            $stmt = $conn->prepare($sql);
    
            $types = str_repeat("s", count($data)) . "i"; // last one is for id
            $values = array_merge(array_values($data), [$id]);
            
            $stmt->bind_param($types, ...$values);
        }
        
        if($stmt->execute())
            $flag = true;
        
        // print_r($stmt->error);
        // exit;

        return $flag;
    }    

    function get_all_registrations($filter = [], $pagination = []) {
        global $conn;
    
        $registrations = ['total' => 0, 'result' => []];
    
        $query = "SELECT 
                    r.id, r.uli_number, r.entry_date, r.last_name, r.first_name,  
                    r.middle_name, r.mail_number_st, r.mail_barangay, r.mail_district, r.mail_citymun,
                    r.mail_province, r.mail_region, r.email_facebook, r.contact_no, r.nationality, r.sex,
                    r.civil_status, r.employment_status, r.employment_type, r.bplace_citymun, r.bplace_province,
                    r.bplace_region, r.educational_attainment, r.guardian_name, r.guardian_mailing_address, r.student_classification,
                    r.other_classification, r.type_disability, r.cause_disability, r.course_qualification, r.type_scholarship, r.picture,
                    r.last_updated, r.date_created
                  FROM registrations r";
    
        $conditions = [];
        $params = [];
        $types = "";
    
        // Build WHERE conditions
        if (!empty($filter)) {
            foreach ($filter as $column => $value) {
                if ($column === "search" && is_array($value)) {
                    // Example: ["columns" => ["first_name","last_name"], "term" => "john"]
                    $searchCols = $value[0] ?? [];
                    $searchTerm = $value[1] ?? "";
    
                    $searchParts = [];
                    foreach ($searchCols as $col) {
                        $searchParts[] = "$col LIKE ?";
                        $params[] = "%$searchTerm%";
                        $types .= "s";
                    }
                    if ($searchParts) {
                        $conditions[] = "(" . implode(" OR ", $searchParts) . ")";
                    }
                } else {
                    $conditions[] = "$column = ?";
                    $params[] = $value;
                    $types .= "s";
                }
            }
        }
    
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
    
        // Count query for pagination
        $count_query = "SELECT COUNT(*) AS total FROM (" . $query . ") AS total_records";
        $stmt = $conn->prepare($count_query);
        if ($stmt === false) {
            throw new Exception("Prepare failed (COUNT): " . $conn->error . "\nSQL: " . $count_query);
        }
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $count_result = $stmt->get_result()->fetch_assoc();
        $registrations['total'] = $count_result['total'] ?? 0;
        $stmt->close();
    
        // Main query
        $query .= " ORDER BY r.id DESC";
        if (!empty($pagination)) {
            $query .= " LIMIT ?, ?";
            $params[] = (int)$pagination['offset'];
            $params[] = (int)$pagination['total_records_per_page'];
            $types .= "ii";
        }
    
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Prepare failed (MAIN): " . $conn->error . "\nSQL: " . $query);
        }
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $registrations['result'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        return $registrations;
    }

    function view_registration($id) {
        global $conn;
        $registration = [];

        $query = "SELECT * FROM `registrations` AS `r`  WHERE `r`.`id` = ?";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
          
            if ($result) {
                $registration = $result->fetch_array(MYSQLI_ASSOC);
            }
            $stmt->close();
        }

        return $registration;
    }
    
    function delete_registration($id) {
        global $conn;
        $flag = false;

        $stmt = $conn->prepare("SELECT id FROM `registrations` WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM `registrations` WHERE id = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $flag = true;
            }
        }

        return $flag;
    }

    function get_registration_count() {
        global $conn;
        $count = 0;

        $query = "SELECT COUNT(*) AS total FROM registrations";
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $count = $row['total'] ?? 0;
        }

        return $count;
    }

    function get_registration_male_count() {
        global $conn;
        $count = 0;

        $query = "SELECT COUNT(*) AS total FROM registrations WHERE sex = 'm'";
        $result = $conn->query($query);;
        if ($result) {       
            $row = $result->fetch_assoc();
            $count = $row['total'] ?? 0;
        }   
        return $count;
    }

    function get_registration_female_count() {
        global $conn;
        $count = 0;

        $query = "SELECT COUNT(*) AS total FROM registrations WHERE sex = 'f'";
        $result = $conn->query($query);;
        if ($result) {       
            $row = $result->fetch_assoc();
            $count = $row['total'] ?? 0;
        }   
        return $count;
    }

    function get_registration_unemployed_count() {
        global $conn;
        $count = 0;

        $query = "SELECT COUNT(*) AS total FROM registrations WHERE employment_status = 'ue'";
        $result = $conn->query($query);;
        if ($result) {       
            $row = $result->fetch_assoc();
            $count = $row['total'] ?? 0;
        }   
        return $count;
    }   

    function get_registration_completion($registration_id) {
        global $conn;

        // Select only necessary fields
        $fields = [
            'uli_number', 'entry_date', 'last_name', 'first_name', 'middle_name',
            'mail_number_st', 'mail_district', 'mail_barangay', 'mail_citymun',
            'mail_province', 'mail_region', 'email_facebook', 'contact_no',
            'nationality', 'sex', 'civil_status', 'employment_status',
            'employment_type', 'dob', 'bplace_citymun', 'bplace_province',
            'bplace_region', 'educational_attainment', 'guardian_name',
            'guardian_mailing_address', 'student_classification',
            'other_classification', 'type_disability', 'cause_disability',
            'course_qualification', 'type_scholarship', 'picture'
        ];

        // Build query dynamically (only once)
        $columns = implode(", ", $fields);
        $sql = "SELECT $columns FROM registrations WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $registration_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $registration = $result->fetch_assoc();
        $stmt->close();

        if (!$registration) {
            return 0;
        }

        // Count non-empty fields
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($registration[$field])) {
                $filled++;
            }
        }

        // Compute completion percentage
        return round(($filled / count($fields)) * 100);
    }

?>