CREATE DATABASE IF NOT EXISTS bakery_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bakery_db;

-- ======================
-- Customers
-- ======================
CREATE TABLE IF NOT EXISTS customers (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- ======================
-- Products
-- ======================
CREATE TABLE IF NOT EXISTS products (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        name VARCHAR(160) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- ======================
-- Carts
-- ======================
CREATE TABLE IF NOT EXISTS carts (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     customer_id INT NOT NULL,
                                     delivery_option ENUM('pickup', 'delivery') DEFAULT 'pickup',
    delivery_address VARCHAR(255) NULL,
    status ENUM('active','checked_out','abandoned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    );

-- ======================
-- Cart Items
-- ======================
CREATE TABLE IF NOT EXISTS cart_items (
                                          cart_id INT NOT NULL,
                                          product_id INT NOT NULL,
                                          quantity INT NOT NULL DEFAULT 1,
                                          PRIMARY KEY (cart_id, product_id),
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );

-- ======================
-- Orders
-- ======================
CREATE TABLE IF NOT EXISTS orders (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      customer_id INT NOT NULL,
                                      cart_id INT NULL,
                                      subtotal_amount DECIMAL(10,2) NOT NULL,        -- total of items only
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0, -- snapshot of delivery fee at checkout
    total_amount DECIMAL(10,2) GENERATED ALWAYS AS (subtotal_amount + delivery_fee) STORED, -- computed field
    delivery_option ENUM('pickup','delivery') DEFAULT 'pickup',
    delivery_address VARCHAR(255) NULL,
    payment_method ENUM('cod','bkash','nagad') DEFAULT 'cod',
    payment_transaction_id VARCHAR(100) NULL,
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    status ENUM('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE SET NULL
    );

-- ======================
-- Order Items
-- ======================
CREATE TABLE IF NOT EXISTS order_items (
                                           order_id INT NOT NULL,
                                           product_id INT NOT NULL,
                                           quantity INT NOT NULL,
                                           unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );

-- ======================
-- Sample Products
-- ======================
INSERT INTO products (name, price, stock, image_url, description) VALUES
                                                                      ('Chocolate Donut', 1.50, 50, '', 'Rich chocolate glazed donut'),
                                                                      ('Croissant', 2.20, 40, '', 'Buttery flaky croissant'),
                                                                      ('Blueberry Muffin', 2.00, 35, '', 'Muffin with fresh blueberries');

-- ======================
-- Settings
-- ======================
CREATE TABLE IF NOT EXISTS settings (
                                        id INT PRIMARY KEY AUTO_INCREMENT,
                                        key_name VARCHAR(100) UNIQUE,
    value VARCHAR(255)
    );

INSERT INTO settings (key_name, value) VALUES ('delivery_fee', '50')
    ON DUPLICATE KEY UPDATE value='50';
