<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->begin_transaction();

        // Generate unique IDs
        $request_id = 'REQ_' . date('Ymd') . '_' . uniqid();
        $employee_id = 'EMP_' . date('Ymd') . '_' . uniqid();

        // Insert into employee_registrations
        $reg_sql = "INSERT INTO employee_registrations (
            employid,
            firstname,
            middlename,
            lastname,
            department,
            role,
            location,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING')";

        $reg_stmt = $conn->prepare($reg_sql);
        $reg_stmt->bind_param("sssssss",
            $employee_id,
            $_POST['firstname'],
            $_POST['middlename'],
            $_POST['lastname'],
            $_POST['department'],
            $_POST['role'],
            $_POST['location']
        );

        if (!$reg_stmt->execute()) {
            throw new Exception("Error registering employee: " . $reg_stmt->error);
        }

        // Create HR Request
        $hr_sql = "INSERT INTO requests (
            request_id,
            request_type,
            employee_id,
            department,
            priority,
            status,
            description,
            created_at
        ) VALUES (?, 'employee_registration', ?, 'HR', 'medium', 'PENDING', ?, NOW())";

        $description = "New employee registration request:\n" .
                      "Name: " . $_POST['firstname'] . " " . $_POST['lastname'] . "\n" .
                      "Department: " . $_POST['department'] . "\n" .
                      "Role: " . $_POST['role'] . "\n" .
                      "Location: " . $_POST['location'];

        $hr_stmt = $conn->prepare($hr_sql);
        $hr_stmt->bind_param("sss",
            $request_id,
            $employee_id,
            $description
        );

        if (!$hr_stmt->execute()) {
            throw new Exception("Error creating HR request: " . $hr_stmt->error);
        }

        // Insert access rights
        $access_sql = "INSERT INTO access_rights (
            employee_id,
            saiba_access,
            sarb_access,
            taily_access,
            email_access,
            created_at
        ) VALUES (?, ?, ?, ?, ?, NOW())";

        $access_stmt = $conn->prepare($access_sql);
        $access_stmt->bind_param("sssss",
            $employee_id,
            $_POST['saiba_access'] ?? 'none',
            $_POST['sarb_access'] ?? 'none',
            $_POST['taily_access'] ?? 'none',
            $_POST['email'] ?? ''
        );

        if (!$access_stmt->execute()) {
            throw new Exception("Error setting access rights: " . $access_stmt->error);
        }

        // Create notification
        $notif_sql = "INSERT INTO notifications (
            request_id,
            notification_type,
            message,
            status,
            created_at
        ) VALUES (?, 'registration', ?, 'unread', NOW())";

        $notif_message = "New employee registration request for " . 
                        $_POST['firstname'] . " " . $_POST['lastname'];

        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("ss",
            $request_id,
            $notif_message
        );

        if (!$notif_stmt->execute()) {
            throw new Exception("Error creating notification: " . $notif_stmt->error);
        }

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Registration submitted successfully. Request ID: ' . $request_id,
            'request_id' => $request_id,
            'employee_id' => $employee_id
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

try {
    // Fetch all HR-related requests
    $request_sql = "SELECT r.*, 
                          e.firstname, 
                          e.lastname, 
                          e.department 
                   FROM requests r 
                   LEFT JOIN employee_registrations e ON r.created_by = e.employid 
                   WHERE r.department = 'HR' 
                   ORDER BY r.created_at DESC";
                   
    $request_result = $conn->query($request_sql);

    if ($request_result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }
?>

<div class="hr-requests-container">
    <h2>HR Department Requests</h2>
    
    <div class="request-controls">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search requests...">
            <i class="fas fa-search"></i>
        </div>
        <div class="filter-options">
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="PENDING">Pending</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="COMPLETED">Completed</option>
            </select>
            <select id="priorityFilter">
                <option value="">All Priorities</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
        </div>
    </div>

    <div class="requests-grid">
        <?php if ($request_result && $request_result->num_rows > 0): ?>
            <?php while($request = $request_result->fetch_assoc()): ?>
                <div class="request-card" 
                     data-status="<?php echo htmlspecialchars($request['status']); ?>"
                     data-priority="<?php echo htmlspecialchars($request['priority']); ?>">
                    <div class="request-header">
                        <div class="request-title">
                            <span class="request-type"><?php echo htmlspecialchars($request['request_type']); ?></span>
                            <span class="request-id">#<?php echo htmlspecialchars($request['request_id']); ?></span>
                        </div>
                        <span class="priority-badge <?php echo strtolower($request['priority']); ?>">
                            <?php echo ucfirst($request['priority']); ?>
                        </span>
                    </div>

                    <div class="request-content">
                        <p class="request-description"><?php echo htmlspecialchars($request['description']); ?></p>
                        <div class="request-meta">
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-building"></i>
                                <?php echo htmlspecialchars($request['department']); ?>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M d, Y', strtotime($request['created_at'])); ?>
                            </div>
                        </div>
                    </div>

                    <div class="request-actions">
                        <button class="action-btn view-btn" onclick="viewRequest('<?php echo $request['request_id']; ?>')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <?php if ($request['status'] === 'PENDING'): ?>
                            <button class="action-btn process-btn" onclick="processRequest('<?php echo $request['request_id']; ?>')">
                                <i class="fas fa-tasks"></i> Process
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-requests">
                <i class="fas fa-inbox"></i>
                <p>No requests found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.hr-requests-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.request-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 20px;
}

.search-box {
    position: relative;
    flex: 1;
}

.search-box input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.filter-options {
    display: flex;
    gap: 10px;
}

.filter-options select {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}

.requests-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.request-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.request-card:hover {
    transform: translateY(-2px);
}

.request-header {
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.priority-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.priority-badge.high {
    background: #fee2e2;
    color: #dc2626;
}

.priority-badge.medium {
    background: #fef3c7;
    color: #d97706;
}

.priority-badge.low {
    background: #e0f2fe;
    color: #0284c7;
}

.request-content {
    padding: 15px;
}

.request-description {
    margin-bottom: 15px;
    color: #4b5563;
}

.request-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 13px;
    color: #6b7280;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.request-actions {
    padding: 15px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}

.action-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
}

.view-btn {
    background: #e0f2fe;
    color: #0284c7;
}

.process-btn {
    background: #3498db;
    color: white;
}

.action-btn:hover {
    filter: brightness(0.95);
}
</style>

<script>
// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterRequests);
document.getElementById('statusFilter').addEventListener('change', filterRequests);
document.getElementById('priorityFilter').addEventListener('change', filterRequests);

function filterRequests() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    
    const cards = document.querySelectorAll('.request-card');
    
    cards.forEach(card => {
        const cardText = card.textContent.toLowerCase();
        const cardStatus = card.dataset.status;
        const cardPriority = card.dataset.priority;
        
        const matchesSearch = cardText.includes(searchTerm);
        const matchesStatus = !statusFilter || cardStatus === statusFilter;
        const matchesPriority = !priorityFilter || cardPriority === priorityFilter;
        
        card.style.display = (matchesSearch && matchesStatus && matchesPriority) ? 'block' : 'none';
    });
}

function viewRequest(requestId) {
    // Implement view request details functionality
    window.location.href = `view_request.php?id=${requestId}`;
}

function processRequest(requestId) {
    // Implement process request functionality
    window.location.href = `process_request.php?id=${requestId}`;
}
</script>

<?php
} catch (Exception $e) {
    echo '<div class="error-message">';
    echo '<i class="fas fa-exclamation-circle"></i>';
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
?> 