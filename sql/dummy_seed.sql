-- Pastikan file gambar ada di public/uploads/medicines/ sesuai nama pada kolom image
-- Optional: aman untuk reset data rekomendasi/obat (biarkan users jika sudah ada user nyata)
-- SET FOREIGN_KEY_CHECKS=0;
-- TRUNCATE TABLE fulfillment_orders;
-- TRUNCATE TABLE recommendation_items;
-- TRUNCATE TABLE recommendations;
-- TRUNCATE TABLE medicines;
-- SET FOREIGN_KEY_CHECKS=1;

INSERT INTO users (id, name, email, password, role) VALUES
    (1, 'Super Admin', 'superadmin@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'super_admin'),
    (2, 'Admin Klinik', 'admin@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'admin'),
    (3, 'Pasien Sinta', 'sinta@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien'),
    (4, 'Pasien Budi', 'budi@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien'),
    (5, 'Pasien Rina', 'rina@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien'),
    (6, 'Pasien Dedi', 'dedi@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien'),
    (7, 'Pasien Maya', 'maya@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien'),
    (8, 'Pasien Lila', 'lila@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien'),
    (9, 'Pasien Rafi', 'rafi@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien'),
    (10, 'Pasien Nia', 'nia@example.com', '$2y$10$zO80yAGP82LPgAvFp8Z64eiUm7Uxr87hcPLZ9eczsQnUnxE27XGr2', 'pasien');

INSERT INTO medicines (id, name, unit, price, stock, image, is_active, created_at) VALUES
    (1, 'Paracetamol 500mg', 'tablet', 5000, 100, 'uploads/medicines/paracetamol.jpg', 1, '2024-07-01 08:00:00'),
    (2, 'Vitamin C 500mg', 'tablet', 7000, 80, 'uploads/medicines/vitamin-c.jpg', 1, '2024-07-01 08:05:00'),
    (3, 'Oralit', 'sachet', 4000, 60, 'uploads/medicines/oralit.jpg', 1, '2024-07-01 08:10:00'),
    (4, 'Amoxicillin 500mg', 'kapsul', 12000, 70, 'uploads/medicines/amoxicillin.jpg', 1, '2024-07-01 08:15:00'),
    (5, 'Sirup Salbutamol', 'botol', 15000, 40, 'uploads/medicines/salbutamol.jpg', 1, '2024-07-01 08:20:00'),
    (6, 'Ibuprofen 200mg', 'tablet', 9000, 90, 'uploads/medicines/ibuprofen.jpg', 1, '2024-07-01 08:25:00'),
    (7, 'Zinc 20mg', 'tablet', 3000, 120, 'uploads/medicines/zinc.jpg', 1, '2024-07-01 08:30:00'),
    (8, 'Sirup Cetirizine', 'botol', 18000, 30, 'uploads/medicines/cetirizine.jpg', 1, '2024-07-01 08:35:00'),
    (9, 'Betadine Solution', 'botol', 25000, 50, 'uploads/medicines/betadine.jpg', 1, '2024-07-01 08:40:00'),
    (10, 'Krim Hydrocortisone', 'tube', 22000, 35, 'uploads/medicines/hydrocortisone.jpg', 1, '2024-07-01 08:45:00');

INSERT INTO recommendations (id, patient_id, admin_id, notes, status, created_at) VALUES
    (1, 3, 1, 'Keluhan demam ringan dan nyeri kepala.', 'SENT', '2024-07-02 09:00:00'),
    (2, 4, 2, 'Batuk pilek, butuh antibiotik dan vitamin.', 'CONFIRMED', '2024-07-02 09:30:00'),
    (3, 5, 1, 'Diare ringan, hidrasi dan bronkodilator.', 'SENT', '2024-07-02 10:00:00'),
    (4, 6, 2, 'Alergi musiman, butuh antihistamin.', 'FULFILLED', '2024-07-02 10:30:00'),
    (5, 7, 1, 'Nyeri sendi, butuh analgesik.', 'SENT', '2024-07-02 11:00:00'),
    (6, 8, 2, 'Luka ringan, butuh antiseptik.', 'CANCELLED', '2024-07-02 11:30:00'),
    (7, 9, 1, 'Flu ringan, butuh penurun panas.', 'CONFIRMED', '2024-07-02 12:00:00'),
    (8, 10, 2, 'Diare sedang, butuh oralit dan antiinflamasi.', 'SENT', '2024-07-02 12:30:00'),
    (9, 3, 1, 'Asma ringan, butuh bronkodilator dan antihistamin.', 'SENT', '2024-07-02 13:00:00'),
    (10, 4, 2, 'Demam dan infeksi bakteri ringan.', 'SENT', '2024-07-02 13:30:00');

INSERT INTO recommendation_items (recommendation_id, medicine_id, qty, dosage, note) VALUES
    (1, 1, 2, '2x1 tablet per hari setelah makan', 'Jika demam >38.5C'),
    (1, 2, 1, '1x1 tablet per hari', NULL),
    (2, 4, 1, '3x1 kapsul per hari', NULL),
    (2, 7, 2, '1 tablet per hari', 'Habiskan dalam 7 hari'),
    (3, 5, 1, '2x5ml per hari', NULL),
    (3, 3, 2, 'Larutkan tiap setelah BAB cair', NULL),
    (4, 8, 1, '1x5ml malam hari', NULL),
    (5, 6, 2, '3x1 tablet per hari', 'Setelah makan'),
    (5, 10, 1, 'Oleskan tipis 2x sehari', NULL),
    (6, 9, 1, 'Gunakan 2x sehari', 'Hanya area luka'),
    (6, 2, 1, '1x1 tablet per hari', NULL),
    (7, 1, 1, '2x1 tablet per hari', NULL),
    (7, 7, 1, '1x1 tablet per hari', NULL),
    (8, 3, 3, 'Larutkan setiap diare', NULL),
    (8, 6, 1, '3x1 tablet per hari', 'Maks 5 hari'),
    (9, 5, 2, '2x5ml per hari', NULL),
    (9, 8, 1, '1x5ml malam hari', NULL),
    (10, 2, 2, '1x1 tablet per hari', NULL),
    (10, 4, 1, '3x1 kapsul per hari', NULL);

INSERT INTO fulfillment_orders (id, recommendation_id, patient_id, fulfillment_method, address, delivery_fee, total_amount, payment_status, midtrans_order_id, created_at) VALUES
    (1, 1, 3, 'PICKUP', NULL, 0, 17000, 'PENDING', 'POSYANDU-MED-1-1710001', '2024-07-02 14:00:00'),
    (2, 2, 4, 'DELIVERY', 'Jl. Melati No.12, Kota A', 8000, 26000, 'PAID', 'POSYANDU-MED-2-1710002', '2024-07-02 14:10:00'),
    (3, 3, 5, 'SELF_BUY', NULL, 0, 23000, 'UNPAID', NULL, '2024-07-02 14:20:00'),
    (4, 4, 6, 'PICKUP', NULL, 0, 18000, 'PAID', 'POSYANDU-MED-4-1710004', '2024-07-02 14:30:00'),
    (5, 5, 7, 'DELIVERY', 'Jl. Kenanga No.9, Kota B', 10000, 50000, 'FAILED', 'POSYANDU-MED-5-1710005', '2024-07-02 14:40:00'),
    (6, 6, 8, 'PICKUP', NULL, 0, 32000, 'UNPAID', NULL, '2024-07-02 14:50:00'),
    (7, 7, 9, 'PICKUP', NULL, 0, 8000, 'PENDING', 'POSYANDU-MED-7-1710007', '2024-07-02 15:00:00'),
    (8, 8, 10, 'DELIVERY', 'Jl. Anggrek No.5, Kota C', 7000, 28000, 'PAID', 'POSYANDU-MED-8-1710008', '2024-07-02 15:10:00'),
    (9, 9, 3, 'PICKUP', NULL, 0, 48000, 'PENDING', 'POSYANDU-MED-9-1710009', '2024-07-02 15:20:00'),
    (10, 10, 4, 'SELF_BUY', NULL, 0, 26000, 'UNPAID', NULL, '2024-07-02 15:30:00');
