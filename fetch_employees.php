<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT r.*, 
        CASE 
            WHEN r.status = 'REJECTED_BY_HR' THEN 'Rejected by HR'
            WHEN r.status = 'REJECTED_BY_IT' THEN 'Rejected by IT'
            WHEN r.status = 'FULLY_APPROVED' THEN 'Approved'
            WHEN r.status = 'HR_APPROVED' THEN 'HR Approved'
            ELSE 'Pending'
        END as status_text,
        rf.reason as rejection_reason,
        rf.rejected_by,
        rf.rejection_date,
        COALESCE(rf.department, '') as rejected_department
        FROM employee_registrations r
        LEFT JOIN rejected_forms rf ON r.employid = rf.employee_id
        ORDER BY 
            CASE 
                WHEN r.status LIKE 'REJECTED%' THEN 2
                WHEN r.status = 'FULLY_APPROVED' THEN 1
                ELSE 0
            END,
            r.created_at DESC";

$result = $conn->query($sql);

$employees = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($employees);

$conn->close();
?> 