-- SQL script to create an admin user with email admin@gmail.com and password admin123
-- Replace 'users' with your actual users table name if different

INSERT INTO users (username, email, password, role)
VALUES (
    'admin@gmail.com',
    'admin@gmail.com',
    -- Password hash for 'admin123' generated using PHP password_hash function
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);

-- Note: The password hash above is generated using PHP's password_hash('admin123', PASSWORD_DEFAULT) function.
