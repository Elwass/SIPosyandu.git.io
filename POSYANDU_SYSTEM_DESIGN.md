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

## Kesimpulan
Rancangan ini memberikan kerangka komprehensif untuk membangun sistem informasi Posyandu berbasis web dengan fitur-fitur kunci yang dibutuhkan Puskesmas. Dengan arsitektur fullstack, manajemen data terpusat, dan fitur reminder serta pelaporan otomatis, Puskesmas dapat meningkatkan akurasi pencatatan dan pengambilan keputusan berbasis data.
