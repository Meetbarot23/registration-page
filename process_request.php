<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Begin transaction
        $conn->begin_transaction();

        // Generate unique request ID
        $request_id = 'REQ_' . date('Ymd') . '_' . uniqid();

        // Validate required fields
        $required_fields = ['request_type', 'priority', 'description', 'department', 'location'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields");
            }
        }

        // Insert into requests table
        $sql = "INSERT INTO requests (
            request_id,
            request_type,
            priority,
            description,
            department,
            location,
            status,
            created_by,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'PENDING', ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss",
            $request_id,
            $_POST['request_type'],
            $_POST['priority'],
            $_POST['description'],
            $_POST['department'],
            $_POST['location'],
            $_SESSION['user_id'] ?? 'SYSTEM'
        );

        if (!$stmt->execute()) {
            throw new Exception("Error submitting request: " . $stmt->error);
        }

        // Insert notification for IT department
        $notification_sql = "INSERT INTO it_notifications (
            request_id,
            notification_type,
            message,
            status,
            created_at
        ) VALUES (?, 'new_request', 'New request requires attention', 'unread', NOW())";

        $notify_stmt = $conn->prepare($notification_sql);
        $notify_stmt->bind_param("s", $request_id);
        
        if (!$notify_stmt->execute()) {
            throw new Exception("Error creating notification: " . $notify_stmt->error);
        }

        // Commit transaction
        $conn->commit();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Request submitted successfully.',
            'request_id' => $request_id
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit();
}
?> 