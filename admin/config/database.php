<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'stikes_mrhj');

try {

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
} catch (Exception $e) {

    die("Database Connection Error. Pastikan database '" . DB_NAME . "' sudah dibuat.");
}
?>
