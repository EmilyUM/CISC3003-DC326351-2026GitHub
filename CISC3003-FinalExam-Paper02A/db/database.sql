CREATE DATABASE IF NOT EXISTS cisc3003_p2a CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cisc3003_p2a;

CREATE TABLE IF NOT EXISTS submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  age INT NULL,
  bio TEXT NOT NULL,
  major VARCHAR(50) NOT NULL,
  gender VARCHAR(20) NOT NULL,
  interests VARCHAR(255) NOT NULL,
  agree_terms TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO submissions (full_name, email, age, bio, major, gender, interests, agree_terms)
VALUES ('Demo User', 'demo@example.com', 20, 'Demo bio', 'CS', 'Male', 'PHP,MySQL', 1);

