<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS rejected_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    employee_name VARCHAR(100) NOT NULL,
    rejected_by VARCHAR(100) NOT NULL,
    department VARCHAR(20) NOT NULL,
    reason TEXT NOT NULL,
    rejection_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table rejected_forms created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 