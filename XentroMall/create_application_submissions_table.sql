CREATE TABLE application_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    letter_of_intent VARCHAR(255) NOT NULL,
    business_profile VARCHAR(255) NOT NULL,
    business_registration VARCHAR(255) NOT NULL,
    valid_id VARCHAR(255) NOT NULL,
    bir_registration VARCHAR(255) NOT NULL,
    financial_statement VARCHAR(255) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
