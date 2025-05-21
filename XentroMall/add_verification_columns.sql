ALTER TABLE application_submissions
ADD COLUMN verification_status ENUM('approved', 'rejected') DEFAULT NULL,
ADD COLUMN admin_feedback TEXT DEFAULT NULL;
