-- Create approval_users table if not exists
CREATE TABLE IF NOT EXISTS approval_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department VARCHAR(20) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_email (email)
);

-- Insert HR user (username: hr_admin, password: hr123)
INSERT INTO approval_users (department, username, password, full_name, email) VALUES 
('hr', 'hr_admin', '$2y$10$8tdsR.2X4kYr1POZHJWkH.45ItJGzotWpyqJwjwLrxk2tJHxhWXNi', 'HR Administrator', 'hr@company.com');

-- Insert IT user (username: it_admin, password: it123)
INSERT INTO approval_users (department, username, password, full_name, email) VALUES 
('it', 'it_admin', '$2y$10$Q.3zYGXLtGsXwTCj8WUF7.HB8HPRp9vHZHvM2PWZOzgmHY6FfhXra', 'IT Administrator', 'it@company.com'); 