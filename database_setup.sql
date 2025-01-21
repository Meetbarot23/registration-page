-- Create HR approvals table
CREATE TABLE hr_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hr_name VARCHAR(100) NOT NULL,
    hr_signature VARCHAR(255) NOT NULL,
    approval_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create IT approvals table
CREATE TABLE it_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    it_name VARCHAR(100) NOT NULL,
    approval_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create approval logs table
CREATE TABLE approval_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    approval_type VARCHAR(20) NOT NULL,
    approved_by VARCHAR(100) NOT NULL,
    approval_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create approval users table
CREATE TABLE approval_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department VARCHAR(20) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_dept_user (department, username)
);

-- Insert sample users (password is 'password123' hashed)
INSERT INTO approval_users (department, username, password) VALUES 
('hr', 'hr_admin', '$2y$10$YourHashedPasswordHere'),
('it', 'it_admin', '$2y$10$YourHashedPasswordHere'); 