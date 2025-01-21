<?php
session_start();
require_once 'db_connection.php';

try {
    $sql = "SELECT rm.*, er.request_type 
            FROM request_messages rm 
            INNER JOIN employee_registrations er ON rm.request_id = er.employid 
            WHERE er.status IN ('PENDING_HR', 'HR_APPROVED') 
            AND rm.created_at > ?
            ORDER BY rm.created_at DESC 
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    $last_check = isset($_GET['last_check']) ? $_GET['last_check'] : date('Y-m-d H:i:s', strtotime('-1 minute'));
    $stmt->bind_param("s", $last_check);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $messages = [];
    
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'messages' => $messages
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error fetching messages: ' . $e->getMessage()
    ]);
}
?> 