-- LaptopHub E-Commerce Database Schema
-- Create Database
CREATE DATABASE IF NOT EXISTS laptop_hub;
USE laptop_hub;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category_id INT,
    specs TEXT,
    stock INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(100),
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activity Log Table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_type VARCHAR(20) NOT NULL,
    item_type VARCHAR(30) NOT NULL,
    item_id INT NULL,
    item_name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password Reset Tokens
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Categories
INSERT INTO categories (name, slug, description) VALUES
('Business', 'business', 'Professional laptops for business use'),
('Gaming', 'gaming', 'High-performance gaming laptops'),
('Professional', 'professional', 'Premium laptops for professionals'),
('Student', 'student', 'Affordable laptops for students');

-- Insert Products
INSERT INTO products (name, description, price, image, category_id, specs, stock, featured) VALUES
-- Business Laptops
('Dell Latitude 7420', '14 inch Business Laptop with Intel Core i7, 16GB RAM, 512GB SSD', 85000.00, 'images/Business Lap/Dell.jpg', 1, 'Intel Core i7, 16GB RAM, 512GB SSD, 14 inch FHD', 25, TRUE),
('HP EliteBook 850', '15.6 inch Business Laptop with Intel Core i5, 8GB RAM, 256GB SSD', 80000.00, 'images/Business Lap/Hp-EliteBook.webp', 1, 'Intel Core i5, 8GB RAM, 256GB SSD, 15.6 inch FHD', 30, FALSE),
('Lenovo ThinkPad T14', '14 inch Business Laptop with AMD Ryzen 5, 8GB RAM, 512GB SSD', 78000.00, 'images/Business Lap/T14-.png', 1, 'AMD Ryzen 5, 8GB RAM, 512GB SSD, 14 inch FHD', 20, FALSE),
('ASUS ExpertBook', '14 inch Lightweight Business Laptop with Intel Core i5, 8GB RAM, 512GB SSD', 65000.00, 'images/Business Lap/ExperBook.jpg', 1, 'Intel Core i5, 8GB RAM, 512GB SSD, 14 inch FHD', 35, FALSE),

-- Gaming Laptops
('ASUS ROG Strix', '15.6 inch Gaming Laptop with RTX 4060, Ryzen 7, 16GB RAM, 1TB SSD', 145000.00, 'images/Gaming lap/Asus Rog.jpg', 2, 'RTX 4060, Ryzen 7, 16GB RAM, 1TB SSD, 15.6 inch 144Hz', 15, TRUE),
('Acer Predator Helios', '15.6 inch Gaming Laptop with RTX 4050, Intel i7, 16GB RAM, 512GB SSD', 125000.00, 'images/Gaming lap/Predator.webp', 2, 'RTX 4050, Intel i7, 16GB RAM, 512GB SSD, 15.6 inch 165Hz', 18, FALSE),
('MSI Katana', '15.6 inch Gaming Laptop with RTX 4060, Intel i7, 16GB RAM, 1TB SSD', 115000.00, 'images/Gaming lap/Msi.jpg', 2, 'RTX 4060, Intel i7, 16GB RAM, 1TB SSD, 15.6 inch 144Hz', 12, FALSE),
('HP Omen 16', '16.1 inch Gaming Laptop with RTX 4060, Intel i7, 16GB RAM, 1TB SSD', 130000.00, 'images/Gaming lap/Hp omen.jpg', 2, 'RTX 4060, Intel i7, 16GB RAM, 1TB SSD, 16.1 inch QHD', 20, FALSE),

-- Professional Laptops
('MacBook Pro M3', '14 inch Laptop with Apple M3 Pro, 18GB RAM, 512GB SSD', 180000.00, 'images/Professional lap/MacBook 1.jpg', 3, 'Apple M3 Pro, 18GB RAM, 512GB SSD, 14 inch Retina', 10, TRUE),
('Dell XPS 15', '15.6 inch Laptop with Intel Core i7, 16GB RAM, 512GB SSD, OLED', 175000.00, 'images/Professional lap/Dell.jpg', 3, 'Intel Core i7, 16GB RAM, 512GB SSD, 15.6 inch OLED', 15, FALSE),
('Lenovo ThinkPad X1', '14 inch Ultrabook with Intel Core i7, 16GB RAM, 1TB SSD', 150000.00, 'images/Professional lap/good.jpg', 3, 'Intel Core i7, 16GB RAM, 1TB SSD, 14 inch 2.8K OLED', 12, FALSE),
('HP ZBook Studio', '15.6 inch Mobile Workstation with RTX 2000, Intel i7, 32GB RAM', 190000.00, 'images/Professional lap/Z Book.png', 3, 'Intel Core i7, RTX 2000, 32GB RAM, 1TB SSD, 15.6 inch 4K', 8, FALSE),

-- Student Laptops
('HP 15s', '15.6 inch Student Laptop with AMD Ryzen 3, 8GB RAM, 512GB SSD', 45000.00, 'images/Student lap/HP.jpg', 4, 'AMD Ryzen 3, 8GB RAM, 512GB SSD, 15.6 inch HD', 50, FALSE),
('Lenovo IdeaPad 3', '15.6 inch Student Laptop with Intel Core i3, 8GB RAM, 256GB SSD', 42000.00, 'images/Student lap/Ideapad.jpg', 4, 'Intel Core i3, 8GB RAM, 256GB SSD, 15.6 inch HD', 45, FALSE),
('Acer Aspire 5', '15.6 inch Student Laptop with Intel Core i5, 8GB RAM, 512GB SSD', 50000.00, 'images/Student lap/Acer.jpg', 4, 'Intel Core i5, 8GB RAM, 512GB SSD, 15.6 inch FHD', 40, FALSE),
('Dell Inspiron 14', '14 inch Student Laptop with Intel Core i5, 8GB RAM, 512GB SSD', 48000.00, 'images/Student lap/Dell.jpg', 4, 'Intel Core i5, 8GB RAM, 512GB SSD, 14 inch FHD', 35, TRUE);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Admin User
-- Email: admin@lapphub.com
-- Password: alf123
INSERT INTO admins (name, email, password) VALUES
('Admin', 'admin@lapphub.com', '$2b$12$zi92j5tBNPvGmNUWhjlrpuVt2Pa1wGW0Xh65aX1DhQNt1m2.TrQWW');