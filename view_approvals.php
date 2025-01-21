<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all registrations with their approval status
$sql = "SELECT 
            r.*,
            CASE 
                WHEN r.status = 'REJECTED_BY_HR' THEN 'Rejected by HR'
                WHEN r.status = 'REJECTED_BY_IT' THEN 'Rejected by IT'
                WHEN r.status = 'HR_APPROVED' THEN 'HR Approved'
                WHEN r.status = 'FULLY_APPROVED' THEN 'Fully Approved'
                WHEN h.status IS NULL THEN 'Pending HR Approval'
                WHEN i.status IS NULL THEN 'Pending IT Approval'
                ELSE 'In Progress'
            END as approval_status,
            h.hr_name, h.hr_signature, h.hr_date,
            i.it_name, i.it_signature, i.approval_date as it_date,
            rf.reason as rejection_reason,
            rf.rejected_by,
            rf.rejection_date
        FROM employee_registrations r
        LEFT JOIN hr_approvals h ON r.employid = h.employee_id
        LEFT JOIN it_approvals i ON r.employid = i.employee_id
        LEFT JOIN rejected_forms rf ON r.employid = rf.employee_id
        ORDER BY 
            CASE 
                WHEN r.status LIKE 'REJECTED%' THEN 2
                WHEN r.status = 'FULLY_APPROVED' THEN 1
                ELSE 0
            END,
            r.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Approvals Status</title>
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
            background-color: #9C27B0;
            color: white;
            padding: 25px;
            margin: -20px -20px 20px -20px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        .nav-menu {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .nav-button {
            padding: 10px 20px;
            background-color: #9C27B0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        .nav-button:hover {
            background-color: #7B1FA2;
        }
        .status-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .status-header {
            padding: 15px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .status-details {
            padding: 20px;
            display: none;
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            min-width: 140px;
            display: inline-block;
        }
        .status-approved {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        .status-rejected {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }
        .status-pending {
            background-color: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffe0b2;
        }
        .status-progress {
            background-color: #e3f2fd;
            color: #1976d2;
            border: 1px solid #90caf9;
        }
        .approval-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .approval-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .approval-section h4 {
            color: #9C27B0;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .approval-section p {
            margin: 8px 0;
            color: #333;
        }
        .search-container {
            margin-bottom: 20px;
        }

        .search-box {
            width: 100%;
            padding: 12px;
            border: 2px solid #9C27B0;
            border-radius: 8px;
            font-size: 16px;
            transition: box-shadow 0.3s;
        }

        .search-box:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(156, 39, 176, 0.3);
        }

        .search-box::placeholder {
            color: #999;
        }

        /* Optional: Add this class to hide cards when searching */
        .status-card.hidden {
            display: none;
        }

        .download-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #9C27B0;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .download-btn:hover {
            background-color: #7B1FA2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Registration Approval Status</h2>
        </div>

        <div class="nav-menu">
            <a href="main.html" class="nav-button">Back to Home</a>
        </div>

        <div class="search-container">
            <input type="text" 
                   id="searchInput" 
                   class="search-box" 
                   placeholder="Search by name, ID, or department..."
                   onkeyup="searchApprovals()">
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="status-card">
                    <div class="status-header" onclick="toggleDetails('<?php echo $row['employid']; ?>')">
                        <div class="employee-info">
                            <strong><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></strong>
                            <span class="employee-id">(ID: <?php echo htmlspecialchars($row['employid']); ?>)</span>
                        </div>
                        <span class="status-badge <?php 
                            if (strpos($row['approval_status'], 'Rejected') !== false) {
                                echo 'status-rejected';
                            } elseif ($row['approval_status'] === 'Fully Approved') {
                                echo 'status-approved';
                            } elseif ($row['approval_status'] === 'In Progress') {
                                echo 'status-progress';
                            } else {
                                echo 'status-pending';
                            }
                        ?>">
                            <?php echo htmlspecialchars($row['approval_status']); ?>
                        </span>
                    </div>
                    <div id="details-<?php echo $row['employid']; ?>" class="status-details">
                        <h4>Employee Details</h4>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><strong>Role:</strong> <?php echo htmlspecialchars($row['role']); ?></p>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($row['department']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>

                        <div class="approval-info">
                            <!-- HR Approval Info -->
                            <div class="approval-section">
                                <h4>HR Approval</h4>
                                <?php if ($row['hr_name']): ?>
                                    <p><strong>Approved By:</strong> <?php echo htmlspecialchars($row['hr_name']); ?></p>
                                    <p><strong>Signature:</strong> <?php echo htmlspecialchars($row['hr_signature']); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['hr_date']); ?></p>
                                <?php else: ?>
                                    <p>Pending HR Approval</p>
                                <?php endif; ?>
                            </div>

                            <!-- IT Approval Info -->
                            <div class="approval-section">
                                <h4>IT Approval</h4>
                                <?php if ($row['it_name']): ?>
                                    <p><strong>Approved By:</strong> <?php echo htmlspecialchars($row['it_name']); ?></p>
                                    <p><strong>Signature:</strong> <?php echo htmlspecialchars($row['it_signature']); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['it_date']); ?></p>
                                <?php else: ?>
                                    <p>Pending IT Approval</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (strpos($row['approval_status'], 'Rejected') !== false): ?>
                            <div class="rejection-info">
                                <h4>Rejection Details</h4>
                                <p><strong>Rejected By:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                                <p><strong>Rejection Date:</strong> <?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($row['rejection_date']))); ?></p>
                                <p><strong>Reason:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <?php if ($row['approval_status'] === 'Fully Approved'): ?>
                                <a href="download_registration.php?employee_id=<?php echo $row['employid']; ?>" 
                                   class="download-btn" 
                                   target="_blank">
                                    Download Registration Form
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No registrations found.</p>
        <?php endif; ?>
    </div>

    <script>
    function searchApprovals() {
        const searchInput = document.getElementById('searchInput');
        const filter = searchInput.value.toLowerCase();
        const cards = document.getElementsByClassName('status-card');

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
    </script>
</body>
</html>
<?php $conn->close(); ?> 