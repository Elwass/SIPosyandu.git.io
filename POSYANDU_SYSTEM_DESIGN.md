# Rancangan Sistem Informasi Posyandu Berbasis Web

## Ringkasan
Sistem informasi Posyandu berbasis web ditujukan untuk membantu Puskesmas mengatasi kendala pencatatan manual pada layanan ibu hamil, balita, dan lansia. Sistem ini mengintegrasikan pencatatan data warga, pemeriksaan rutin, imunisasi, laporan otomatis, serta dashboard statistik gizi anak. Arsitektur fullstack diterapkan dengan pemisahan antara frontend, backend, dan basis data untuk memastikan skalabilitas dan keamanan.

## Arsitektur Sistem Fullstack
- **Frontend (Web App)**
  - Framework SPA (mis. React/Vue) untuk pengalaman pengguna interaktif.
  - Komponen utama: autentikasi, pendaftaran warga, form pencatatan, dashboard statistik, manajemen jadwal imunisasi, reminder center, dan modul laporan.
  - Menggunakan state management (Redux/Pinia) untuk sinkronisasi data lintas halaman serta komponen grafik (Chart.js/ECharts) untuk dashboard gizi.
  - Integrasi dengan API backend via REST/GraphQL dengan dukungan WebSocket/SSE untuk notifikasi real-time (mis. status reminder terkirim).
- **Frontend Mobile (Opsional)**
  - Aplikasi hybrid (Ionic/React Native) untuk kader melakukan input cepat di lapangan.
  - Mendukung mode offline-first dengan penyimpanan lokal (IndexedDB/SQLite) dan sinkronisasi periodik.
- **Backend (Service API)**
  - Framework berbasis Node.js/Express atau Laravel untuk pengelolaan bisnis proses dan penegakan aturan domain kesehatan ibu & anak.
  - Modul utama:
    - **Auth & Authorization Service** (JWT + refresh token, RBAC middleware).
    - **Citizen & Household Service** untuk registrasi warga dan validasi NIK/KK.
    - **Maternal & Child Health Service** untuk catatan timbang, ANC (antenatal care), lansia, dan imunisasi.
    - **Reporting Service** untuk agregasi data, pengelolaan template, dan konversi PDF.
    - **Notification Service** sebagai orchestrator reminder imunisasi yang terhubung dengan job scheduler dan gateway pesan.
  - Menyediakan webhook internal bagi aplikasi mobile kader (opsional) dan endpoint public untuk integrasi Dinas Kesehatan.
- **Database**
  - RDBMS (PostgreSQL/MySQL) dengan relasi kuat antar entitas warga, pemeriksaan, imunisasi, dan jadwal; disertai view materialized untuk statistik gizi.
- **Layanan Pendukung**
  - **Job Scheduler & Queue**: (mis. BullMQ/Cron + Redis) mengatur pengiriman reminder jadwal imunisasi dan eskalasi jadwal terlewat.
  - **PDF Service**: modul server-side untuk menghasilkan laporan PDF (mis. menggunakan wkhtmltopdf atau library sejenis) berikut manajemen template yang dapat dikustomisasi super admin.
  - **Storage**: penyimpanan file hasil export laporan (S3-compatible) dan cache CDN untuk distribusi cepat ke Puskesmas.
  - **Monitoring & Logging**: stack ELK/Prometheus-Grafana untuk audit, kesehatan aplikasi, dan pemantauan kepatuhan.

### Detail Arsitektur Frontend

1. **Lapisan Presentasi**
   - Atomic design untuk konsistensi UI: atoms (input, button), molecules (form grup timbang), organisms (dashboard cards).
   - Design system terpusat (Storybook) agar tim dapat menguji komponen secara terisolasi.
2. **State Management**
   - Store global menyimpan profil pengguna, hak akses, dan konfigurasi wilayah.
   - Module terpisah untuk data pendaftaran, catatan timbang, jadwal imunisasi, serta laporan.
   - Menggunakan selector memoized guna menjaga performa saat menampilkan grafik besar.
3. **Komunikasi Data**
   - `apiClient` abstraksi fetch/axios dengan interceptors untuk token refresh.
   - Service worker untuk caching halaman utama (PWA) dan menerima push notification reminder.
   - Error boundary global menangani kegagalan API dan menampilkan fallback.
4. **Keamanan Frontend**
   - Penyimpanan token di httpOnly cookie, proteksi CSRF dengan synchronizer token.
   - Implementasi Content Security Policy (CSP) dan sanitasi input pada form editor template laporan.

### Detail Arsitektur Backend

1. **Struktur Layanan**
   - Backend modular berbasis layered architecture (controller → service → repository) atau hexagonal untuk memisahkan domain dan infrastruktur.
   - Microservice opsional: Notification Service dipisahkan agar dapat diskalakan independen dengan worker queue.
2. **API Gateway & BFF (Backend for Frontend)**
   - API gateway mengelola rate limiting, caching, dan versioning endpoint.
   - BFF khusus frontend web mengoptimalkan payload (agregasi beberapa service menjadi satu response) terutama untuk dashboard statistik.
