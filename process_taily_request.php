<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Your existing processing logic here
        
        // Return JSON response
        echo json_encode([
            'status' => 'success',
            'message' => 'Your Taily request has been submitted successfully and is pending approval.',
            'request_id' => $request_id // if you generate one
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit();
}
?> 