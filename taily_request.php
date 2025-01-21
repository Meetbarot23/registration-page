// Add this to your existing JavaScript
function showPopupMessage(message, type = 'success') {
    // Create popup container
    const popup = document.createElement('div');
    popup.className = `popup-message ${type}`;
    
    // Create message content
    const content = document.createElement('div');
    content.className = 'popup-content';
    
    // Add icon based on type
    const icon = document.createElement('i');
    icon.className = type === 'success' 
        ? 'fas fa-check-circle' 
        : 'fas fa-exclamation-circle';
    
    // Add message text
    const text = document.createElement('span');
    text.textContent = message;
    
    // Add close button
    const closeBtn = document.createElement('button');
    closeBtn.className = 'popup-close';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = () => {
        popup.remove();
    };
    
    // Assemble popup
    content.appendChild(icon);
    content.appendChild(text);
    content.appendChild(closeBtn);
    popup.appendChild(content);
    
    // Add to document
    document.body.appendChild(popup);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        popup.classList.add('fade-out');
        setTimeout(() => {
            popup.remove();
        }, 500);
    }, 3000);
}

// Update your form submission handler
document.getElementById('requestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const formData = new FormData(this);

    fetch('process_taily_request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success popup
            showPopup('success', 'Request Submitted', data.message);
            // Reset form
            this.reset();
        } else {
            // Show error popup
            showPopup('error', 'Error', data.message);
        }
    })
    .catch(error => {
        showPopup('error', 'Error', 'An error occurred while processing your request.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Request';
    });
});

function showPopup(type, title, message) {
    // Create popup elements
    const popup = document.createElement('div');
    popup.className = `popup-overlay ${type}`;
    
    popup.innerHTML = `
        <div class="popup-content">
            <div class="popup-header ${type}">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <h3>${title}</h3>
                <button class="close-btn" onclick="closePopup(this)">×</button>
            </div>
            <div class="popup-body">
                <p>${message}</p>
            </div>
            <div class="popup-footer">
                <button onclick="closePopup(this)" class="btn ${type}">OK</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Add animation class after a small delay
    setTimeout(() => {
        popup.classList.add('show');
    }, 10);
}

function closePopup(element) {
    const popup = element.closest('.popup-overlay');
    popup.classList.remove('show');
    setTimeout(() => {
        popup.remove();
    }, 300);
}

<style>
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.popup-overlay.show {
    opacity: 1;
    visibility: visible;
}

.popup-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    transform: scale(0.7);
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.popup-overlay.show .popup-content {
    transform: scale(1);
}

.popup-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 15px;
}

.popup-header.success {
    color: #2ecc71;
}

.popup-header.error {
    color: #e74c3c;
}

.popup-header i {
    font-size: 24px;
}

.popup-header h3 {
    margin: 0;
    flex-grow: 1;
    font-size: 18px;
}

.popup-body {
    padding: 20px;
    color: #666;
}

.popup-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    text-align: right;
}

.btn {
    padding: 8px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn.success {
    background: #2ecc71;
    color: white;
}

.btn.error {
    background: #e74c3c;
    color: white;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.close-btn:hover {
    color: #666;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.7);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: scale(1);
    }
    to {
        opacity: 0;
        transform: scale(0.7);
    }
}
</style>