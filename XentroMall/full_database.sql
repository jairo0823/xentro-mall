-- Database: xentromall
CREATE DATABASE IF NOT EXISTS xentromall;
USE xentromall;

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'tenant',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `tenant_details`
CREATE TABLE IF NOT EXISTS tenant_details (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  tradename VARCHAR(255) NOT NULL,
  store_premises VARCHAR(255) NOT NULL,
  store_location VARCHAR(255) NOT NULL,
  ownership VARCHAR(50) NOT NULL,
  company_name VARCHAR(255) NOT NULL,
  business_address TEXT NOT NULL,
  tin VARCHAR(50),
  office_tel VARCHAR(50),
  tenant_representative VARCHAR(255),
  contact_person VARCHAR(255),
  position VARCHAR(100),
  contact_tel VARCHAR(50),
  mobile VARCHAR(50),
  email VARCHAR(255),
  prepared_by VARCHAR(255),
  business_type VARCHAR(50),
  documents VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
