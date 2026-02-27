<?php
    require_once 'db/db.php';
    
    function get_all_imports($filter = [], $pagination = []) {

        global $conn;

        $imports = [
            'total'  => 0,
            'result' => []
        ];

        $json_columns = [
            'concerned_division',
            'names_stakeholders',
            'signatories'
        ];

        $query = "
            SELECT i.* FROM import_metadata i
        ";

        $conditions = [];
        $params     = [];
        $types      = "";

        // Build filters
        foreach ($filter as $key => $value) {

            // Search filter
            if ($key === 'search' && is_array($value) && count($value) >= 2) {

                $columns = $value[0];
                $input   = trim($value[1]);

                if ($input !== '') {

                    $searchParts = [];

                    foreach ($columns as $col) {
                        $searchParts[] = "i.$col LIKE ?";
                        $params[] = "%$input%";
                        $types   .= "s";
                    }

                    if (!empty($searchParts)) {
                        $conditions[] = "(" . implode(" OR ", $searchParts) . ")";
                    }
                }

                continue;
            }

            /* ---------- DATE RANGE ---------- */
            if ($key === 'date_range' && is_array($value) && count($value) >= 3) {

                $column = $value[0][0];
                $from   = trim($value[1]);
                $to     = trim($value[2]);

                if ($from && $to) {
                    $conditions[] = "i.$column BETWEEN ? AND ?";
                    $params[] = $from;
                    $params[] = $to;
                    $types   .= "ss";
                } elseif ($from) {
                    $conditions[] = "i.$column >= ?";
                    $params[] = $from;
                    $types   .= "s";
                } elseif ($to) {
                    $conditions[] = "i.$column <= ?";
                    $params[] = $to;
                    $types   .= "s";
                }

                continue;
            }

            // Item filter
            if ($key === 'item' && is_array($value)) {

                foreach ($value as $item) {
                    if (!isset($item['column'], $item['value']) || $item['value'] === '') {
                        continue;
                    }

                    $column = $item['column'];
                    $input  = $item['value'];

                    // Normal columns
                    if (is_array($input)) {

                        $placeholders = implode(',', array_fill(0, count($input), '?'));
                        $conditions[] = "i.$column IN ($placeholders)";

                        foreach ($input as $val) {
                            $params[] = $val;
                            $types   .= is_numeric($val) ? "i" : "s";
                        }

                    } else {

                        $conditions[] = "i.$column = ?";
                        $params[] = $input;
                        $types   .= is_numeric($input) ? "i" : "s";
                    }
                }

                continue;
            }
        }

        // Apply where conditions
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $countQuery = "SELECT COUNT(*) AS total FROM ($query) AS total_records";

        $stmt = $conn->prepare($countQuery);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $countResult = $stmt->get_result()->fetch_assoc();
        $documents['total'] = $countResult['total'] ?? 0;
        $stmt->close();

        // Pagination
        $limit  = $pagination['total_records_per_page'] ?? 20;
        $offset = $pagination['offset'] ?? 0;

        $query .= " ORDER BY i.id DESC LIMIT ?, ?";
        $params[] = (int)$offset;
        $params[] = (int)$limit;
        $types   .= "ii";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $documents['result'][] = $row;
        }

        $stmt->close();

        return $documents;
    }

    function get_import_count() {
        global $conn;
        
        $query = "SELECT COUNT(*) AS total FROM import_metadata";
        $result = $conn->query($query);

        if($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        
        return 0;
    }

    function get_import_by_id($id) {
        global $conn;

        $query = "SELECT * FROM import_metadata WHERE id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $import = $result->fetch_assoc();
        $stmt->close();

        return $import;
    }

    function save_import($fields = [], $id = null) {
        global $conn;
        $flag = false;
    
        // Allowed columns in DB
        $allowed = [
            'name'
         ];
    
        // Filter only allowed keys
        $data = array_intersect_key($fields, array_flip($allowed));
        
        if ($id === null) {
            // Insert
            $data['date_created'] = date("Y-m-d H:i:s");
    
            $columns = array_keys($data);
            $placeholders = implode(",", array_fill(0, count($columns), "?"));
            $sql = "INSERT INTO import_metadata(`" . implode("`,`", $columns) . "`) VALUES ($placeholders)"; 

            $stmt = $conn->prepare($sql);
            $types = str_repeat("s", count($columns));
            $values = array_values($data);
    
            $stmt->bind_param($types, ...$values);
        } else {
            $set = implode(", ", array_map(fn($col) => "`$col` = ?", array_keys($data)));

            $sql = "UPDATE import_metadata SET $set WHERE id = ?";
    
            $stmt = $conn->prepare($sql);
    
            $types = str_repeat("s", count($data)) . "i"; // last one is for id
            $values = array_merge(array_values($data), [$id]);
            
            $stmt->bind_param($types, ...$values);
        }
        
        if($stmt->execute())
            $flag = true;
        
        return $flag;
    }    

    function delete_import($id) {
        global $conn;
        $flag = false;

        $stmt = $conn->prepare("SELECT id FROM `import_metadata` WHERE id = ?");

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM `import_metadata` WHERE id = ?");
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
?>