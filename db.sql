CREATE DATABASE ecorecycle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ecorecycle;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

CREATE TABLE electronic_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    condition VARCHAR(50) NOT NULL,
    seller_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_seller_id (seller_id),
    INDEX idx_category (category),
    INDEX idx_condition (condition)
);

INSERT INTO users (username, email) VALUES 
('john_doe', 'john@example.com'),
('jane_smith', 'jane@example.com'),
('mike_wilson', 'mike@example.com'),
('sarah_connor', 'sarah@example.com'),
('alex_tech', 'alex@example.com');

INSERT INTO electronic_items (name, category, condition, seller_id) VALUES 
('iPhone 12 Pro', 'Smartphone', 'Good', 1),
('MacBook Air M1', 'Laptop', 'Like New', 2),
('Samsung Galaxy Tab S7', 'Tablet', 'Fair', 3),
('Dell XPS 13', 'Laptop', 'Good', 1),
('iPad Pro 11"', 'Tablet', 'New', 4),
('HP LaserJet Pro', 'Printer', 'Good', 5),
('ASUS ROG Desktop', 'Desktop', 'Like New', 2),
('Samsung 27" Monitor', 'Monitor', 'Good', 3),
('Google Pixel 6', 'Smartphone', 'Fair', 4),
('Microsoft Surface Pro 8', 'Tablet', 'Like New', 5);

CREATE VIEW items_with_sellers AS
SELECT 
    ei.id,
    ei.name,
    ei.category,
    ei.condition,
    ei.seller_id,
    u.username AS seller_name,
    u.email AS seller_email,
    ei.created_at,
    ei.updated_at
FROM electronic_items ei
JOIN users u ON ei.seller_id = u.id;

CREATE INDEX idx_items_created_at ON electronic_items(created_at);
CREATE INDEX idx_users_created_at ON users(created_at);

CREATE USER IF NOT EXISTS 'hitesh'@'localhost' IDENTIFIED BY 'hitesh123';
GRANT ALL PRIVILEGES ON ecorecycle.* TO 'hitesh'@'localhost';
