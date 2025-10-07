<?php
    function renderProfile($value, $size=40) {
        if (!empty($value['picture'])) {
            return '<div style="width:'.$size.'px;height:'.$size.'px;"><img src="data:image/jpeg;base64,' . base64_encode($value['picture']) . '" alt="Profile" class="picture"></div>';
        } else {
            $first = !empty($value['first_name']) ? strtoupper(substr($value['first_name'], 0, 1)) : '';
            $last  = !empty($value['last_name']) ? strtoupper(substr($value['last_name'], 0, 1)) : '';
            $initials = $first . $last;
            if ($initials === '') $initials = '?';

            return '<div class="profile-initials" style="width:'.$size.'px;height:'.$size.'px;">' . $initials . '</div>';
        }
    }
?>