3. **Pengelolaan Data & Integrasi**
   - ORM (TypeORM/Prisma/Eloquent) untuk mempercepat query sekaligus menjaga validasi skema.
   - Modul ETL kecil untuk impor data eksternal (format CSV/Excel) dan sinkronisasi Dinas Kesehatan.
   - Materialized view direfresh via job scheduler; cache API layer (Redis) untuk endpoint statistik.
4. **Keamanan Backend**
   - Middleware sanitasi input, rate limiter per IP, dan verifikasi tanda tangan digital pada permintaan dari super admin.
   - Audit log otomatis di-trigger oleh domain events (mis. `CitizenRegistered`, `ImmunizationReminderSent`).

### Orkestrasi Data End-to-End

1. **Sequence Login → Dashboard**
   - Pengguna login → Auth Service membuat token → Frontend menyimpan token → Frontend memanggil endpoint `/me` untuk memuat profil dan role → Dashboard BFF menyiapkan data statistik, notifikasi reminder terbaru, dan daftar tugas kader.
2. **Sequence Pendaftaran Lapangan**
   - Kader membuka aplikasi mobile offline → Input data disimpan lokal → Saat jaringan tersedia, sync adapter mengirim batch ke API → Backend memvalidasi, menyimpan ke `citizens`, mengirim event `CitizenRegistered` → Notification Service mengirim email notifikasi ke admin Puskesmas.
3. **Sequence Reminder Imunisasi**
   - Scheduler membaca `immunization_schedules` untuk H-3/H-1 → Membuat job queue `SendReminder` → Worker Notification memanggil SMS/WhatsApp gateway → Respons disimpan di `immunization_reminders` dan status job diupdate.
4. **Sequence Cetak Laporan**
   - Admin memilih periode → Backend melakukan agregasi (SQL + pipeline ETL) → Template Engine merender HTML → PDF Service mengubah menjadi PDF → File diupload ke storage → Event `ReportGenerated` memicu email ke pihak terkait dan menandai status `distributed` jika terkirim.

### Infrastruktur & Deployment

- **Lingkungan**: Dev, staging, dan production dengan konfigurasi environment variable terpisah menggunakan secret manager.
- **Containerization**: Seluruh service dikemas dalam Docker; orchestrator (Kubernetes/Docker Swarm) mengatur autoscaling backend dan worker reminder.
- **API Monitoring**: Health check endpoint (`/healthz`) dipantau oleh load balancer; alerting melalui Opsgenie/Slack.
- **Security Compliance**: WAF di layer depan, IDS/IPS untuk mendeteksi anomali, enkripsi basis data dengan KMS.

### CI/CD & QA

- **CI Pipeline**: Linting (ESLint/Prettier, PHP-CS-Fixer), unit test, integration test (Postman/Newman) dijalankan otomatis di tiap commit.
- **CD Pipeline**: Deploy blue-green untuk backend dan canary release untuk frontend SPA melalui CDN.
- **Quality Gates**: Coverage minimal 80%, pemeriksaan kerentanan dependency (Snyk/NPM audit), serta scanning konfigurasi Docker.
- **Observability QA**: Synthetic test memastikan endpoint kritikal (login, pendaftaran, reminder scheduler) berfungsi pasca deploy.

## Role & Hak Akses

| Modul/Fitur | Super Admin | Admin Puskesmas | Bidan | Kader Posyandu |
| --- | --- | --- | --- | --- |
| Manajemen master (Puskesmas, Posyandu, role) | CRUD penuh | Baca wilayah sendiri | - | - |
| Manajemen pengguna (admin, bidan, kader) | CRUD penuh | CRUD dalam wilayah | Baca profil sendiri | Baca profil sendiri |
| Pendaftaran warga | Monitoring | CRUD warga wilayah | Baca/validasi | Create & update data lapangan |
| Catatan ibu hamil & ANC | Monitoring | Baca & validasi | CRUD (input, verifikasi) | Baca |
| Catatan balita (timbang & gizi) | Monitoring | Baca & validasi | CRUD | Create & update |
| Catatan lansia | Monitoring | Baca & validasi | CRUD | Create & update |
| Jadwal & reminder imunisasi | Atur template reminder | CRUD jadwal wilayah, jalankan scheduler | CRUD jadwal pasien | Baca daftar & tandai hadir |
| Dashboard statistik | Agregat nasional/kabupaten | Statistik wilayah | Statistik pasien sendiri | Statistik posyandu |
| Laporan PDF | Atur template, generate lintas wilayah | Generate & unduh laporan wilayah | Baca laporan pasien | Baca ringkasan posyandu |
| Integrasi eksternal/API | Konfigurasi API key | Ajukan permintaan integrasi | - | - |

> **Catatan:** Warga/ortu opsional hanya memiliki akses baca terhadap jadwal imunisasi, reminder, dan riwayat keluarga melalui portal publik/ aplikasi mobile.

## Struktur Tabel Database (Ringkas)

