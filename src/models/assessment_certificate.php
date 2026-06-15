<?php
    require_once 'db/db.php';

    function get_all_records($filter = [], $pagination = []) {

        global $conn;

        $records = [
            'total'  => 0,
            'result' => []
        ];

        $query = "
            SELECT a.*
            FROM assessment_certificates a
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
                        $searchParts[] = "a.$col LIKE ?";
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
                    $conditions[] = "a.$column BETWEEN ? AND ?";
                    $params[] = $from;
                    $params[] = $to;
                    $types   .= "ss";
                } elseif ($from) {
                    $conditions[] = "a.$column >= ?";
                    $params[] = $from;
                    $types   .= "s";
                } elseif ($to) {
                    $conditions[] = "a.$column <= ?";
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
                        $conditions[] = "a.$column IN ($placeholders)";
                        foreach($input as $val) {
                            $params[] = $val;
                            $types   .= is_numeric($val) ? "i" : "s";
                        }
                    } else {
                        $conditions[] = "a.$column = ?";
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

        $query .= " ORDER BY a.id DESC LIMIT ?, ?";
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

    function get_assessment_certificate_dashboard_stats($import_id) {
        global $conn;

        $stats = [
            'total' => 0,
            // 'assessment_result' => [],
            // 'sex' => [],
            // 'type_of_certificate' => [],
            // 'assessment_center' => []
        ];

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM assessment_certificates WHERE import_id = ?");
        $stmt->bind_param("i", $import_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'] ?? 0;
        $stmt->close();

        foreach (['assessment_result', 'sex', 'type_of_certificate', 'assessment_center', 'nc_title', 'sector'] as $column) {
            $query = "SELECT `$column` AS label, COUNT(*) AS count FROM assessment_certificates WHERE import_id = ? GROUP BY `$column` ORDER BY count DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $import_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $label = $row['label'] !== null && $row['label'] !== '' ? $row['label'] : 'Unknown';
                $stats[$column][$label] = (int) $row['count'];
            }

            $stmt->close();
        }

        return $stats;
    }
?>