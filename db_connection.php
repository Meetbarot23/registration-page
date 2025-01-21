<?php
$host = 'localhost';
$username = 'root'; // your database username
$password = ''; // your database password
$database = 'employee_registration'; // your database name

try {
    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?> 