### 1. `users`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas pengguna |
| `name` | VARCHAR | Nama lengkap |
| `email` | VARCHAR (unik) | Email login |
| `password_hash` | VARCHAR | Hash kata sandi |
| `role` | ENUM (`super_admin`, `admin_puskesmas`, `bidan`, `kader`) | Peran pengguna |
| `puskesmas_id` | FK -> `puskesmas.id` | Relasi wilayah kerja |
| `last_login_at` | TIMESTAMP | Catatan login terakhir |
| `is_active` | BOOLEAN | Status akun |

### 1a. `user_roles`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas role spesifik |
| `user_id` | FK -> `users.id` | Relasi pengguna |
| `role` | ENUM (`super_admin`, `admin_puskesmas`, `bidan`, `kader`) | Role yang dimiliki |
| `posyandu_id` | FK -> `posyandu.id` (opsional) | Pembatasan akses spesifik |

### 1b. `user_sessions`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas sesi |
| `user_id` | FK -> `users.id` | Pengguna |
| `refresh_token` | VARCHAR | Token refresh terenkripsi |
| `expires_at` | TIMESTAMP | Masa berlaku |
| `device_info` | JSONB | Informasi perangkat |

### 2. `puskesmas`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas Puskesmas |
| `name` | VARCHAR | Nama Puskesmas |
| `district` | VARCHAR | Kecamatan |
| `address` | TEXT | Alamat |
| `phone` | VARCHAR | Kontak |

### 3. `posyandu`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas Posyandu |
| `puskesmas_id` | FK -> `puskesmas.id` | Relasi Puskesmas |
| `name` | VARCHAR | Nama Posyandu |
| `village` | VARCHAR | Desa/Kelurahan |
| `schedule_day` | VARCHAR | Hari rutin kegiatan |

### 4. `citizens`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas warga |
| `family_card_number` | VARCHAR | Nomor KK |
| `nik` | VARCHAR | NIK |
| `name` | VARCHAR | Nama lengkap |
| `birth_date` | DATE | Tanggal lahir |
| `gender` | ENUM (`L`, `P`) | Jenis kelamin |
| `address` | TEXT | Alamat |
| `phone` | VARCHAR | Kontak |
| `posyandu_id` | FK -> `posyandu.id` | Posyandu terdaftar |
| `category` | ENUM (`ibu_hamil`, `balita`, `lansia`) | Kelompok layanan |
| `status` | ENUM (`aktif`, `nonaktif`) | Status kepesertaan |
| `guardian_name` | VARCHAR | Nama wali (balita) |
| `guardian_contact` | VARCHAR | Kontak wali |
| `registration_source` | ENUM (`manual`, `import`, `mobile_app`) | Sumber pendaftaran |

### 5. `pregnancy_records`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas catatan kehamilan |
| `citizen_id` | FK -> `citizens.id` | Ibu hamil |
| `visit_date` | DATE | Tanggal pemeriksaan |
| `gestational_age_weeks` | INTEGER | Usia kehamilan |
| `weight` | DECIMAL | Berat badan |
| `blood_pressure` | VARCHAR | Tekanan darah |
| `notes` | TEXT | Catatan bidan |
| `midwife_id` | FK -> `users.id` | Bidan pemeriksa |

### 6. `child_growth_records`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas pencatatan |
| `citizen_id` | FK -> `citizens.id` | Balita |
| `recorded_at` | DATE | Tanggal penimbangan |
| `weight` | DECIMAL | Berat |
| `height` | DECIMAL | Tinggi/Panjang |
| `head_circumference` | DECIMAL | Lingkar kepala |
| `nutrition_status` | ENUM (`gizi_buruk`, `gizi_kurang`, `gizi_baik`, `gizi_lebih`, `obesitas`) | Status gizi |
| `recorder_id` | FK -> `users.id` | Kader/Bidan pencatat |
| `z_score_weight_for_age` | DECIMAL | Nilai z-score berat menurut usia |
| `z_score_height_for_age` | DECIMAL | Nilai z-score tinggi menurut usia |
| `attachment_path` | VARCHAR | Bukti foto (opsional) |

### 7. `elderly_health_records`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas pemeriksaan |
| `citizen_id` | FK -> `citizens.id` | Lansia |
| `recorded_at` | DATE | Tanggal pemeriksaan |
| `blood_pressure` | VARCHAR | Tekanan darah |
| `blood_sugar` | DECIMAL | Gula darah |
| `cholesterol` | DECIMAL | Kolesterol |
| `notes` | TEXT | Catatan kesehatan |
| `recorder_id` | FK -> `users.id` | Petugas |

### 8. `immunization_schedules`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas jadwal |
| `citizen_id` | FK -> `citizens.id` | Balita |
| `vaccine_type` | VARCHAR | Jenis vaksin |
| `scheduled_date` | DATE | Tanggal terjadwal |
| `status` | ENUM (`terjadwal`, `terlewat`, `selesai`) | Status jadwal |
| `reminder_sent_at` | TIMESTAMP | Waktu reminder dikirim |
| `second_reminder_sent_at` | TIMESTAMP | Waktu reminder susulan |
| `channel` | ENUM (`sms`, `whatsapp`, `email`) | Kanal reminder utama |

