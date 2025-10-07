<?php
    require "db/db.php";

    function get_all_roles() {
        global $conn;
        $roles = [];

        $query = "SELECT * FROM roles";

        if ($stmt = $conn->prepare($query)) {
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $roles = $result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }

        return $roles;
    }
?>
