-- SAIBA System Table
CREATE TABLE saiba_access (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    employee_id BIGINT NOT NULL,
    access_level ENUM('full', 'limited', 'none') DEFAULT 'none',
    module_finance BOOLEAN DEFAULT FALSE,
    module_reporting BOOLEAN DEFAULT FALSE,
    module_admin BOOLEAN DEFAULT FALSE,
    module_audit BOOLEAN DEFAULT FALSE,
    access_start_date DATE NOT NULL,
    access_end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- SARB System Table
CREATE TABLE sarb_access (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    employee_id BIGINT NOT NULL,
    access_level ENUM('full', 'limited', 'none') DEFAULT 'none',
    module_banking BOOLEAN DEFAULT FALSE,
    module_transactions BOOLEAN DEFAULT FALSE,
    module_compliance BOOLEAN DEFAULT FALSE,
    module_reports BOOLEAN DEFAULT FALSE,
    access_start_date DATE NOT NULL,
    access_end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- TAILY System Table
CREATE TABLE taily_access (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    employee_id BIGINT NOT NULL,
    access_level ENUM('full', 'limited', 'none') DEFAULT 'none',
    module_accounting BOOLEAN DEFAULT FALSE,
    module_payroll BOOLEAN DEFAULT FALSE,
    module_inventory BOOLEAN DEFAULT FALSE,
    module_billing BOOLEAN DEFAULT FALSE,
    access_start_date DATE NOT NULL,
    access_end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Access History Table
CREATE TABLE access_history (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    employee_id BIGINT NOT NULL,
    system_name ENUM('SAIBA', 'SARB', 'TAILY') NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    previous_level VARCHAR(50),
    new_level VARCHAR(50),
    changed_by BIGINT NOT NULL,
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (changed_by) REFERENCES employees(id)
);

-- Access Requests Table
CREATE TABLE access_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    employee_id BIGINT NOT NULL,
    system_name ENUM('SAIBA', 'SARB', 'TAILY') NOT NULL,
    requested_level VARCHAR(50) NOT NULL,
    request_reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_by BIGINT NOT NULL,
    approved_by BIGINT,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response_date TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (requested_by) REFERENCES employees(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id)
);
