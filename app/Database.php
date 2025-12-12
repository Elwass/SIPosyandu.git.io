<?php
class Database
{
    private static ?\PDO $instance = null;

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $db = config('db');

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $db['host'],
                $db['port'],
                $db['database'],
                $db['charset']
            );

            try {
                self::$instance = new \PDO($dsn, $db['username'], $db['password'], [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]);
            } catch (\PDOException $e) {
                if (str_contains($e->getMessage(), 'Unknown database')) {
                    self::createDatabase($db);
                    self::$instance = new \PDO($dsn, $db['username'], $db['password'], [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    ]);
                } else {
                    throw $e;
                }
            }

            self::ensureSchema(self::$instance, $db['charset']);
        }

        return self::$instance;
    }

    private static function createDatabase(array $db): void
    {
        $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $db['host'], $db['port'], $db['charset']);
        $pdo = new \PDO($dsn, $db['username'], $db['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $db['database']
        ));
    }

    private static function ensureSchema(\PDO $pdo, string $charset): void
    {
        $requiredTables = [
            'users', 'residents', 'bpjs_profiles', 'patient_children', 'measurements', 'immunizations', 'reminders'
        ];

        if (!self::isMissingTables($pdo, $requiredTables)) {
            return;
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'midwife', 'kader', 'pasien') NOT NULL DEFAULT 'kader',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE=utf8mb4_unicode_ci;
SQL);

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS residents (
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
) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE=utf8mb4_unicode_ci;
SQL);

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS bpjs_profiles (
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
) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE=utf8mb4_unicode_ci;
SQL);

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS patient_children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resident_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_patient_child (user_id, resident_id),
    CONSTRAINT fk_patient_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_patient_resident FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE=utf8mb4_unicode_ci;
SQL);

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS measurements (
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
) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE=utf8mb4_unicode_ci;
SQL);

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS immunizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    vaccine_name VARCHAR(100) NOT NULL,
    schedule_date DATE NOT NULL,
    administered_date DATE NULL,
    status ENUM('scheduled', 'completed', 'pending') NOT NULL DEFAULT 'scheduled',
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE=utf8mb4_unicode_ci;
SQL);

        $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS reminders (
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
) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE=utf8mb4_unicode_ci;
SQL);

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

        // Seed minimal accounts so login pages work out-of-the-box
        $seedPassword = '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2'; // "password"
        $pdo->exec(<<<SQL
INSERT IGNORE INTO users (name, email, password, role) VALUES
('Super Admin', 'super@posyandu.test', '{$seedPassword}', 'super_admin'),
('Ibu Pasien', 'pasien@posyandu.test', '{$seedPassword}', 'pasien');
SQL);
    }

    private static function isMissingTables(\PDO $pdo, array $tables): bool
    {
        $placeholders = implode(',', array_fill(0, count($tables), '?'));
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS existing FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ($placeholders)"
        );
        $stmt->execute($tables);
        $existing = (int)$stmt->fetchColumn();

        return $existing < count($tables);
    }
}
