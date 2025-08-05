-- Database schema for SwiftDelivery Courier Management System

CREATE DATABASE IF NOT EXISTS courier_db;
USE courier_db;

-- Customers table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip_code VARCHAR(10) NOT NULL,
    country VARCHAR(50) NOT NULL DEFAULT 'USA',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Shipments table
CREATE TABLE shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_number VARCHAR(20) UNIQUE NOT NULL,
    sender_id INT,
    receiver_name VARCHAR(100) NOT NULL,
    receiver_phone VARCHAR(20) NOT NULL,
    receiver_email VARCHAR(100),
    pickup_address TEXT NOT NULL,
    pickup_city VARCHAR(50) NOT NULL,
    pickup_state VARCHAR(50) NOT NULL,
    pickup_zip VARCHAR(10) NOT NULL,
    delivery_address TEXT NOT NULL,
    delivery_city VARCHAR(50) NOT NULL,
    delivery_state VARCHAR(50) NOT NULL,
    delivery_zip VARCHAR(10) NOT NULL,
    package_weight DECIMAL(10,2) NOT NULL,
    package_dimensions VARCHAR(50),
    service_type ENUM('express', 'standard', 'overnight', 'same_day') NOT NULL,
    status ENUM('pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed_delivery') DEFAULT 'pending',
    estimated_delivery DATE,
    actual_delivery DATETIME,
    total_cost DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Tracking events table
CREATE TABLE tracking_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    status VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    description TEXT,
    event_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
);

-- Quotes table
CREATE TABLE quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    pickup_address TEXT NOT NULL,
    delivery_address TEXT NOT NULL,
    package_weight DECIMAL(10,2) NOT NULL,
    package_dimensions VARCHAR(50),
    service_type ENUM('express', 'standard', 'overnight', 'same_day') NOT NULL,
    estimated_cost DECIMAL(10,2),
    status ENUM('pending', 'quoted', 'accepted', 'declined') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'manager', 'operator') DEFAULT 'operator',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password, email, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@swiftdelivery.com', 'System Administrator', 'admin');

-- Sample data for testing
INSERT INTO customers (first_name, last_name, email, phone, address, city, state, zip_code) VALUES
('John', 'Doe', 'john.doe@email.com', '555-0123', '123 Main St', 'New York', 'NY', '10001'),
('Jane', 'Smith', 'jane.smith@email.com', '555-0124', '456 Oak Ave', 'Los Angeles', 'CA', '90001'),
('Bob', 'Johnson', 'bob.johnson@email.com', '555-0125', '789 Pine Rd', 'Chicago', 'IL', '60601');

INSERT INTO shipments (tracking_number, sender_id, receiver_name, receiver_phone, receiver_email, pickup_address, pickup_city, pickup_state, pickup_zip, delivery_address, delivery_city, delivery_state, delivery_zip, package_weight, service_type, status, total_cost) VALUES
('SD001234567890', 1, 'Alice Brown', '555-0126', 'alice@email.com', '123 Main St', 'New York', 'NY', '10001', '789 Broadway', 'Boston', 'MA', '02101', 2.5, 'express', 'in_transit', 24.99),
('SD001234567891', 2, 'Charlie Wilson', '555-0127', 'charlie@email.com', '456 Oak Ave', 'Los Angeles', 'CA', '90001', '321 Market St', 'San Francisco', 'CA', '94102', 1.2, 'standard', 'picked_up', 12.50),
('SD001234567892', 3, 'Diana Davis', '555-0128', 'diana@email.com', '789 Pine Rd', 'Chicago', 'IL', '60601', '654 Elm St', 'Detroit', 'MI', '48201', 3.8, 'overnight', 'delivered', 35.75);

INSERT INTO tracking_events (shipment_id, status, location, description) VALUES
(1, 'Package Picked Up', 'New York, NY', 'Package picked up from sender'),
(1, 'In Transit', 'Philadelphia, PA', 'Package in transit to destination'),
(2, 'Package Picked Up', 'Los Angeles, CA', 'Package picked up from sender'),
(3, 'Package Picked Up', 'Chicago, IL', 'Package picked up from sender'),
(3, 'In Transit', 'Toledo, OH', 'Package in transit to destination'),
(3, 'Delivered', 'Detroit, MI', 'Package delivered successfully');