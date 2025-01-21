<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'session_check.php';

if ($_SESSION['department'] != 'it') {
    header("Location: approval_login.html");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

try {
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Validate form data
    if (empty($_POST['employee_id']) || empty($_POST['it_name']) || 
        empty($_POST['it_signature']) || empty($_POST['it_date'])) {
        throw new Exception("All fields are required");
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Check if HR has approved first
        $check_sql = "SELECT * FROM hr_approvals WHERE employee_id = ? AND status = 'approved'";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $check_stmt->bind_param("s", $_POST['employee_id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception("HR approval is required first");
        }

        // Insert IT approval
        $insert_sql = "INSERT INTO it_approvals (
            employee_id, 
            employee_name, 
            it_name, 
            it_signature, 
            approval_date, 
            status
        ) VALUES (?, ?, ?, ?, ?, 'approved')";
        
        $insert_stmt = $conn->prepare($insert_sql);
        if (!$insert_stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $insert_stmt->bind_param("sssss", 
            $_POST['employee_id'],
            $_POST['employee_name'],
            $_POST['it_name'],
            $_POST['it_signature'],
            $_POST['it_date']
        );

        if (!$insert_stmt->execute()) {
            throw new Exception("Error inserting IT approval: " . $insert_stmt->error);
        }

        // Update employee registration status
        $update_sql = "UPDATE employee_registrations SET status = 'FULLY_APPROVED' WHERE employid = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $update_stmt->bind_param("s", $_POST['employee_id']);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Error updating status: " . $update_stmt->error);
        }

        // If everything is successful, commit the transaction
        $conn->commit();
        
        // Redirect to view approvals page
        header("Location: view_approvals.php?status=success&message=IT approval completed successfully");
        exit();

    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    header("Location: it_department.php?status=error&message=" . urlencode($e->getMessage()));
    exit();
} finally {
    // Close all statements and connection
    if (isset($check_stmt) && $check_stmt) {
        $check_stmt->close();
    }
    if (isset($insert_stmt) && $insert_stmt) {
        $insert_stmt->close();
    }
    if (isset($update_stmt) && $update_stmt) {
        $update_stmt->close();
    }
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?> 