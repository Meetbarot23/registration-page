<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'request_db'; // Replace with your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$employee_id = $_POST['employee_id'];
$email = $_POST['email'];
$request_type = $_POST['request_type'];

// Insert data into the database
$sql = "INSERT INTO requests (name, employee_id, email, request_type, status) VALUES (?, ?, ?, ?, 'PENDING')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $employee_id, $email, $request_type);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Request submitted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error submitting request.']);
}

$stmt->close();
$conn->close();
?>