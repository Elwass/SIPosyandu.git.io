DROP DATABASE IF EXISTS si_posyandu;
CREATE DATABASE IF NOT EXISTS si_posyandu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE si_posyandu;

-- avoid residual references while dropping tables
SET FOREIGN_KEY_CHECKS=0;

-- ensure a clean slate when re-running the schema script
DROP TABLE IF EXISTS fulfillment_orders;
DROP TABLE IF EXISTS recommendation_items;
DROP TABLE IF EXISTS recommendations;
DROP TABLE IF EXISTS medicines;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS reminders;
DROP TABLE IF EXISTS immunizations;
DROP TABLE IF EXISTS measurements;
DROP TABLE IF EXISTS patient_children;
DROP TABLE IF EXISTS bpjs_profiles;
DROP TABLE IF EXISTS residents;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'midwife', 'kader', 'pasien') NOT NULL DEFAULT 'kader',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE residents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    nik VARCHAR(20) NOT NULL UNIQUE,
    family_number VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    category ENUM('pregnant', 'toddler', 'elderly') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bpjs_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    no_bpjs VARCHAR(25) NULL,
    status_bpjs ENUM('aktif', 'tidak_aktif', 'tidak_diketahui') NOT NULL DEFAULT 'tidak_diketahui',
    jenis_bpjs VARCHAR(50) NULL,
    faskes_tingkat_1 VARCHAR(120) NULL,
    tanggal_validasi DATE NULL,
    keterangan TEXT NULL,
    bpjs_reference_id VARCHAR(100) NULL,
    last_bpjs_check_at DATETIME NULL,
    source_system ENUM('manual', 'api') NOT NULL DEFAULT 'manual',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_bpjs (user_id),
    CONSTRAINT fk_bpjs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE patient_children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resident_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_patient_child (user_id, resident_id),
    CONSTRAINT fk_patient_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_patient_resident FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE measurements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    height DECIMAL(5,2) NOT NULL,
    muac DECIMAL(5,2) NULL,
    nutritional_status VARCHAR(50) NOT NULL,
    notes VARCHAR(255) NULL,
    measured_at DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE immunizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    vaccine_name VARCHAR(100) NOT NULL,
    schedule_date DATE NOT NULL,
    administered_date DATE NULL,
    status ENUM('scheduled', 'completed', 'pending') NOT NULL DEFAULT 'scheduled',
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    immunization_id INT NULL,
    schedule_date DATE NOT NULL,
    channel ENUM('sms', 'whatsapp', 'email') NOT NULL,
    status ENUM('scheduled', 'sent') NOT NULL DEFAULT 'scheduled',
    sent_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE,
    FOREIGN KEY (immunization_id) REFERENCES immunizations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(80) NOT NULL UNIQUE,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    description TEXT NULL,
    image_url VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    price INT NOT NULL DEFAULT 0,
    stock INT NULL,
    image VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pickup_method ENUM('PICKUP', 'DELIVERY') NOT NULL DEFAULT 'PICKUP',
    address TEXT NULL,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_status ENUM('UNPAID', 'PAID', 'EXPIRED', 'CANCELLED', 'REFUNDED') NOT NULL DEFAULT 'UNPAID',
    fulfillment_status ENUM('DIPROSES', 'SIAP_DIAMBIL', 'DIKIRIM', 'SELESAI') NOT NULL DEFAULT 'DIPROSES',
    stock_deducted TINYINT(1) NOT NULL DEFAULT 0,
    snap_token VARCHAR(255) NULL,
    snap_redirect_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    transaction_id VARCHAR(120) NULL,
    midtrans_order_id VARCHAR(120) NULL,
    status VARCHAR(50) NOT NULL,
    signature_key VARCHAR(255) NULL,
    raw_response LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    admin_id INT NOT NULL,
    notes TEXT NULL,
    status ENUM('SENT','CONFIRMED','FULFILLED','CANCELLED') NOT NULL DEFAULT 'SENT',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rec_patient FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_rec_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recommendation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recommendation_id INT NOT NULL,
    medicine_id INT NOT NULL,
    qty INT NOT NULL,
    dosage VARCHAR(255) NOT NULL,
    note VARCHAR(255) NULL,
    CONSTRAINT fk_rec_item_header FOREIGN KEY (recommendation_id) REFERENCES recommendations(id) ON DELETE CASCADE,
    CONSTRAINT fk_rec_item_medicine FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fulfillment_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recommendation_id INT NOT NULL,
    patient_id INT NOT NULL,
    fulfillment_method ENUM('PICKUP','DELIVERY','SELF_BUY') NOT NULL,
    address TEXT NULL,
    delivery_fee INT NOT NULL DEFAULT 0,
    total_amount INT NOT NULL DEFAULT 0,
    payment_status ENUM('UNPAID','PENDING','PAID','FAILED') NOT NULL DEFAULT 'UNPAID',
    midtrans_order_id VARCHAR(190) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fulfillment_rec FOREIGN KEY (recommendation_id) REFERENCES recommendations(id) ON DELETE CASCADE,
    CONSTRAINT fk_fulfillment_patient FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name, email, password, role) VALUES
('Super Admin', 'super@posyandu.test', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'super_admin'),
('Ibu Pasien', 'pasien@posyandu.test', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien');
