<?php
    require "db/db.php";
    include_once "config/default.php";

    function check_existing_email($email) {
        global $conn;
        $flag = false;

        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $flag = true;
        }

        $stmt->close();
        return $flag;
    }

    function save_registration($fname, $mname, $lname, $email, $password) {
        global $conn;
        $user = [];

        // Step 1: Insert with plain password first
        $date_created = date('Y-m-d H:i:s');
        $sql = "INSERT INTO users (fname, mname, lname, email, password, date_created) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $fname, $mname, $lname, $email, $password, $date_created);

        if ($stmt->execute()) {
            $id = $conn->insert_id;
            $stmt->close();

            // Step 2: Encrypt password and update
            $encrypted_password = md5(md5($id . $password));
            $sql = "UPDATE users SET password = ? WHERE id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $encrypted_password, $id);

            if ($stmt->execute()) {
                $stmt->close();

                // Step 3: Fetch user
                $sql = "SELECT id, fname, email FROM users WHERE id = ? AND password = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $id, $encrypted_password);
                $stmt->execute();

                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $user = [
                        'id' => $row['id'],
                        'fname' => $row['fname'],
                        'email' => $row['email']
                    ];
                }
                $stmt->close();
            }
        }

        return $user;
    }
?>
