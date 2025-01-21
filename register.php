<?php
// Enable CORS for local testing (optional, remove in production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Set the response header to JSON
header("Content-Type: application/json");

$response = [
    "success" => false,
    "message" => "An error occurred.",
];

try {
    // Database configuration
    $host = "localhost"; // Replace with your DB host
    $user = "root";      // Replace with your DB user
    $database = "employee_registration";
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    

    // Get POST data
    $firstname = $_POST['firstname'] ?? '';
    $middlename = $_POST['middlename'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $employid = $_POST['employid'] ?? '';
    $role = $_POST['role'] ?? '';
    $location = $_POST['location'] ?? '';
    $department = $_POST['department'] ?? '';
    $access = $_POST['access'] ?? [];

    // Validate required fields
    if (empty($firstname) || empty($lastname) || empty($employid) || empty($role) || empty($location) || empty($department)) {
        throw new Exception("All required fields must be filled out.");
    }

    // Prepare the SQL query
    $stmt = $pdo->prepare("
        INSERT INTO employees (firstname, middlename, lastname, employid, role, location, department, access_rights)
        VALUES (:firstname, :middlename, :lastname, :employid, :role, :location, :department, :access_rights)
    ");

    // Serialize access rights array into a string
    $accessRights = implode(", ", $access);

    // Execute the query
    $stmt->execute([
        ':firstname' => $firstname,
        ':middlename' => $middlename,
        ':lastname' => $lastname,
        ':employid' => $employid,
        ':role' => $role,
        ':location' => $location,
        ':department' => $department,
        ':access_rights' => $accessRights,
    ]);

    // Successful response
    $response['success'] = true;
    $response['message'] = "Employee registered successfully!";
} catch (PDOException $e) {
    error_log("SQL Error: " . $e->getMessage());
    throw new Exception("Database error occurred. Please try again later.");
}

// Return the JSON response
echo json_encode($response);
?>
