<?php
session_start();
require_once 'db_connection.php';

// Check if database connection exists
if (!$conn) {
    die("Database connection failed");
}

try {
    // First, check if the required tables exist
    $table_check = $conn->query("SHOW TABLES LIKE 'employee_registrations'");
    if ($table_check->num_rows == 0) {
        // Create the table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS employee_registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employid VARCHAR(50) UNIQUE NOT NULL,
            firstname VARCHAR(100),
            lastname VARCHAR(100),
            email VARCHAR(255),
            phone VARCHAR(20),
            department VARCHAR(100),
            role VARCHAR(100),
            location VARCHAR(100),
            access VARCHAR(50),
            status VARCHAR(50) DEFAULT 'PENDING_HR',
            request_type VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($create_table);
    }

    // Prepare the SQL query with proper JOIN syntax and filtering
    $request_sql = "SELECT * FROM employee_registrations 
                   WHERE status = 'PENDING_HR' 
                   ORDER BY created_at DESC";
                   
    $request_result = $conn->query($request_sql);

    if ($request_result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }
?>

<!-- Add search and filter options -->
<div class="request-controls">
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search requests...">
        <i class="fas fa-search"></i>
    </div>
    <div class="filter-options">
        <select id="departmentFilter">
            <option value="">All Departments</option>
            <option value="IT">IT</option>
            <option value="HR">HR</option>
            <option value="Finance">Finance</option>
            <option value="Marketing">Marketing</option>
        </select>
        <select id="statusFilter">
            <option value="PENDING_HR">Pending</option>
            <option value="all">All Status</option>
        </select>
    </div>
</div>

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
                        <div class="request-id">ID: <?php echo htmlspecialchars($request['employid'] ?? 'N/A'); ?></div>
                        <div class="request-date"><?php echo date('M d, Y', strtotime($request['created_at'])); ?></div>
                    </div>
                </div>

                <div id="request-details-<?php echo $request['employid']; ?>" class="request-details" style="display: none;">
                    <div class="details-grid">
                        <div class="detail-section">
                            <h4>Personal Information</h4>
                            <div class="detail-content">
                                <?php
                                $fields = [
                                    'firstname' => 'First Name',
                                    'lastname' => 'Last Name',
                                    'email' => 'Email',
                                    'phone' => 'Phone'
                                ];
                                foreach ($fields as $field => $label):
                                    if (isset($request[$field]) && !empty($request[$field])):
                                ?>
                                    <div class="detail-group">
                                        <div class="detail-label"><?php echo $label; ?></div>
                                        <div class="detail-value"><?php echo htmlspecialchars($request[$field]); ?></div>
                                    </div>
                                <?php 
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h4>Job Information</h4>
                            <div class="detail-content">
                                <?php
                                $job_fields = [
                                    'department' => 'Department',
                                    'role' => 'Role',
                                    'location' => 'Location',
                                    'access' => 'Access Level'
                                ];
                                foreach ($job_fields as $field => $label):
                                    if (isset($request[$field]) && !empty($request[$field])):
                                ?>
                                    <div class="detail-group">
                                        <div class="detail-label"><?php echo $label; ?></div>
                                        <div class="detail-value"><?php echo htmlspecialchars($request[$field]); ?></div>
                                    </div>
                                <?php 
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="approval-section">
                        <h4>HR Approval</h4>
                        <form action="process_hr_approval.php" method="POST" class="approval-form" onsubmit="return confirmApproval(this)">
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
                                <div class="approval-item full-width">
                                    <label>Comments (Optional)</label>
                                    <textarea name="hr_comments" placeholder="Add any comments..."></textarea>
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

<?php
} catch (Exception $e) {
    echo '<div class="error-message">';
    echo '<i class="fas fa-exclamation-circle"></i>';
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
?>

<style>
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
    padding: 12px 40px 12px 15px;
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
    cursor: pointer;
}

.request-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.request-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

/* ... Add your existing styles ... */
</style>

<script>
// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterRequests);
document.getElementById('departmentFilter').addEventListener('change', filterRequests);
document.getElementById('statusFilter').addEventListener('change', filterRequests);

function filterRequests() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const departmentFilter = document.getElementById('departmentFilter').value;
    const cards = document.querySelectorAll('.request-card');

    cards.forEach(card => {
        const cardText = card.textContent.toLowerCase();
        const cardDepartment = card.dataset.department;
        const shouldShow = 
            cardText.includes(searchTerm) && 
            (departmentFilter === '' || cardDepartment === departmentFilter);
        
        card.style.display = shouldShow ? 'block' : 'none';
    });
}

function toggleRequestDetails(requestId) {
    const detailsDiv = document.getElementById('request-details-' + requestId);
    if (detailsDiv) {
        const isHidden = detailsDiv.style.display === 'none';
        detailsDiv.style.display = isHidden ? 'block' : 'none';
        
        if (isHidden) {
            detailsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
}

function confirmApproval(form) {
    return confirm('Are you sure you want to approve this request?');
}

function showRejectForm(requestId) {
    const reason = prompt('Please enter the reason for rejection:');
    if (reason) {
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
        reasonInput.value = reason;
        
        form.appendChild(requestIdInput);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>