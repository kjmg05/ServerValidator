<?php
    require_once "./config/config.php";

    $sql = "SELECT email FROM users";
    $all_emails = "";

    if ($result = $mysqli->query($sql)) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array()) {
                $all_emails.=($row['email']).", ";
            }
        }
    }
    
    $all = substr($all_emails, 0, -2);
?>