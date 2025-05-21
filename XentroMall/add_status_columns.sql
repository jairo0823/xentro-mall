ALTER TABLE application_submissions
ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending';

ALTER TABLE maintenance_requests
ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending';

ALTER TABLE renewal_requests
ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending';
