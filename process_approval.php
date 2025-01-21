<?php
require_once 'session_check.php';

// Database connection configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

try {
    // Create database connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $department = $_SESSION['department'];
        
        if ($department === 'it') {
            // Process IT approval
            $it_name = $conn->real_escape_string($_POST['it_name']);
            $it_date = $conn->real_escape_string($_POST['it_date']);
            
            $sql = "INSERT INTO it_approvals (it_name, approval_date) VALUES (?, ?)";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $it_name, $it_date);
                if ($stmt->execute()) {
                    header("Location: it_department.php?status=success");
                } else {
                    throw new Exception("Failed to save approval");
                }
                $stmt->close();
            }
        }
    }
} catch (Exception $e) {
    header("Location: " . $_SESSION['department'] . "_department.php?error=" . urlencode($e->getMessage()));
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 