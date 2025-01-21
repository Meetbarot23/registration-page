<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'employee_registration'; // Ensure this is your correct database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'] ?? '';
$employee_id = $_POST['employee_id'] ?? '';
$request_type = 'Old Employee';
$message = $_POST['additional_info'] ?? '';

// Insert data into the IT requests table
$sql = "INSERT INTO it_requests (name, employee_id, request_type, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssss", $name, $employee_id, $request_type, $message);

if ($stmt->execute()) {
    echo "Request submitted successfully to IT.";
} else {
    echo "Error submitting request to IT.";
}

$stmt->close();
$conn->close();
?>