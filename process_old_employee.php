<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->begin_transaction();

        // Generate employee ID
        $employee_id = 'EMP_' . uniqid();

        // Insert into employee_registrations
        $sql = "INSERT INTO employee_registrations (
            employid, 
            firstname, 
            lastname, 
            email, 
            department, 
            role, 
            location, 
            access, 
            status,
            request_type,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'HR_APPROVED', 'old_employee', NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", 
            $employee_id,
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['email'],
            $_POST['department'],
            $_POST['role'],
            $_POST['location'],
            $_POST['access_rights']
        );
        $stmt->execute();

        // Insert into hr_approvals
        $hr_sql = "INSERT INTO hr_approvals (
            employee_id, 
            hr_name, 
            hr_signature, 
            hr_date
        ) VALUES (?, ?, ?, NOW())";
        
        $hr_stmt = $conn->prepare($hr_sql);
        $hr_signature = $_SESSION['full_name'] . " (Auto-approved)";
        $hr_stmt->bind_param("sss", 
            $employee_id,
            $_SESSION['full_name'],
            $hr_signature
        );
        $hr_stmt->execute();

        $conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Request submitted successfully and pending IT approval.'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Error processing request: ' . $e->getMessage()
        ]);
    }
    exit();
}
?> 