### 9. `immunization_records`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas imunisasi |
| `schedule_id` | FK -> `immunization_schedules.id` | Relasi jadwal |
| `citizen_id` | FK -> `citizens.id` | Balita |
| `vaccine_type` | VARCHAR | Jenis vaksin |
| `immunization_date` | DATE | Tanggal imunisasi |
| `batch_number` | VARCHAR | Batch vaksin |
| `officer_id` | FK -> `users.id` | Bidan/Kader |
| `notes` | TEXT | Catatan |
| `certificate_url` | VARCHAR | Link sertifikat imunisasi |

### 9a. `immunization_reminders`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas reminder |
| `schedule_id` | FK -> `immunization_schedules.id` | Relasi jadwal |
| `sent_at` | TIMESTAMP | Waktu pengiriman |
| `status` | ENUM (`sukses`, `gagal`, `dijadwalkan`) | Status kirim |
| `response_payload` | JSONB | Respons gateway pesan |

### 10. `events`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas kegiatan |
| `posyandu_id` | FK -> `posyandu.id` | Posyandu penyelenggara |
| `title` | VARCHAR | Nama kegiatan |
| `event_date` | DATE | Tanggal |
| `description` | TEXT | Deskripsi |

### 11. `reports`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas laporan |
| `puskesmas_id` | FK -> `puskesmas.id` | Puskesmas |
| `generated_by` | FK -> `users.id` | User penghasil laporan |
| `period_start` | DATE | Periode awal |
| `period_end` | DATE | Periode akhir |
| `report_type` | ENUM (`bulanan`, `triwulan`, `tahunan`, `khusus`) | Jenis laporan |
| `file_path` | VARCHAR | Lokasi file PDF |
| `created_at` | TIMESTAMP | Waktu dibuat |
| `status` | ENUM (`draft`, `generated`, `distributed`) | Status proses |

### 12. `audit_logs`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas log |
| `user_id` | FK -> `users.id` | Pengguna yang melakukan aksi |
| `action` | VARCHAR | Jenis aksi |
| `entity` | VARCHAR | Entitas yang diubah |
| `entity_id` | UUID | ID entitas |
| `payload` | JSONB | Perubahan detail |
| `created_at` | TIMESTAMP | Waktu kejadian |

### 13. `scheduler_jobs`
| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | PK, UUID | Identitas job |
| `job_type` | ENUM (`reminder_imunisasi`, `backup_laporan`, `sinkronisasi_mobile`) | Jenis job |
| `scheduled_for` | TIMESTAMP | Jadwal eksekusi |
| `status` | ENUM (`menunggu`, `berjalan`, `selesai`, `gagal`) | Status |
| `retry_count` | INTEGER | Jumlah percobaan ulang |
| `last_error` | TEXT | Pesan kesalahan terakhir |

## Fitur Utama
1. **Pendaftaran Warga**
   - Form pendaftaran oleh kader/bidan/admin dengan validasi NIK & kategori layanan.
   - Import massal dari data Dukcapil/Puskesmas (opsional).
2. **Pencatatan Hasil Timbang**
   - Input berat/tinggi balita dan lansia dengan kalkulasi status gizi otomatis (mengacu pada WHO Anthro).
   - Riwayat penimbangan dan grafik pertumbuhan.
3. **Pencatatan Imunisasi**
   - Jadwal imunisasi terintegrasi dengan status pelaksanaan.
   - Cetak kartu imunisasi.
4. **Laporan Otomatis**
   - Laporan bulanan/triwulan dalam format PDF berisi rekap data penimbangan, imunisasi, ibu hamil, dan lansia.
   - Fitur download dan pengiriman email otomatis ke Dinas terkait.
5. **Dashboard Statistik Gizi Anak**
   - Grafik status gizi, tren berat/tinggi, distribusi imunisasi, dan deteksi dini balita gizi buruk.
   - Filter berdasarkan Puskesmas, Posyandu, rentang waktu.
   - Integrasi dengan standar WHO melalui perhitungan z-score otomatis pada backend dan visualisasi heatmap untuk identifikasi wilayah risiko.
6. **Reminder Jadwal Imunisasi**
   - Scheduler mengirim notifikasi ke orang tua/wali melalui SMS/WhatsApp/email H-3 dan H-1.
   - Riwayat reminder tersimpan untuk audit.
   - Admin dapat menyesuaikan template pesan dan jadwal pengiriman ulang otomatis untuk jadwal yang belum ditandai selesai.
7. **Cetak & Distribusi Laporan PDF**
   - Template laporan dapat dikonfigurasi per wilayah (header, logo, tanda tangan digital).
   - Laporan dapat diunduh, dikirim via email resmi, atau dibagikan ke layanan arsip pemerintah.

## Alur Data (Flow)
1. **Autentikasi & Otorisasi**
   - Pengguna login ➜ Frontend mengirim kredensial ke Auth API ➜ Backend memverifikasi & menghasilkan access token + refresh token ➜ Token disimpan secara aman (httpOnly cookie/secure storage) ➜ Setiap request berikutnya melewati middleware RBAC.
2. **Pendaftaran Warga**
   - Kader/Bidan mengisi form ➜ Backend memvalidasi (duplikasi NIK, kategori layanan) ➜ Simpan ke `citizens` dan log di `audit_logs` ➜ Notifikasi push ke admin Puskesmas melalui WebSocket & email.
