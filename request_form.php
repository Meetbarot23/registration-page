<?php
session_start();
require_once 'db_connection.php';
?>

<div class="request-container">
    <div id="notification" class="notification" style="display: none;">
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <span id="notification-message"></span>
            <button onclick="closeNotification()" class="close-btn">&times;</button>
        </div>
    </div>

    <form id="requestForm" class="request-form">
        <h2>New Request Form</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="request_type">Request Type *</label>
                <select id="request_type" name="request_type" required>
                    <option value="">Select Request Type</option>
                    <option value="hardware">Hardware Request</option>
                    <option value="software">Software Request</option>
                    <option value="access">Access Request</option>
                    <option value="other">Other Request</option>
                </select>
            </div>

            <div class="form-group">
                <label for="priority">Priority Level *</label>
                <select id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="description">Request Description *</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="department">Department *</label>
                <select id="department" name="department" required>
                    <option value="">Select Department</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                    <option value="Finance">Finance</option>
                    <option value="Marketing">Marketing</option>
                </select>
            </div>

            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" id="submitBtn" class="submit-btn">
                <i class="fas fa-paper-plane"></i>
                Submit Request
            </button>
        </div>
    </form>
</div>

<style>
.request-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
}

.request-form {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    font-weight: 500;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    outline: none;
}

.submit-btn {
    background: #3498db;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.submit-btn:disabled {
    background: #95a5a6;
    cursor: not-allowed;
    transform: none;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    min-width: 300px;
}

.notification-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 15px;
    animation: slideIn 0.5s ease-out;
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
</style>

<script>
document.getElementById('requestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const formData = new FormData(this);

    fetch('process_request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            this.reset();
            
            // Redirect to IT department page after delay
            setTimeout(() => {
                window.location.href = 'it_department.php';
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('An error occurred while processing your request.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Request';
    });
});

function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const messageElement = document.getElementById('notification-message');
    const icon = notification.querySelector('i');
    
    notification.className = `notification ${type}`;
    messageElement.textContent = message;
    icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    notification.style.display = 'block';
    
    setTimeout(() => {
        closeNotification();
    }, 5000);
}

function closeNotification() {
    const notification = document.getElementById('notification');
    notification.style.animation = 'slideOut 0.5s ease-in forwards';
    setTimeout(() => {
        notification.style.display = 'none';
        notification.style.animation = '';
    }, 500);
}
</script> 