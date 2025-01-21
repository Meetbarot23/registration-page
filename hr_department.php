<?php
require_once 'session_check.php';
if ($_SESSION['department'] != 'hr') {
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

// Fetch pending registrations
$sql = "SELECT r.* FROM employee_registrations r 
        WHERE r.status = 'PENDING'
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);

// Fetch pending HR requests
$request_sql = "SELECT * FROM employee_registrations 
               WHERE status = 'PENDING_HR' 
               ORDER BY created_at DESC";
                   
$request_result = $conn->query($request_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Department Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .department-header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .nav-menu {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .nav-button {
            padding: 15px 30px;
            background-color: #00796b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
        }
        .nav-button:hover {
            background-color: #004d40;
            transform: translateY(-2px);
        }
        .approval-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .it-section {
            border-left-color: #f44336;
            opacity: 0.7;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .disabled-input {
            background-color: #eee;
            cursor: not-allowed;
        }
        .registrations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .registrations-table th,
        .registrations-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .registrations-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .registrations-table tr:hover {
            background-color: #f9f9f9;
        }
        .submit-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .employee-select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .approval-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #4CAF50;
        }
        .employee-card {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .approve-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .approve-btn:hover {
            background-color: #45a049;
        }
        .messages-container {
            margin-bottom: 20px;
        }
        .approval-message {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }
        .message {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
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
        .employee-card {
            position: relative;
        }
        .approval-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .employee-list {
            margin: 20px 0;
        }
        .employee-card {
            background: white;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .employee-header {
            padding: 15px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .employee-details {
            padding: 15px;
            display: none;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .detail-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #333;
        }
        .approval-form {
            background: #fff;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-top: 15px;
        }
        .reject-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            margin-left: 10px;
            cursor: pointer;
        }
        .reject-form {
            display: none;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ffcdd2;
            margin-top: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .reject-form h4 {
            color: #d32f2f;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .reject-reason {
            margin-bottom: 20px;
        }
        .reject-reason textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 100px;
            font-size: 14px;
            resize: vertical;
        }
        .reject-reason textarea:focus {
            outline: none;
            border-color: #f44336;
            box-shadow: 0 0 5px rgba(244, 67, 54, 0.2);
        }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .confirm-reject-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .confirm-reject-btn:hover {
            background-color: #d32f2f;
        }
        .cancel-btn {
            background-color: #9e9e9e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .cancel-btn:hover {
            background-color: #757575;
        }
        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .department-header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .nav-menu {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .nav-button {
            padding: 15px 30px;
            background-color: #00796b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
        }
        .nav-button:hover {
            background-color: #004d40;
            transform: translateY(-2px);
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
    
    /* Request Section Styles */
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

    .approval-section {
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

    .button-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .approve-btn, .reject-btn {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
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

    .reject-form {
        margin-top: 20px;
        padding: 20px;
        background: #fff5f5;
        border-radius: 8px;
        border: 1px solid #fee2e2;
    }

    .reject-form textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin: 10px 0;
        resize: vertical;
    }

    /* Messages */
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
    </style>
</head>
<body>
    <div id="notification" class="notification">
        <div class="notification-content">
            <i class="fas fa-check-circle notification-icon"></i>
            <span id="notification-message"></span>
        </div>
    </div>

    <div id="requestNotification" class="request-notification">
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="notification-text">
                <h4>Success!</h4>
                <p id="request-message">Request submitted successfully to HR.</p>
            </div>
            <button class="close-notification"></button>
        </div>
    </div>

    <div class="container">
        <div class="department-header">
            <h2>HR Department Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
        </div>
        
        <div class="nav-menu">
            <a href="view_hr_requests.php" class="nav-button">Requests</a>
        </div>

        <div class="messages-container">
            <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
                <div class="<?php echo $_GET['status'] === 'success' ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="nav-menu">
            <a href="register.html" class="nav-button">New Registration</a>
        </div>

        <div id="pending-approvals" class="employee-list">
            <h3>Pending Approvals</h3>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="employee-card">
                        <div class="employee-header" onclick="toggleDetails('<?php echo $row['employid']; ?>')">
                            <div class="employee-name">
                                <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>
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

                            <!-- HR Approval Section -->
                            <div class="hr-approval-section">
                                <h4>HR Approval</h4>
                                <div class="approval-action">
                                    <form action="process_hr_approval.php" method="POST" class="inline-approval-form">
                                        <input type="hidden" name="employee_id" value="<?php echo $row['employid']; ?>">
                                        <input type="hidden" name="employee_name" value="<?php echo $row['firstname'] . ' ' . $row['lastname']; ?>">
                                        
                                        <div class="approval-grid">
                                            <div class="approval-item">
                                                <label>HR Name</label>
                                                <input type="text" name="hr_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" readonly>
                                            </div>
                                            <div class="approval-item">
                                                <label>Signature</label>
                                                <input type="text" name="hr_signature" required placeholder="Enter your signature">
                                            </div>
                                            <div class="approval-item">
                                                <label>Date</label>
                                                <input type="date" name="hr_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="button-group">
                                            <button type="submit" class="approve-btn">Approve Registration</button>
                                            <button type="button" class="reject-btn" onclick="showRejectForm('<?php echo $row['employid']; ?>')">
                                                Reject Registration
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-records">No pending approvals</p>
            <?php endif; ?>
        </div>

        <div id="approvalForm" class="approval-form">
            <h4>HR Approval Form</h4>
            <div class="hr-details">
                <div class="hr-info">
                    <p><strong>HR Name:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    <p><strong>Department:</strong> HR</p>
                </div>
            </div>
            <form action="process_hr_approval.php" method="POST" onsubmit="return confirmApproval(event)">
                <input type="hidden" id="employee_id" name="employee_id">
                <input type="hidden" id="employee_name" name="employee_name">
                <input type="hidden" name="hr_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">
                
                <div class="form-group signature-section">
                    <label for="hr_signature">Digital Signature</label>
                    <div class="signature-pad">
                        <canvas id="signaturePad" width="400" height="200"></canvas>
                    </div>
                    <input type="hidden" id="hr_signature" name="hr_signature" required>
                    <div class="signature-buttons">
                        <button type="button" class="clear-btn" onclick="clearSignature()">Clear Signature</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="hr_date">Approval Date</label>
                    <input type="date" id="hr_date" name="hr_date" required readonly>
                </div>

                <div class="approval-buttons">
                    <button type="submit" class="submit-button">Submit Approval</button>
                    <button type="button" class="cancel-button" onclick="hideApprovalForm()">Cancel</button>
                </div>
            </form>
        </div>

        <div class="reject-form" id="reject-form-<?php echo $row['employid']; ?>">
            <h4>Registration Rejection Form</h4>
            <form action="process_hr_rejection.php" method="POST" class="rejection-form" onsubmit="return validateRejection(this);">
                <input type="hidden" name="employee_id" value="<?php echo $row['employid']; ?>">
                <input type="hidden" name="employee_name" value="<?php echo $row['firstname'] . ' ' . $row['lastname']; ?>">
                
                <div class="form-group">
                    <label>HR Officer Name</label>
                    <input type="text" name="hr_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" readonly class="form-control">
                </div>

                <div class="form-group">
                    <label>Rejection Date</label>
                    <input type="date" name="rejection_date" value="<?php echo date('Y-m-d'); ?>" readonly class="form-control">
                </div>

                <div class="reject-reason">
                    <label>Rejection Reason <span style="color: #f44336;">*</span></label>
                    <textarea name="reject_reason" 
                            placeholder="Please provide a detailed reason for rejection (minimum 20 characters)"
                            required></textarea>
                    <div class="error-message" id="reason-error-<?php echo $row['employid']; ?>">
                        Please provide a detailed reason for rejection (minimum 20 characters)
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="button" class="cancel-btn" onclick="hideRejectForm('<?php echo $row['employid']; ?>')">
                        Cancel
                    </button>
                    <button type="submit" class="confirm-reject-btn">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>

        <!-- Add this section for new employee requests -->
        <div class="employee-requests">
            <?php if ($request_result && $request_result->num_rows > 0): ?>
                <?php while($request = $request_result->fetch_assoc()): ?>
                    <div class="request-card" data-department="<?php echo htmlspecialchars($request['department']); ?>">
                        <div class="request-header" onclick="toggleRequestDetails('<?php echo $request['employid']; ?>')">
                            <div class="request-title">
                                <i class="fas fa-user-plus"></i>
                                <div class="request-info">
                                    <h3><?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?></h3>
                                    <span class="request-subtitle"><?php echo htmlspecialchars($request['department']); ?> Department</span>
                                </div>
                                <span class="request-badge pending">Pending HR</span>
                            </div>
                            <div class="request-meta">
                                <div class="request-id">ID: <?php echo htmlspecialchars($request['employid']); ?></div>
                                <div class="request-date"><?php echo date('M d, Y', strtotime($request['created_at'])); ?></div>
                            </div>
                        </div>

                        <div id="request-details-<?php echo $request['employid']; ?>" class="request-details" style="display: none;">
                            <div class="details-grid">
                                <div class="detail-section">
                                    <h4>Personal Information</h4>
                                    <div class="detail-content">
                                        <?php if (isset($request['firstname'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">First Name</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['firstname']); ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($request['lastname'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">Last Name</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['lastname']); ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($request['email'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">Email</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['email']); ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($request['phone'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">Phone</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['phone']); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="detail-section">
                                    <h4>Job Information</h4>
                                    <div class="detail-content">
                                        <?php if (isset($request['department'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">Department</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['department']); ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($request['role'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">Role</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['role']); ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($request['location'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">Location</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['location']); ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($request['access'])): ?>
                                            <div class="detail-group">
                                                <div class="detail-label">Access Level</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($request['access']); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="approval-section">
                                <h4>HR Approval</h4>
                                <form action="process_hr_approval.php" method="POST" class="approval-form">
                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['employid']); ?>">
                                    
                                    <div class="approval-grid">
                                        <div class="approval-item">
                                            <label>HR Name</label>
                                            <input type="text" name="hr_name" value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>" readonly>
                                        </div>
                                        <div class="approval-item">
                                            <label>Signature</label>
                                            <input type="text" name="hr_signature" required placeholder="Enter your signature">
                                        </div>
                                        <div class="approval-item">
                                            <label>Date</label>
                                            <input type="date" name="hr_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="button-group">
                                        <button type="submit" class="approve-btn">
                                            <i class="fas fa-check"></i>
                                            Approve Request
                                        </button>
                                        <button type="button" class="reject-btn" onclick="showRejectForm('<?php echo htmlspecialchars($request['employid']); ?>')">
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
                <div class="no-requests">
                    <i class="fas fa-inbox"></i>
                    <p>No pending requests found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleDetails(employeeId) {
        const detailsDiv = document.getElementById('details-' + employeeId);
        const currentDisplay = detailsDiv.style.display;
        detailsDiv.style.display = currentDisplay === 'block' ? 'none' : 'block';
    }

    function confirmApproval(event) {
        if (!confirm('Are you sure you want to approve this registration?')) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // Set current date as default for all date inputs
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.valueAsDate = new Date();
    });

    function showRejectForm(id) {
        document.getElementById('reject-form-' + id).style.display = 'block';
    }

    function hideRejectForm(id) {
        const form = document.getElementById('reject-form-' + id);
        form.style.display = 'none';
        form.querySelector('textarea[name="reject_reason"]').value = '';
        form.querySelector('.error-message').style.display = 'none';
    }

    function validateRejection(form) {
        const reasonInput = form.querySelector('textarea[name="reject_reason"]');
        const errorDiv = form.querySelector('.error-message');
        const reason = reasonInput.value.trim();

        if (reason.length < 20) {
            errorDiv.style.display = 'block';
            reasonInput.focus();
            return false;
        }

        return confirm('Are you sure you want to reject this registration? This action cannot be undone.');
    }

    function toggleRequestDetails(requestId) {
        const detailsDiv = document.getElementById('request-details-' + requestId);
        const currentDisplay = detailsDiv.style.display;
        detailsDiv.style.display = currentDisplay === 'none' ? 'block' : 'none';
    }

    function confirmApproval(event) {
        return confirm('Are you sure you want to approve this request?');
    }

    function showRejectForm(requestId) {
        // Implementation for reject form display
        const rejectReason = prompt('Please enter the reason for rejection:');
        if (rejectReason) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'process_hr_rejection.php';
            
            const requestIdInput = document.createElement('input');
            requestIdInput.type = 'hidden';
            requestIdInput.name = 'request_id';
            requestIdInput.value = requestId;
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reject_reason';
            reasonInput.value = rejectReason;
            
            form.appendChild(requestIdInput);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>

    <!-- Add this modal for old employee request -->
    <div id="oldEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Old Employee Request Form</h2>
            
            <form action="process_old_employee.php" method="POST" class="employee-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="employee_id">Employee ID</label>
                        <input type="text" id="employee_id" name="employee_id" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Marketing">Marketing</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role</label>
                        <input type="text" id="role" name="role" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="access_rights">Access Rights</label>
                        <select id="access_rights" name="access_rights" required>
                            <option value="">Select Access Level</option>
                            <option value="Basic">Basic</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add these styles -->
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
        }

        .close:hover {
            color: #000;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2196F3;
        }

        .form-actions {
            text-align: center;
            margin-top: 25px;
        }

        .submit-btn {
            background-color: #2196F3;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #1976D2;
            transform: translateY(-2px);
        }
    </style>

    <!-- Add this JavaScript -->
    <script>
        // Get the modal
        var modal = document.getElementById("oldEmployeeModal");

        // Get the button that opens the modal
        var btn = document.querySelector(".request-button"); // Your existing request button

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <!-- Add these styles -->
    <style>
        /* Notification Styles */
        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        .notification-content {
            background: #fff;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
        }

        .notification.success .notification-content {
            border-left: 4px solid #2ecc71;
        }

        .notification.error .notification-content {
            border-left: 4px solid #e74c3c;
        }

        .notification-icon {
            font-size: 20px;
        }

        .notification.success .notification-icon {
            color: #2ecc71;
        }

        .notification.error .notification-icon {
            color: #e74c3c;
        }

        #notification-message {
            color: #2c3e50;
            font-size: 15px;
            font-weight: 500;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <!-- Update your JavaScript -->
    <script>
        // Function to show notification
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');
            const icon = notification.querySelector('.notification-icon');
            
            // Set message
            notificationMessage.textContent = message;
            
            // Set notification type
            notification.className = 'notification ' + type;
            
            // Update icon
            if (type === 'success') {
                icon.className = 'fas fa-check-circle notification-icon';
            } else {
                icon.className = 'fas fa-exclamation-circle notification-icon';
            }
            
            // Show notification
            notification.style.display = 'block';
            
            // Hide after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.5s ease-in forwards';
                setTimeout(() => {
                    notification.style.display = 'none';
                    notification.style.animation = 'slideIn 0.5s ease-out';
                }, 500);
            }, 3000);
        }

        // Update form submission to use AJAX
        document.querySelector('.employee-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('process_old_employee.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showNotification(data.message);
                    // Close the modal
                    document.getElementById('oldEmployeeModal').style.display = 'none';
                    // Reset the form
                    this.reset();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred while processing your request.', 'error');
            });
        });
    </script>

    <!-- Add these styles -->
    <style>
        /* Request Notification Styles */
        .request-notification {
            display: none;
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 9999;
            min-width: 320px;
        }

        .notification-content {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid #2ecc71;
            animation: slideInRight 0.5s ease-out;
        }

        .notification-icon i {
            font-size: 24px;
            color: #2ecc71;
        }

        .notification-text h4 {
            margin: 0;
            color: #2ecc71;
            font-size: 16px;
            font-weight: 600;
        }

        .notification-text p {
            margin: 5px 0 0;
            color: #2c3e50;
            font-size: 14px;
        }

        .close-notification {
            background: none;
            border: none;
            color: #95a5a6;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            margin-left: auto;
        }

        .close-notification:hover {
            color: #7f8c8d;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <!-- Update the JavaScript -->
    <script>
        function showRequestNotification() {
            const notification = document.getElementById('requestNotification');
            
            // Show notification
            notification.style.display = 'block';

            // Auto hide after 3 seconds
            setTimeout(() => {
                hideNotification();
            }, 3000);
        }

        function hideNotification() {
            const notification = document.getElementById('requestNotification');
            notification.style.animation = 'slideOutRight 0.5s ease-in forwards';
            setTimeout(() => {
                notification.style.display = 'none';
                notification.style.animation = '';
            }, 500);
        }

        // Handle new employee request button click
        document.querySelector('.request-button').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Send AJAX request
            fetch('process_request.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    requestType: 'new_employee',
                    timestamp: new Date().toISOString()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showRequestNotification();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // Close notification on button click
        document.querySelector('.close-notification').addEventListener('click', hideNotification);

        // Close notification when clicking outside
        document.addEventListener('click', function(e) {
            const notification = document.getElementById('requestNotification');
            if (notification.style.display === 'block' && !notification.contains(e.target)) {
                hideNotification();
            }
        });
    </script>
</body>
</html>
    <?php $conn->close(); ?>
