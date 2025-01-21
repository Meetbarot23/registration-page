<?php
session_start();

// Database connection configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

try {
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['department']) || empty($_POST['username']) || empty($_POST['password'])) {
            throw new Exception("All fields are required");
        }

        $department = $conn->real_escape_string(trim($_POST['department']));
        $username = $conn->real_escape_string(trim($_POST['username']));
        $password = trim($_POST['password']);

        // Validate department
        if (!in_array($department, ['hr', 'it'])) {
            throw new Exception("Invalid department selected");
        }

        // Check credentials
        if (($department === 'it' && $username === 'it_admin' && $password === 'it123') ||
            ($department === 'hr' && $username === 'hr_admin' && $password === 'hr123')) {
            
            $_SESSION['logged_in'] = true;
            $_SESSION['department'] = $department;
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = ($department === 'it') ? 'IT Administrator' : 'HR Administrator';
            $_SESSION['last_activity'] = time();

            // Redirect based on department
            if ($department === 'it') {
                header("Location: it_department.php");
            } else {
                header("Location: hr_department.php");
            }
            exit();
        } else {
            throw new Exception("Invalid username or password");
        }
    }
} catch (Exception $e) {
    header("Location: approval_login.html?error=" . urlencode($e->getMessage()));
    exit();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 