<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['department'] !== 'it') {
    echo json_encode(['error' => 'Unauthorized access']);
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

    if (!isset($_GET['search'])) {
        throw new Exception("Search term is required");
    }

    $search = $conn->real_escape_string($_GET['search']);

    // First try exact match with employee ID
    $sql = "SELECT * FROM employee_registrations WHERE employid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    // If no result, try name search
    if ($result->num_rows === 0) {
        $sql = "SELECT * FROM employee_registrations 
                WHERE CONCAT(firstname, ' ', COALESCE(middlename, ''), ' ', lastname) LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchLike = "%$search%";
        $stmt->bind_param("s", $searchLike);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    if ($result->num_rows === 0) {
        throw new Exception("Employee not found");
    }

    $employee = $result->fetch_assoc();

    // Get HR approval information
    $sql = "SELECT * FROM hr_approvals WHERE employee_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $employee['employid']);
    $stmt->execute();
    $hr_result = $stmt->get_result();
    $hr_approval = $hr_result->fetch_assoc();

    // Prepare response data
    $response = [
        'employee' => [
            'employid' => $employee['employid'],
            'firstname' => $employee['firstname'],
            'middlename' => $employee['middlename'],
            'lastname' => $employee['lastname'],
            'email' => $employee['email'],
            'phone' => $employee['phone'],
            'position' => $employee['position'],
            'department' => $employee['department'],
            'joining_date' => $employee['joining_date']
        ],
        'hr_approval' => $hr_approval
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 