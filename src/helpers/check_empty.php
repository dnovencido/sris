<?php
    function display($value) {
        return (!isset($value) || $value === null || $value === '') ? '-' : htmlspecialchars($value);
    }
?>