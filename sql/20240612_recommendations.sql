-- Migration for medicine recommendations and fulfillment orders
CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    price INT NOT NULL DEFAULT 0,
    stock INT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    admin_id INT NOT NULL,
    notes TEXT NULL,
    status ENUM('SENT','CONFIRMED','FULFILLED','CANCELLED') NOT NULL DEFAULT 'SENT',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recommendation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recommendation_id INT NOT NULL,
    medicine_id INT NOT NULL,
    qty INT NOT NULL,
    dosage VARCHAR(255) NOT NULL,
    note VARCHAR(255) NULL
);

CREATE TABLE IF NOT EXISTS fulfillment_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recommendation_id INT NOT NULL,
    patient_id INT NOT NULL,
    fulfillment_method ENUM('PICKUP','DELIVERY','SELF_BUY') NOT NULL,
    address TEXT NULL,
    delivery_fee INT NOT NULL DEFAULT 0,
    total_amount INT NOT NULL DEFAULT 0,
    payment_status ENUM('UNPAID','PENDING','PAID','FAILED') NOT NULL DEFAULT 'UNPAID',
    midtrans_order_id VARCHAR(190) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
