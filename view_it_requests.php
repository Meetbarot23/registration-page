<?php
session_start();

// Check if the user is logged in and belongs to the IT department
if (!isset($_SESSION['logged_in']) || $_SESSION['department'] != 'it') {
    header("Location: approval_login.html");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch IT requests
$sql = "SELECT * FROM it_requests WHERE status = 'PENDING'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View IT Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .request-header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .request-list {
            list-style-type: none;
            padding: 0;
        }
        .request-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="request-header">
            <h2>IT Department Requests</h2>
        </div>
        <ul class="request-list">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <li class="request-item">
                        <strong>ID:</strong> <?php echo htmlspecialchars($row['request_id']); ?><br>
                        <strong>Request:</strong> <?php echo htmlspecialchars($row['request_details']); ?><br>
                        <strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No pending requests found.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>

<?php
$conn->close();
?>