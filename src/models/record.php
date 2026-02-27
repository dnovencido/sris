<?php
    require_once 'db/db.php';
    
    function get_all_records($filter = [], $pagination = []) {

        global $conn;

        $records = [
            'total'  => 0,
            'result' => []
        ];

        $query = "
            SELECT r.*
            FROM records r
        ";

        $conditions = [];
        $params     = [];
        $types      = "";

        // BUILD FILTERS
        foreach($filter as $key => $value) {

            // SEARCH
            if($key === 'search' && is_array($value) && count($value) >= 2) {
                $columns = $value[0];
                $input   = trim($value[1]);
                if($input !== '') {
                    $searchParts = [];
                    foreach ($columns as $col) {
                        $searchParts[] = "r.$col LIKE ?";
                        $params[] = "%$input%";
                        $types   .= "s";
                    }
                    if(!empty($searchParts)) {
                        $conditions[] = "(" . implode(" OR ", $searchParts) . ")";
                    }
                }

                continue;
            }

            /* ---------- DATE RANGE ---------- */
            if($key === 'date_range' && is_array($value) && count($value) >= 3) {

                $column = $value[0][0];
                $from   = trim($value[1]);
                $to     = trim($value[2]);

                if ($from && $to) {
                    $conditions[] = "d.$column BETWEEN ? AND ?";
                    $params[] = $from;
                    $params[] = $to;
                    $types   .= "ss";
                } elseif ($from) {
                    $conditions[] = "d.$column >= ?";
                    $params[] = $from;
                    $types   .= "s";
                } elseif ($to) {
                    $conditions[] = "d.$column <= ?";
                    $params[] = $to;
                    $types   .= "s";
                }

                continue;
            }

            /* ---------- ITEM FILTER ---------- */
            if($key === 'item' && is_array($value)) {

                foreach ($value as $item) {
                    if(!isset($item['column'], $item['value']) || $item['value'] === '') {
                        continue;
                    }

                    $column = $item['column'];
                    $input  = $item['value'];

                    /* NORMAL COLUMN FILTER */
                    if(is_array($input)) {
                        $placeholders = implode(',', array_fill(0, count($input), '?'));
                        $conditions[] = "r.$column IN ($placeholders)";
                        foreach($input as $val) {
                            $params[] = $val;
                            $types   .= is_numeric($val) ? "i" : "s";
                        }
                    } else {
                        $conditions[] = "r.$column = ?";
                        $params[] = $input;
                        $types   .= is_numeric($input) ? "i" : "s";
                    }
                }
                continue;
            }
        }

        // APPLY WHERE
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // COUNT TOTAL (SAFE)
        $countQuery = "SELECT COUNT(*) AS total FROM ($query) AS total_records";

        $stmt = $conn->prepare($countQuery);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $countResult = $stmt->get_result()->fetch_assoc();
        $records['total'] = $countResult['total'] ?? 0;
        $stmt->close();

        // Pagination
        $limit  = $pagination['total_records_per_page'] ?? 20;
        $offset = $pagination['offset'] ?? 0;

        $query .= " ORDER BY r.id DESC LIMIT ?, ?";
        $params[] = (int)$offset;
        $params[] = (int)$limit;
        $types   .= "ii";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $records['result'][] = $row;
        }

        $stmt->close();

        return $records;
    }

    function get_record_count() {
        global $conn;
        
        $query = "SELECT COUNT(*) AS total FROM records";
        $result = $conn->query($query);

        if($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        
        return 0;
    }

    function count_delivery_mode($id) {

        global $conn;

        $query = "
            SELECT 
                -- IBT
                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' 
                    AND sex = 'Male' THEN 1 ELSE 0 
                END) AS ibt_enrolled_m,

                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' 
                    AND sex = 'Female' THEN 1 ELSE 0 
                END) AS ibt_enrolled_f,

                -- CBT
                SUM(CASE 
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' 
                    AND sex = 'Male' THEN 1 ELSE 0 
                END) AS cbt_enrolled_m,

                SUM(CASE 
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' 
                    AND sex = 'Female' THEN 1 ELSE 0 
                END) AS cbt_enrolled_f,
                
                -- IBT Graduates
                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' AND date_finished IS NOT NULL
                    AND sex = 'Male' THEN 1 ELSE 0 
                END) AS ibt_graduate_m,

                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' AND date_finished IS NOT NULL
                    AND sex = 'Female' THEN 1 ELSE 0 
                END) AS ibt_graduate_f,

                -- CBT Graduates
                SUM(CASE 
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' AND date_finished IS NOT NULL
                    AND sex = 'Male' THEN 1 ELSE 0 
                END) AS cbt_graduate_m,

                SUM(CASE 
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' AND date_finished IS NOT NULL
                    AND sex = 'Female' THEN 1 ELSE 0 
                END) AS cbt_graduate_f,
                
                -- IBT Asssesed
                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' AND date_assessed IS NOT NULL
                    AND sex = 'Male' THEN 1 ELSE 0 
                END) AS ibt_assessed_m,
                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' AND date_assessed IS NOT NULL
                    AND sex = 'Female' THEN 1 ELSE 0 
                END) AS ibt_assessed_f,

                -- CBT Assessed
                SUM(CASE 
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' AND date_assessed IS NOT NULL
                    AND sex = 'Male' THEN 1 ELSE 0 
                END) AS cbt_assessed_m,
                SUM(CASE  
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' AND date_assessed IS NOT NULL
                    AND sex = 'Female' THEN 1 ELSE 0 
                END) AS cbt_assessed_f,
                
                -- IBT Certified
                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' AND assessment_results IS NOT NULL 
                    AND sex = 'Male' THEN 1 ELSE 0 
                END) AS ibt_certified_m,
                SUM(CASE 
                    WHEN delivery_mode = 'Institution-Based Training (IBT)' AND assessment_results IS NOT NULL 
                    AND sex = 'Female' THEN 1 ELSE 0 
                END) AS ibt_certified_f,

                -- CBT Certified
                SUM(CASE 
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' AND assessment_results IS NOT NULL   
                    AND sex = 'Male' THEN 1 ELSE 0  
                END) AS cbt_certified_m,
                SUM(CASE 
                    WHEN delivery_mode = 'Competency-Based Training (CBT)' AND assessment_results IS NOT NULL   
                    AND sex = 'Female' THEN 1 ELSE 0  
                END) AS cbt_certified_f 

            FROM records 
            WHERE import_id = ?
        ";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception($conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->close();

        return $data;
    }

?>