3. **Penimbangan Balita/Lansia**
   - Petugas memilih warga ➜ Input hasil timbang ➜ Backend menghitung status gizi & z-score ➜ Simpan ke `child_growth_records`/`elderly_health_records` ➜ Scheduler memicu recalculation materialized view ➜ Dashboard diperbarui secara periodik (cron atau event-driven).
4. **Pencatatan Kehamilan & Imunisasi**
   - Bidan membuat jadwal imunisasi (`immunization_schedules`) ➜ Scheduler mendaftarkan job ke `scheduler_jobs` ➜ Reminder dikirim (H-3, H-1, dan H+1 jika terlewat) ➜ Status reminder tercatat di `immunization_reminders` ➜ Saat imunisasi dilaksanakan, catatan disimpan ke `immunization_records` dan jadwal otomatis diperbarui ke `selesai`.
5. **Laporan**
   - Admin memilih periode ➜ Backend menjalankan agregasi data (menggunakan view/statistik) ➜ Template laporan dirender menjadi PDF ➜ File disimpan di storage & metadata di `reports` dengan status `generated` ➜ Admin dapat mengirim ke email Dinas atau mencetak langsung.
6. **Dashboard Statistik**
   - Frontend memanggil API statistik ➜ Backend mengambil data agregat (status gizi, coverage imunisasi) ➜ Response dikirim dengan label wilayah & rentang waktu ➜ Frontend menampilkan grafik, indikator warna, dan rekomendasi tindak lanjut.
7. **Audit & Monitoring**
   - Setiap aksi penting (create/update/delete, reminder gagal) dicatat di `audit_logs` ➜ Super admin memantau melalui modul monitoring untuk memastikan kepatuhan dan integritas data.

## Integrasi & Keamanan
- **Autentikasi**: JWT atau session-based dengan refresh token.
- **Otorisasi**: Middleware role-based memastikan akses sesuai tabel hak akses.
- **Audit Trail**: Log aktivitas penting (create/update/delete) pada tabel `audit_logs` (opsional) untuk pelacakan.
- **Backup & Recovery**: Jadwal backup harian basis data dan penyimpanan di lokasi terpisah.
- **Kepatuhan**: Sesuai standar perlindungan data kesehatan (kebijakan lokal).

## Kebutuhan Non-Fungsional
- **Ketersediaan**: SLA minimal 99% dengan infrastruktur cloud.
- **Kinerja**: API respon < 2 detik untuk operasi pencatatan.
- **Skalabilitas**: Dapat ditingkatkan untuk beberapa Puskesmas/Posyandu dalam satu kabupaten.
- **Akses Offline (Opsional)**: Mode offline pada aplikasi mobile kader dengan sinkronisasi saat online.
- **Keamanan Data**: Enkripsi data sensitif at-rest (TDE) dan in-transit (HTTPS/TLS), serta kepatuhan terhadap regulasi perlindungan data kesehatan.

## Roadmap Implementasi
1. Analisis kebutuhan detail dan desain UI/UX.
2. Pengembangan modul autentikasi dan manajemen pengguna.
3. Implementasi pendaftaran warga dan pencatatan pemeriksaan.
4. Integrasi jadwal dan reminder imunisasi.
5. Pembuatan dashboard statistik dan laporan PDF.
6. Uji coba di satu Posyandu pilot dan pelatihan pengguna.
7. Evaluasi dan rollout bertahap.

## Role Baru "Pasien" & Integrasi Data BPJS

### 1. Desain Role & Hak Akses "Pasien"

**Konsep akses**
- **Boleh**: login, melihat profil sendiri, melihat dan mengedit data pribadi terbatas (nama, kontak), melihat daftar balita yang terhubung, melihat jadwal Posyandu & imunisasi, melihat riwayat kunjungan balita, melihat dan memperbarui data BPJS milik sendiri, mengunggah bukti kepesertaan (jika disediakan upload).
- **Tidak boleh**: mengelola pengguna lain, memodifikasi data balita yang tidak terkait, mengubah jadwal Posyandu, mengakses laporan agregat wilayah, atau mengelola konfigurasi sistem.

**Perbedaan dengan role lain**
- **Admin**: memiliki CRUD master data wilayah/posyandu, manajemen pengguna, laporan wilayah; pasien hanya akses baca data milik sendiri.
- **Bidan**: CRUD catatan kesehatan balita/ibu, validasi data, akses laporan pasien; pasien hanya baca riwayat dan lihat jadwal.
- **Kader**: create/update data lapangan untuk balita wilayah, penandaan kehadiran; pasien hanya menerima informasi dan mengelola profilnya sendiri.

**Perubahan database**
- Jika menggunakan kolom `role` ENUM di tabel `users`:
  ```sql
  ALTER TABLE users
    MODIFY role ENUM('super_admin','admin','bidan','kader','pasien') NOT NULL DEFAULT 'kader';
  ```
- Alternatif tabel `roles` + pivot `user_roles` (lebih fleksibel multi-role):
  ```sql
  CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255)
  );

  CREATE TABLE user_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY user_role_unique (user_id, role_id)
  );
  ```

