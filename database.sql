CREATE DATABASE IF NOT EXISTS dormitory_db;
USE dormitory_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS room_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    deposit DECIMAL(10, 2) NOT NULL,
    description TEXT,
    facilities TEXT
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    floor INT NOT NULL,
    type_id INT,
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    FOREIGN KEY (type_id) REFERENCES room_types(id)
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    room_id INT,
    check_in_date DATE NOT NULL,
    contract_duration INT NOT NULL COMMENT 'Duration in months',
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Insert dummy data for room types
INSERT INTO room_types (name, price, deposit, description, facilities) VALUES 
('Standard', 3500.00, 5000.00, 'Standard room with basic amenities', 'Fan, Bed, Wardrobe'),
('Deluxe', 5000.00, 7000.00, 'Deluxe room with air conditioning', 'Air Con, Bed, Wardrobe, Water Heater');

-- Insert dummy data for rooms
INSERT INTO rooms (room_number, floor, type_id, status) VALUES 
('101', 1, 1, 'available'),
('102', 1, 1, 'occupied'),
('201', 2, 2, 'available'),
('202', 2, 2, 'available');

-- Insert default admin (password: admin123)
-- Note: You should hash passwords in production. For Basic PHP request, we will use password_hash() in the code.
INSERT INTO users (username, password, role, full_name, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Admin', 'admin@example.com');
