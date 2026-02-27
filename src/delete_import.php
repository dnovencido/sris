<?php
    include "session.php";
    include "models/import.php";
    
    $result = [];

    if(array_key_exists("id", $_GET)) {

        $result['deleted'] = false;
        $is_deleted = delete_import($_GET['id']);

        if($is_deleted) {

        $_SESSION['flash_message'] = [
            'type' => 'success',
            'text' => 'You have successfully deleted the imported data.'
        ];

            $result['deleted'] = true;
        } 
    }

    echo json_encode($result);
?>