**Flow login & otorisasi**
- Pisahkan tampilan dashboard: jika role termasuk `admin/bidan/kader` tampilkan dashboard operasional (statistik, tugas input); jika `pasien` tampilkan portal keluarga (profil, balita, jadwal & riwayat).
- Contoh pengecekan role (PHP native):
  ```php
  session_start();
  if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
  $user = $_SESSION['user'];

  function authorize(array $allowedRoles, $user) {
      if (!in_array($user['role'], $allowedRoles, true)) {
          http_response_code(403);
          echo 'Akses ditolak';
          exit;
      }
  }

  // Contoh routing
  if (strpos($_SERVER['REQUEST_URI'], '/dashboard/admin') === 0) {
      authorize(['super_admin','admin','bidan','kader'], $user);
      include 'dashboard_admin.php';
  } else {
      authorize(['pasien','bidan','kader','admin','super_admin'], $user);
      include $user['role'] === 'pasien' ? 'dashboard_pasien.php' : 'dashboard_admin.php';
  }
  ```

### 2. Desain Integrasi Data BPJS

**Desain database**
- Relasi: satu `users` (role pasien) memiliki satu `bpjs_profiles`; satu pasien dapat memiliki beberapa `balita` melalui FK `balita.user_id` (orang tua). `bpjs_profiles.user_id` mengacu ke akun pasien, bukan per balita.
- Contoh tabel:
  ```sql
  CREATE TABLE bpjs_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    no_bpjs VARCHAR(30) NOT NULL,
    status_bpjs ENUM('aktif','tidak_aktif','tidak_diketahui') DEFAULT 'tidak_diketahui',
    jenis_bpjs ENUM('PBI','Mandiri','PPU','PPK','Lainnya') DEFAULT 'Lainnya',
    faskes_tingkat_1 VARCHAR(150),
    tanggal_validasi DATE,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_no_bpjs (no_bpjs),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );
  ```

**Form input & update BPJS (UI sederhana)**
- Wajib: `no_bpjs`, `status_bpjs`, `jenis_bpjs`, `faskes_tingkat_1` (minimal nama klinik), `tanggal_validasi`.
- Opsional: `keterangan`.
- Contoh form HTML (pendaftaran pasien):
  ```html
  <form action="save_bpjs.php" method="post">
    <label>No BPJS/KIS*</label><input name="no_bpjs" required>
    <label>Status*</label>
      <select name="status_bpjs">
        <option value="aktif">Aktif</option>
        <option value="tidak_aktif">Tidak Aktif</option>
        <option value="tidak_diketahui">Tidak Diketahui</option>
      </select>
    <label>Jenis*</label>
      <select name="jenis_bpjs">
        <option>PBI</option><option>Mandiri</option><option>PPU</option><option>PPK</option><option>Lainnya</option>
      </select>
    <label>Faskes Tingkat 1*</label><input name="faskes_tingkat_1" required>
    <label>Tanggal Validasi*</label><input type="date" name="tanggal_validasi" required>
    <label>Keterangan</label><textarea name="keterangan"></textarea>
    <button type="submit">Simpan</button>
  </form>
  ```
- Contoh pemrosesan PHP (insert/update):
  ```php
  // save_bpjs.php
  require 'db.php';
  session_start();
  $userId = $_SESSION['user']['id'];

  $stmt = $pdo->prepare("REPLACE INTO bpjs_profiles (user_id, no_bpjs, status_bpjs, jenis_bpjs, faskes_tingkat_1, tanggal_validasi, keterangan)
    VALUES (:user_id, :no_bpjs, :status_bpjs, :jenis_bpjs, :faskes, :tgl, :ket)");
  $stmt->execute([
    ':user_id' => $userId,
    ':no_bpjs' => $_POST['no_bpjs'],
    ':status_bpjs' => $_POST['status_bpjs'],
    ':jenis_bpjs' => $_POST['jenis_bpjs'],
    ':faskes' => $_POST['faskes_tingkat_1'],
    ':tgl' => $_POST['tanggal_validasi'],
    ':ket' => $_POST['keterangan'] ?? null,
  ]);
  header('Location: profil.php?success=bpjs');
  ```

**Penggunaan data BPJS di modul lain**
- Profil pasien: tampilkan ringkasan status, faskes, tanggal validasi, dan tombol "Cek Status BPJS".
- Riwayat kunjungan Posyandu: kolom `status_bpjs_saat_kunjungan` dapat diisi snapshot dari `bpjs_profiles.status_bpjs` saat kunjungan dicatat.
- Contoh query daftar balita beserta status BPJS orang tua:
  ```sql
  SELECT b.id, b.nama_balita, b.tanggal_lahir, u.name AS nama_ortu, bp.status_bpjs
  FROM balita b
  JOIN users u ON b.user_id = u.id
  LEFT JOIN bpjs_profiles bp ON bp.user_id = u.id;
  ```
- Contoh query detail pasien + BPJS:
  ```sql
  SELECT u.*, bp.no_bpjs, bp.status_bpjs, bp.jenis_bpjs, bp.faskes_tingkat_1, bp.tanggal_validasi, bp.keterangan
  FROM users u
  LEFT JOIN bpjs_profiles bp ON bp.user_id = u.id
  WHERE u.id = ?;
  ```

