CREATE DATABASE IF NOT EXISTS si_posyandu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE si_posyandu;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'midwife', 'kader') NOT NULL DEFAULT 'kader',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
);

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
);

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
);

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
);

INSERT INTO users (name, email, password, role) VALUES
('Super Admin', 'super@posyandu.test', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'super_admin');
