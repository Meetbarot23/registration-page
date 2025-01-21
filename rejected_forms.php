<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all rejected forms with employee details
$sql = "SELECT r.*, e.*, 
        DATE_FORMAT(r.rejection_date, '%d-%m-%Y %H:%i') as formatted_date 
        FROM rejected_forms r
        JOIN employee_registrations e ON r.employee_id = e.employid
        ORDER BY r.rejection_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejected Forms</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #f44336;
            color: white;
            padding: 25px;
            margin: -20px -20px 20px -20px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        .nav-menu {
            margin-bottom: 20px;
        }
        .nav-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        .nav-button:hover {
            background-color: #d32f2f;
        }
        .rejected-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }
        .rejected-header {
            padding: 15px;
            background: #fff3f3;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .rejected-details {
            padding: 20px;
            display: none;
        }
        .rejected-by {
            color: #f44336;
            font-weight: bold;
        }
        .reject-reason {
            background: #fff3f3;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            border: 1px solid #ffcdd2;
        }
        .search-container {
            margin: 20px 0;
        }
        .search-box {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .detail-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        .alert {
            animation: fadeOut 0.5s ease-in-out forwards;
            animation-delay: 3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Rejected Registrations</h2>
        </div>

        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <div class="nav-menu">
            <a href="main.html" class="nav-button">Back to Home</a>
        </div>

        <div class="search-container">
            <input type="text" 
                   id="searchInput" 
                   class="search-box" 
                   placeholder="Search rejected forms..."
                   onkeyup="searchRejected()">
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="rejected-card">
                    <div class="rejected-header" onclick="toggleDetails('<?php echo $row['employid']; ?>')">
                        <div>
                            <strong><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></strong>
                            <span>(ID: <?php echo htmlspecialchars($row['employid']); ?>)</span>
                        </div>
                        <span class="rejected-by">
                            Rejected by <?php echo htmlspecialchars($row['department']); ?>
                        </span>
                    </div>
                    <div id="details-<?php echo $row['employid']; ?>" class="rejected-details">
                        <div class="details-grid">
                            <div class="detail-item">
                                <div class="detail-label">Email</div>
                                <div><?php echo htmlspecialchars($row['email']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Department</div>
                                <div><?php echo htmlspecialchars($row['department']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Role</div>
                                <div><?php echo htmlspecialchars($row['role']); ?></div>
                            </div>
                        </div>
                        
                        <div class="reject-reason">
                            <h4>Rejection Details</h4>
                            <p><strong>Rejected By:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($row['formatted_date']); ?></p>
                            <p><strong>Reason:</strong> <?php echo htmlspecialchars($row['reason']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No rejected forms found.</p>
        <?php endif; ?>
    </div>

    <script>
    function searchRejected() {
        const searchInput = document.getElementById('searchInput');
        const filter = searchInput.value.toLowerCase();
        const cards = document.getElementsByClassName('rejected-card');

        Array.from(cards).forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(filter)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }

    function toggleDetails(id) {
        const detailsDiv = document.getElementById('details-' + id);
        const currentDisplay = detailsDiv.style.display;
        detailsDiv.style.display = currentDisplay === 'block' ? 'none' : 'block';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3500);
        }
    });
    </script>
</body>
</html>
<?php $conn->close(); ?> 