### 3. Persiapan Integrasi API BPJS (Level Dasar)

**Field tambahan yang disiapkan**
- `bpjs_reference_id` (VARCHAR): ID referensi dari sistem BPJS/PCare.
- `last_synced_at` atau `last_bpjs_check_at` (TIMESTAMP): waktu pengecekan terakhir.
- `source_system` ENUM('manual','api'): asal data.
- `last_response_payload` (JSON/TEXT opsional): menyimpan respon terakhir untuk audit.

**Contoh fungsi cek status (pseudocode cURL)**
```php
function cekStatusBpjs($noBpjs) {
  $ch = curl_init('https://api.bpjs-pcare.local/peserta');
  $payload = json_encode(['no_bpjs' => $noBpjs]);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'Authorization: Bearer DUMMY_TOKEN',
    ],
    CURLOPT_POSTFIELDS => $payload,
  ]);
  $response = curl_exec($ch);
  if ($response === false) { throw new Exception(curl_error($ch)); }
  $data = json_decode($response, true);
  curl_close($ch);

  // Update database
  $stmt = $pdo->prepare("UPDATE bpjs_profiles
      SET status_bpjs = :status, jenis_bpjs = :jenis, faskes_tingkat_1 = :faskes,
          bpjs_reference_id = :ref, last_bpjs_check_at = NOW(), source_system = 'api'
      WHERE no_bpjs = :no_bpjs");
  $stmt->execute([
    ':status' => $data['status'] ?? 'tidak_diketahui',
    ':jenis' => $data['jenis'] ?? 'Lainnya',
    ':faskes' => $data['faskes'] ?? null,
    ':ref' => $data['reference_id'] ?? null,
    ':no_bpjs' => $noBpjs,
  ]);
  return $data;
}
```

**Alur bisnis sederhana**
1) Admin/bidan klik "Cek Status BPJS" pada profil pasien.
2) Sistem memanggil fungsi cURL (atau API resmi) dengan parameter `no_bpjs`/NIK.
3) Response JSON ditampilkan (status, jenis, faskes) dan tabel `bpjs_profiles` diupdate beserta timestamp sinkronisasi.

### 4. Integrasi Role "Pasien" dengan Data BPJS
- **Login & akses**: pasien login seperti role lain; middleware/guard mengarahkan ke `dashboard_pasien.php` yang memuat kartu profil, daftar balita, dan blok ringkasan BPJS.
- **Relasi**: satu pasien → banyak balita (`balita.user_id = users.id`), satu `bpjs_profiles` → satu pasien.
- **Query balita + status BPJS untuk pasien tertentu**:
  ```sql
  SELECT b.*, bp.status_bpjs, bp.no_bpjs
  FROM balita b
  JOIN users u ON u.id = b.user_id
  LEFT JOIN bpjs_profiles bp ON bp.user_id = u.id
  WHERE u.id = :pasien_id;
  ```
- **Portal pasien**: halaman profil menampilkan data pribadi, tabel anak, kartu BPJS (status, faskes, tanggal validasi), tombol ubah BPJS, dan riwayat kunjungan balita dengan kolom status BPJS saat kunjungan.

### 5. Implementasi PHP Native (Langkah Konkret)

**Struktur folder minimal**
- `config/db.php`: koneksi PDO.
- `auth/login.php`, `auth/logout.php`: proses login/logout.
- `middleware/auth.php`: pengecekan session dan role guard.
- `dashboard_admin.php`, `dashboard_pasien.php`: tampilan terpisah.
- `bpjs/create.php`, `bpjs/update.php`: form & handler BPJS.
- `balita/index.php`: daftar balita milik pasien (filtered by `user_id`).

**Snippet middleware role guard**
```php
// middleware/auth.php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: /auth/login.php');
  exit;
}

function allowRoles(array $roles) {
  $current = $_SESSION['user']['role'];
  if (!in_array($current, $roles, true)) {
    http_response_code(403);
    echo 'Akses ditolak';
    exit;
  }
}

function redirectDashboard() {
  $role = $_SESSION['user']['role'];
  header('Location: ' . ($role === 'pasien' ? '/dashboard_pasien.php' : '/dashboard_admin.php'));
  exit;
}
```

**Contoh login handler (hash & session)**
```php
// auth/login.php
require __DIR__.'/../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1');
  $stmt->execute([':email' => $_POST['email']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($_POST['password'], $user['password_hash'])) {
    $_SESSION['user'] = $user;
    redirectDashboard();
  } else {
    $error = 'Email atau password salah';
  }
}
```

**Template dashboard pasien (elemen kunci)**
- Kartu ringkasan profil: nama, kontak, alamat, status akun.
- Kartu BPJS: nomor, status, jenis, faskes, tanggal validasi, tombol "Ubah" & "Cek Status".
- Tabel balita terkait: nama, tanggal lahir, jadwal imunisasi berikutnya, status kehadiran terakhir.
- Riwayat kunjungan: tanggal kunjungan, berat/tinggi, status gizi, status BPJS saat kunjungan.

