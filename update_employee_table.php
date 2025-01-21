<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE employee_registrations 
        ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Table employee_registrations updated successfully";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?> 