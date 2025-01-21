<?php
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

    if (empty($_POST['employee_id']) || empty($_POST['reject_reason'])) {
        throw new Exception("All fields are required");
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert rejection record
        $sql = "INSERT INTO rejected_forms (employee_id, employee_name, rejected_by, department, reason, rejection_date) 
                VALUES (?, ?, ?, 'IT', ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("ssss", 
            $_POST['employee_id'],
            $_POST['employee_name'],
            $_SESSION['full_name'],
            $_POST['reject_reason']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error recording rejection: " . $stmt->error);
        }

        // Update employee_registrations status
        $update_sql = "UPDATE employee_registrations SET status = 'REJECTED_BY_IT' WHERE employid = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $update_stmt->bind_param("s", $_POST['employee_id']);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Error updating status: " . $update_stmt->error);
        }

        $conn->commit();
        header("Location: rejected_forms.php?status=success&message=Registration rejected successfully");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    header("Location: it_department.php?status=error&message=" . urlencode($e->getMessage()));
    exit();
} finally {
    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    if (isset($update_stmt) && $update_stmt) {
        $update_stmt->close();
    }
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?> 