Contoh skeleton HTML:
```html
<!-- dashboard_pasien.php -->
<?php require 'middleware/auth.php'; allowRoles(['pasien']); ?>
<h1>Halo, <?= htmlspecialchars($_SESSION['user']['name']) ?></h1>

<section>
  <h2>Data BPJS</h2>
  <?php include 'bpjs/summary_card.php'; ?>
  <a href="/bpjs/update.php" class="btn">Ubah BPJS</a>
  <a href="/bpjs/check.php" class="btn">Cek Status BPJS</a>
</section>

<section>
  <h2>Daftar Balita</h2>
  <?php include 'balita/list.php'; ?>
</section>
```

**Form BPJS (update)**
```php
// bpjs/update.php
require __DIR__.'/../middleware/auth.php';
require __DIR__.'/../config/db.php';
allowRoles(['pasien']);

$stmt = $pdo->prepare('SELECT * FROM bpjs_profiles WHERE user_id = :uid LIMIT 1');
$stmt->execute([':uid' => $_SESSION['user']['id']]);
$bpjs = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<form method="post" action="/bpjs/save.php">
  <input type="hidden" name="id" value="<?= $bpjs['id'] ?? '' ?>">
  <label>No BPJS*</label><input name="no_bpjs" required value="<?= htmlspecialchars($bpjs['no_bpjs'] ?? '') ?>">
  <label>Status*</label>
  <select name="status_bpjs" required>
    <?php foreach(['aktif','tidak_aktif','tidak_diketahui'] as $opt): ?>
      <option value="<?= $opt ?>" <?= ($bpjs['status_bpjs'] ?? '') === $opt ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ', $opt)) ?></option>
    <?php endforeach; ?>
  </select>
  <label>Jenis*</label>
  <select name="jenis_bpjs" required>
    <?php foreach(['PBI','Mandiri','PPU','PPK','Lainnya'] as $opt): ?>
      <option value="<?= $opt ?>" <?= ($bpjs['jenis_bpjs'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
    <?php endforeach; ?>
  </select>
  <label>Faskes Tingkat 1*</label><input name="faskes_tingkat_1" required value="<?= htmlspecialchars($bpjs['faskes_tingkat_1'] ?? '') ?>">
  <label>Tanggal Validasi*</label><input type="date" name="tanggal_validasi" required value="<?= htmlspecialchars($bpjs['tanggal_validasi'] ?? '') ?>">
  <label>Keterangan</label><textarea name="keterangan"><?= htmlspecialchars($bpjs['keterangan'] ?? '') ?></textarea>
  <button type="submit">Simpan</button>
</form>
```

**Handler save BPJS (menghindari REPLACE jika ingin audit)**
```php
// bpjs/save.php
require __DIR__.'/../middleware/auth.php';
require __DIR__.'/../config/db.php';
allowRoles(['pasien']);

$sql = 'INSERT INTO bpjs_profiles
  (user_id, no_bpjs, status_bpjs, jenis_bpjs, faskes_tingkat_1, tanggal_validasi, keterangan, source_system)
  VALUES (:uid, :no, :status, :jenis, :faskes, :tgl, :ket, :source)
  ON DUPLICATE KEY UPDATE
    status_bpjs = VALUES(status_bpjs),
    jenis_bpjs = VALUES(jenis_bpjs),
    faskes_tingkat_1 = VALUES(faskes_tingkat_1),
    tanggal_validasi = VALUES(tanggal_validasi),
    keterangan = VALUES(keterangan),
    source_system = VALUES(source_system),
    updated_at = CURRENT_TIMESTAMP';

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':uid' => $_SESSION['user']['id'],
  ':no' => $_POST['no_bpjs'],
  ':status' => $_POST['status_bpjs'],
  ':jenis' => $_POST['jenis_bpjs'],
  ':faskes' => $_POST['faskes_tingkat_1'],
  ':tgl' => $_POST['tanggal_validasi'],
  ':ket' => $_POST['keterangan'] ?? null,
  ':source' => 'manual',
]);

header('Location: /dashboard_pasien.php?success=bpjs');
exit;
```

**Query riwayat kunjungan balita dengan status BPJS saat itu**
```sql
SELECT k.id, k.tanggal_kunjungan, k.berat, k.tinggi, k.status_gizi,
       COALESCE(k.status_bpjs_saat_kunjungan, bp.status_bpjs) AS status_bpjs_kunjungan
FROM kunjungan_balita k
JOIN balita b ON b.id = k.balita_id
JOIN users u ON u.id = b.user_id
LEFT JOIN bpjs_profiles bp ON bp.user_id = u.id
WHERE u.id = :pasien_id
ORDER BY k.tanggal_kunjungan DESC;
```

## Kesimpulan
Rancangan ini memberikan kerangka komprehensif untuk membangun sistem informasi Posyandu berbasis web dengan fitur-fitur kunci yang dibutuhkan Puskesmas. Dengan arsitektur fullstack, manajemen data terpusat, dan fitur reminder serta pelaporan otomatis, Puskesmas dapat meningkatkan akurasi pencatatan dan pengambilan keputusan berbasis data.
