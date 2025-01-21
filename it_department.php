<?php
require_once 'session_check.php';
if ($_SESSION['department'] != 'it') {
    header("Location: approval_login.html");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$database = "employee_registration";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch pending IT requests including old employee requests
$sql = "SELECT r.*, e.firstname, e.lastname, e.email, e.department, e.role, e.location, e.access,
        h.hr_name, h.hr_signature, h.hr_date 
        FROM employee_registrations e
        INNER JOIN hr_approvals h ON e.employid = h.employee_id
        LEFT JOIN it_approvals i ON e.employid = i.employee_id
        WHERE e.status = 'HR_APPROVED' 
        AND i.id IS NULL
        AND e.employid NOT IN (SELECT employee_id FROM rejected_forms)
        ORDER BY e.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Department Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .department-header {
            background-color: #2196F3;
            color: white;
            padding: 25px;
            margin: -20px -20px 20px -20px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        .department-header h2 {
            margin: 0;
            font-size: 28px;
        }
        .nav-menu {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-bottom: 30px;
        }
        .nav-button {
            padding: 12px 25px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
            font-weight: 500;
        }
        .nav-button:hover {
            background-color: #d32f2f;
        }
        .employee-list {
            margin: 30px 0;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        .employee-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .employee-header {
            padding: 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .employee-name {
            font-size: 18px;
            font-weight: 500;
            color: #2c3e50;
        }
        .employee-id {
            color: #666;
            font-size: 14px;
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
        }
        .employee-details {
            padding: 25px;
            display: none;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .detail-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .detail-value {
            color: #2c3e50;
            font-size: 16px;
        }
        .hr-approval-details {
            margin: 25px 0;
            padding: 20px;
            background: #e3f2fd;
            border-radius: 8px;
            border: 1px solid #bbdefb;
        }
        .hr-approval-details h4 {
            color: #1976d2;
            margin: 0 0 15px 0;
        }
        .it-approval-section {
            margin-top: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #dee2e6;
        }
        .approval-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 0;
        }
        .approval-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .approval-item label {
            font-size: 14px;
            color: #495057;
            font-weight: 600;
        }
        .approval-item input {
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 15px;
        }
        .approval-action {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .approve-btn {
            background-color: #2196F3;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .approve-btn:hover {
            background-color: #1976D2;
        }
        .section-header {
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            color: #2c3e50;
            font-size: 24px;
        }
        .no-records {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
            font-size: 16px;
            margin: 20px 0;
        }
        .messages-container {
            margin-bottom: 20px;
        }
        .success-message, .error-message {
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 10px 0;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn {
            background-color: #00796b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #004d40;
        }
        .request-button {
        background-color: #4CAF50; /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        transition: background-color 0.3s ease;
    }

    .request-button:hover {
        background-color: #45a049; /* Darker green */
    }
        
        /* New styles for approval buttons */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .approve-btn, .reject-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .approve-btn {
            background-color: #2ecc71;
            color: white;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2);
        }

        .approve-btn:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.3);
        }

        .reject-btn {
            background-color: #e74c3c;
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
        }

        .reject-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        .confirm-reject-btn {
            background-color: #e74c3c;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .cancel-btn {
            background-color: #95a5a6;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .approval-form {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .approval-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 0;
        }

        .approval-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .approval-item label {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .approval-item input {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        .approval-item input:focus {
            outline: none;
            border-color: #2196F3;
        }

        .approval-item.full-width {
            grid-column: 1 / -1;
        }

        .approval-item textarea {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            resize: vertical;
            min-height: 100px;
            transition: border-color 0.3s ease;
        }

        .approval-item textarea:focus {
            outline: none;
            border-color: #2196F3;
        }

        /* Button icons */
        .button-icon {
            font-size: 18px;
        }

        .badge {
            background-color: #3498db;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }

        .employee-card {
            margin-bottom: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: white;
        }

        .employee-header {
            padding: 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .employee-details {
            padding: 20px;
            display: none;
        }

        .hr-approval-details {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .it-approval-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .approval-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .approve-btn, .reject-btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .approve-btn {
            background: #2ecc71;
            color: white;
        }

        .reject-btn {
            background: #e74c3c;
            color: white;
        }

        .approve-btn:hover, .reject-btn:hover {
            transform: translateY(-2px);
        }

        .request-messages {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .message-item {
            padding: 15px;
            border-left: 4px solid #3498db;
            background: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 0 8px 8px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease;
        }

        .message-item:hover {
            transform: translateX(5px);
        }

        .message-item.new-request {
            border-left-color: #2ecc71;
        }

        .message-item.old-request {
            border-left-color: #3498db;
        }

        .message-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .message-content i {
            font-size: 18px;
            color: #2c3e50;
        }

        .message-time {
            font-size: 12px;
            color: #7f8c8d;
        }

        /* Animation for new messages */
        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .message-item:first-child {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="department-header">
            <h2>IT Department Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
            <button class="btn" onclick="window.location.href='view_it_requests.php'">Request</button>
        </div>
        

        <div class="nav-menu">
            <a href="logout.php" class="nav-button">Logout</a>
        </div>

        <div class="messages-container">
            <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
                <div class="<?php echo $_GET['status'] === 'success' ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
        </div>

        
            <h3 class="section-header">Pending IT Approvals</h3>
        <div class="employee-list">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="employee-card">
                        <div class="employee-header" onclick="toggleDetails('<?php echo $row['employid']; ?>')">
                            <div class="employee-name">
                                <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>
                                <?php if ($row['request_type'] === 'old_employee'): ?>
                                    <span class="badge">Old Employee</span>
                                <?php endif; ?>
                            </div>
                            <div class="employee-id">
                                ID: <?php echo htmlspecialchars($row['employid']); ?>
                            </div>
                        </div>
                        
                        <div id="details-<?php echo $row['employid']; ?>" class="employee-details">
                            <div class="details-grid">
                                <div class="detail-item">
                                    <div class="detail-label">Full Name</div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($row['email']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Role</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($row['role']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Department</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($row['department']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Location</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($row['location']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Access Rights</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($row['access'] ?? 'None'); ?></div>
                                </div>
                            </div>

                            <div class="hr-approval-details">
                                <h4>HR Approval Information</h4>
                                <div class="details-grid">
                                    <div class="detail-item">
                                        <div class="detail-label">HR Name</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($row['hr_name']); ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">HR Signature</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($row['hr_signature']); ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">HR Approval Date</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($row['hr_date']); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="it-approval-section">
                                <h4>IT Approval</h4>
                                <form action="process_it_approval.php" method="POST" class="approval-form">
                                    <input type="hidden" name="employee_id" value="<?php echo $row['employid']; ?>">
                                    <div class="approval-grid">
                                        <div class="approval-item">
                                            <label>IT Name</label>
                                            <input type="text" name="it_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" readonly>
                                        </div>
                                        <div class="approval-item">
                                            <label>Signature</label>
                                            <input type="text" name="it_signature" required placeholder="Enter your signature">
                                        </div>
                                        <div class="approval-item">
                                            <label>Date</label>
                                            <input type="date" name="it_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="button-group">
                                        <button type="submit" class="approve-btn">
                                            <i class="fas fa-check"></i>
                                            Approve Request
                                        </button>
                                        <button type="button" class="reject-btn" onclick="showRejectForm('<?php echo $row['employid']; ?>')">
                                            <i class="fas fa-times"></i>
                                            Reject Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-records">No pending IT approvals</p>
            <?php endif; ?>
        </div>

        <!-- Add this section for request messages -->
        <div class="request-messages">
            <?php
            // Fetch request messages
            $message_sql = "SELECT rm.*, er.request_type 
                           FROM request_messages rm 
                           INNER JOIN employee_registrations er ON rm.request_id = er.employid 
                           WHERE er.status IN ('PENDING_HR', 'HR_APPROVED') 
                           ORDER BY rm.created_at DESC 
                           LIMIT 10";
            $message_result = $conn->query($message_sql);
            ?>
            
            <?php if ($message_result && $message_result->num_rows > 0): ?>
                <?php while($message = $message_result->fetch_assoc()): ?>
                    <div class="message-item <?php echo $message['request_type'] === 'new_employee' ? 'new-request' : 'old-request'; ?>">
                        <div class="message-content">
                            <i class="fas <?php echo $message['request_type'] === 'new_employee' ? 'fa-user-plus' : 'fa-user'; ?>"></i>
                            <span><?php echo htmlspecialchars($message['message']); ?></span>
                        </div>
                        <div class="message-time">
                            <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleDetails(employeeId) {
        const detailsDiv = document.getElementById('details-' + employeeId);
        if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
            detailsDiv.style.display = 'block';
        } else {
            detailsDiv.style.display = 'none';
        }
    }

    function showRejectForm(employeeId) {
        // Implementation for reject form
    }

    function updateMessages() {
        fetch('get_latest_messages.php')
            .then(response => response.json())
            .then(data => {
                if (data.messages) {
                    const messagesContainer = document.querySelector('.request-messages');
                    data.messages.forEach(message => {
                        // Check if message already exists
                        if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                            const messageElement = createMessageElement(message);
                            messagesContainer.insertBefore(messageElement, messagesContainer.firstChild);
                        }
                    });
                }
            });
    }

    function createMessageElement(message) {
        const div = document.createElement('div');
        div.className = `message-item ${message.request_type === 'new_employee' ? 'new-request' : 'old-request'}`;
        div.setAttribute('data-message-id', message.id);
        
        div.innerHTML = `
            <div class="message-content">
                <i class="fas ${message.request_type === 'new_employee' ? 'fa-user-plus' : 'fa-user'}"></i>
                <span>${message.message}</span>
            </div>
            <div class="message-time">
                ${new Date(message.created_at).toLocaleString()}
            </div>
        `;
        
        return div;
    }

    // Update messages every 30 seconds
    setInterval(updateMessages, 30000);
    </script>
</body>
</html>
<?php $conn->close();?> 