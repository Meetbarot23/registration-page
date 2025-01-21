<?php
session_start();
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

    // Validate form data
    if (empty($_POST['employee_id']) || empty($_POST['hr_name']) || 
        empty($_POST['hr_signature']) || empty($_POST['hr_date'])) {
        throw new Exception("All fields are required");
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert HR approval
        $sql = "INSERT INTO hr_approvals (employee_id, employee_name, hr_name, hr_signature, hr_date, status) 
                VALUES (?, ?, ?, ?, ?, 'approved')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", 
            $_POST['employee_id'],
            $_POST['employee_name'],
            $_POST['hr_name'],
            $_POST['hr_signature'],
            $_POST['hr_date']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error processing approval");
        }

        // Update employee_registrations status
        $update_sql = "UPDATE employee_registrations 
                       SET status = 'HR_APPROVED' 
                       WHERE employid = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $_POST['employee_id']);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Error updating registration status");
        }

        // Commit transaction
        $conn->commit();
        
        // Redirect to view approvals page with success message
        header("Location: view_approvals.php?status=success&message=HR approval completed successfully");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    header("Location: hr_department.php?status=error&message=" . urlencode($e->getMessage()));
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($update_stmt)) {
        $update_stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 