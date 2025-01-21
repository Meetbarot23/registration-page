<?php
session_start();
require_once 'db_connection.php';
?>

<div class="registration-container">
    <div id="popup" class="popup-overlay">
        <div class="popup-content"></div>
    </div>

    <form id="registrationForm" class="registration-form">
        <h2>Employee Registration Form</h2>

        <!-- Employee Type -->
        <div class="form-section">
            <h3>Employee Type</h3>
            <div class="form-group">
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="employee_type" value="new" required> New Employee
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="employee_type" value="existing" required> Existing Employee
                    </label>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="form-section">
            <h3>Personal Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="firstname">First Name *</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name *</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                
            </div>
        </div>

        <!-- Department Information -->
        <div class="form-section">
            <h3>Department Information</h3>
            <div class="form-grid">
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
                    <label for="role">Role *</label>
                    <input type="text" id="role" name="role" required>
                </div>

                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" required>
                </div>
            </div>
        </div>

        <!-- Access Details -->
        <div class="form-section">
            <h3>Access Details</h3>
            <div class="form-grid">
                <!-- Email Access -->
                <div class="form-group">
                    <label>Email Access Level *</label>
                    <div class="access-options">
                        <label class="access-option">
                            <input type="radio" name="email_access" value="full" required>
                            <span class="option-content">
                                <i class="fas fa-envelope"></i>
                                <span class="option-title">Full Access</span>
                                <span class="option-desc">Internal & External Email</span>
                            </span>
                        </label>
                        <label class="access-option">
                            <input type="radio" name="email_access" value="internal" required>
                            <span class="option-content">
                                <i class="fas fa-envelope-open"></i>
                                <span class="option-title">Internal Only</span>
                                <span class="option-desc">Company Email Only</span>
                            </span>
                        </label>
                        <label class="access-option">
                            <input type="radio" name="email_access" value="restricted" required>
                            <span class="option-content">
                                <i class="fas fa-envelope-square"></i>
                                <span class="option-title">Restricted</span>
                                <span class="option-desc">Limited Email Access</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- USB Access -->
                <div class="form-group">
                    <label>USB Access Level *</label>
                    <div class="access-options">
                        <label class="access-option">
                            <input type="radio" name="usb_access" value="full" required>
                            <span class="option-content">
                                <i class="fas fa-usb"></i>
                                <span class="option-title">Full Access</span>
                                <span class="option-desc">Read & Write Access</span>
                            </span>
                        </label>
                        <label class="access-option">
                            <input type="radio" name="usb_access" value="readonly" required>
                            <span class="option-content">
                                <i class="fas fa-lock"></i>
                                <span class="option-title">Read Only</span>
                                <span class="option-desc">View Files Only</span>
                            </span>
                        </label>
                        <label class="access-option">
                            <input type="radio" name="usb_access" value="blocked" required>
                            <span class="option-content">
                                <i class="fas fa-ban"></i>
                                <span class="option-title">Blocked</span>
                                <span class="option-desc">No USB Access</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" id="submitBtn" class="submit-btn">
                <i class="fas fa-user-plus"></i> Submit Registration
            </button>
        </div>
    </form>
</div>

<style>
.registration-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
}

.form-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.form-section h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    outline: none;
}

.radio-group,
.checkbox-group {
    display: flex;
    gap: 20px;
    margin-top: 10px;
}

.radio-label,
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
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

.popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup-content {
    background: white;
    padding: 30px;
    border-radius: 12px;
    max-width: 400px;
    width: 90%;
    text-align: center;
}

/* Add responsive design */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.access-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 10px;
}

.access-option {
    cursor: pointer;
    position: relative;
}

.access-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.option-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.access-option input[type="radio"]:checked + .option-content {
    border-color: #3498db;
    background: #ebf7ff;
}

.option-content i {
    font-size: 24px;
    color: #3498db;
    margin-bottom: 10px;
}

.option-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.option-desc {
    font-size: 12px;
    color: #64748b;
    text-align: center;
}

.access-option:hover .option-content {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Responsive design for access options */
@media (max-width: 768px) {
    .access-options {
        grid-template-columns: 1fr;
    }
    
    .option-content {
        flex-direction: row;
        justify-content: flex-start;
        gap: 15px;
        text-align: left;
    }
    
    .option-content i {
        margin-bottom: 0;
    }
    
    .option-text {
        display: flex;
        flex-direction: column;
    }
}
</style>

<script>
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const formData = new FormData(this);

    fetch('process_registration.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showPopup('success', 'Registration Successful', data.message);
            this.reset();
        } else {
            showPopup('error', 'Registration Failed', data.message);
        }
    })
    .catch(error => {
        showPopup('error', 'Error', 'An error occurred while processing your request.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Submit Registration';
    });
});

function showPopup(type, title, message) {
    const popup = document.getElementById('popup');
    const content = popup.querySelector('.popup-content');
    
    content.innerHTML = `
        <div class="popup-header ${type}">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <h3>${title}</h3>
        </div>
        <div class="popup-body">
            <p>${message}</p>
        </div>
        <div class="popup-footer">
            <button onclick="closePopup()" class="btn ${type}">OK</button>
        </div>
    `;
    
    popup.style.display = 'flex';
}

function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

// Validate email access and USB access selections
document.querySelectorAll('input[name="email_access[]"], input[name="usb_access[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const group = this.name;
        const checkboxes = document.querySelectorAll(`input[name="${group}"]`);
        let checked = false;
        checkboxes.forEach(cb => {
            if (cb.checked) checked = true;
        });
        
        checkboxes.forEach(cb => {
            const label = cb.closest('.checkbox-label');
            if (!checked) {
                label.classList.add('error');
            } else {
                label.classList.remove('error');
            }
        });
    });
});
</script>