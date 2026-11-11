-- FarmDirect schema. You can import this file in phpMyAdmin.
CREATE DATABASE IF NOT EXISTS farmer_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE farmer_marketplace;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(25) DEFAULT NULL,
  location VARCHAR(150) DEFAULT NULL,
  role ENUM('farmer','buyer','admin') NOT NULL DEFAULT 'buyer',
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS crops (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description TEXT,
  quantity DECIMAL(10,2) NOT NULL,
  unit VARCHAR(20) NOT NULL DEFAULT 'kg',
  price DECIMAL(10,2) NOT NULL,
  location VARCHAR(150) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  status ENUM('available','sold') NOT NULL DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_crop_farmer FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_crop_name (name), INDEX idx_crop_location (location), INDEX idx_crop_status (status)
) ENGINE=InnoDB;
