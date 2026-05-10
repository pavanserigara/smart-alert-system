-- Smart Weather & Energy Ecosystem - Database Schema
-- Purpose: Manage users and role-based access

CREATE DATABASE IF NOT EXISTS weather_eco;
USE weather_eco;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('common', 'farmer', 'energy', 'admin') DEFAULT 'common',
    city_preference VARCHAR(100) DEFAULT 'New York',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AI Processing Logs Table (For Admin View)
CREATE TABLE IF NOT EXISTS processing_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    details TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Emergency Alerts Table (Push Notifications)
CREATE TABLE IF NOT EXISTS emergency_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    message TEXT,
    severity VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    author VARCHAR(100) DEFAULT 'System Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Demo Users (Password is 'password123' for all)
-- Use a standard hash for BCRYPT if using in PHP, here we just show the structure
INSERT INTO users (username, password_hash, role, city_preference) VALUES 
('citizen_joe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'common', 'London'),
('farmer_ted', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', 'Iowa'),
('solar_sam', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'energy', 'Phoenix'),
('admin_main', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Global');
