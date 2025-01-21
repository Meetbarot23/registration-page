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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];

    // Update the request status to APPROVED
    $sql = "UPDATE hr_requests SET status = 'APPROVED' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        echo "Request approved successfully.";
    } else {
        echo "Error approving request.";
    }

    $stmt->close();
}

$conn->close();
?>