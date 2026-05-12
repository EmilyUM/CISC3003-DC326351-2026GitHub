CREATE DATABASE IF NOT EXISTS cisc3003_p2c CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cisc3003_p2c;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_email_verified TINYINT(1) NOT NULL DEFAULT 0,
  email_verify_token_hash CHAR(64) NULL,
  password_reset_token_hash CHAR(64) NULL,
  password_reset_expires_at DATETIME NULL
);

