CREATE TABLE work_permits (
    permit_no INT AUTO_INCREMENT PRIMARY KEY,
    date_filed DATE NOT NULL,
    tenant_name VARCHAR(255) NOT NULL,
    scope_of_work TEXT NOT NULL,
    security_posting BOOLEAN DEFAULT FALSE,
    rate_security DECIMAL(10,2) DEFAULT NULL,
    charge_security ENUM('With Charge', 'No Charge') DEFAULT NULL,
    janitorial_deployment BOOLEAN DEFAULT FALSE,
    rate_janitorial DECIMAL(10,2) DEFAULT NULL,
    charge_janitorial ENUM('With Charge', 'No Charge') DEFAULT NULL,
    maintenance BOOLEAN DEFAULT FALSE,
    rate_maintenance DECIMAL(10,2) DEFAULT NULL,
    charge_maintenance ENUM('With Charge', 'No Charge') DEFAULT NULL,
    personnel TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
