<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_registration";

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare SQL statement
    $stmt = $conn->query("SELECT * FROM employees ORDER BY id DESC");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .back-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <a href="index.html" class="back-btn">Back to Main Page</a>
    <h2>Employee List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Employee ID</th>
            <th>Role</th>
            <th>Location</th>
            <th>Department</th>
            <th>Email</th>
        </tr>
        <?php foreach($employees as $employee): ?>
        <tr>
            <td><?php echo htmlspecialchars($employee['id']); ?></td>
            <td><?php echo htmlspecialchars($employee['firstname']); ?></td>
            <td><?php echo htmlspecialchars($employee['middlename']); ?></td>
            <td><?php echo htmlspecialchars($employee['lastname']); ?></td>
            <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
            <td><?php echo htmlspecialchars($employee['role']); ?></td>
            <td><?php echo htmlspecialchars($employee['location']); ?></td>
            <td><?php echo htmlspecialchars($employee['department']); ?></td>
            <td><?php echo htmlspecialchars($employee['email']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html> 