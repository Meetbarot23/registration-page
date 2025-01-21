<?php
require_once 'session_check.php';

if ($_SESSION['department'] != 'hr') {
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

    // Validate required fields
    $required_fields = ['employee_id', 'employee_name', 'reject_reason'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("All fields are required");
        }
    }

    // Validate rejection reason length
    if (strlen(trim($_POST['reject_reason'])) < 20) {
        throw new Exception("Rejection reason must be at least 20 characters long");
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Check if the registration is already processed
        $check_sql = "SELECT status FROM employee_registrations WHERE employid = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $_POST['employee_id']);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $current_status = $result->fetch_assoc()['status'];

        if ($current_status === 'REJECTED_BY_HR' || $current_status === 'REJECTED_BY_IT' || $current_status === 'FULLY_APPROVED') {
            throw new Exception("This registration has already been processed");
        }

        // Insert rejection record
        $sql = "INSERT INTO rejected_forms (
                    employee_id, 
                    employee_name, 
                    rejected_by, 
                    department, 
                    reason, 
                    rejection_date
                ) VALUES (?, ?, ?, 'HR', ?, NOW())";
        
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
        $update_sql = "UPDATE employee_registrations 
                      SET status = 'REJECTED_BY_HR',
                          last_updated = NOW() 
                      WHERE employid = ?";
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
        
        // Redirect with success message
        header("Location: hr_department.php?status=success&message=" . urlencode("Registration rejected successfully"));
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    header("Location: hr_department.php?status=error&message=" . urlencode($e->getMessage()));
    exit();
} finally {
    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    if (isset($update_stmt) && $update_stmt) {
        $update_stmt->close();
    }
    if (isset($check_stmt) && $check_stmt) {
        $check_stmt->close();
    }
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?> 