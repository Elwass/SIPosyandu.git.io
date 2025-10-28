# Rancangan Sistem Informasi Posyandu Berbasis Web

## Ringkasan
Sistem informasi Posyandu berbasis web ditujukan untuk membantu Puskesmas mengatasi kendala pencatatan manual pada layanan ibu hamil, balita, dan lansia. Sistem ini mengintegrasikan pencatatan data warga, pemeriksaan rutin, imunisasi, laporan otomatis, serta dashboard statistik gizi anak. Arsitektur fullstack diterapkan dengan pemisahan antara frontend, backend, dan basis data untuk memastikan skalabilitas dan keamanan.

## Arsitektur Sistem Fullstack
- **Frontend (Web App)**
  - Framework SPA (mis. React/Vue) untuk pengalaman pengguna interaktif.
  - Komponen utama: autentikasi, pendaftaran warga, form pencatatan, dashboard statistik, manajemen jadwal imunisasi, dan modul laporan.
  - Integrasi dengan API backend via REST/GraphQL.
- **Backend (Service API)**
  - Framework berbasis Node.js/Express atau Laravel untuk pengelolaan bisnis proses.
  - Fitur: autentikasi & otorisasi berbasis role, manajemen data warga, pencatatan hasil timbang dan imunisasi, mesin reminder, generator laporan, API statistik.
- **Database**
  - RDBMS (PostgreSQL/MySQL) dengan relasi kuat antar entitas warga, pemeriksaan, imunisasi, dan jadwal.
- **Layanan Pendukung**
  - **Job Scheduler**: mengirim reminder jadwal imunisasi via SMS/WA/email.
  - **PDF Service**: modul server-side untuk menghasilkan laporan PDF (mis. menggunakan wkhtmltopdf atau library sejenis).
  - **Storage**: penyimpanan file hasil export laporan.

## Role & Hak Akses
| Role | Hak Akses Utama |
| --- | --- |
| **Super Admin (Dinkes/Puskesmas Pusat)** | Manajemen master data (kader, bidan, desa), pengaturan template laporan, monitoring seluruh Posyandu, akses penuh ke statistik agregat. |
| **Admin Puskesmas** | Manajemen data warga dalam wilayah kerja, pengaturan jadwal Posyandu, persetujuan hasil pencatatan kader/bidan, generate laporan Puskesmas, monitoring dashboard wilayah. |
| **Bidan** | Input dan verifikasi data pemeriksaan ibu hamil, persalinan, serta imunisasi bayi/balita; melihat riwayat kesehatan pasien; memberikan catatan medis. |
| **Kader Posyandu** | Registrasi warga baru, pencatatan hasil penimbangan balita dan lansia, input kehadiran imunisasi lapangan, melihat jadwal Posyandu dan reminder tugas. |
| **Warga (Opsional via Portal)** | Melihat jadwal imunisasi anak, riwayat penimbangan, dan menerima reminder. |

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
6. **Reminder Jadwal Imunisasi**
   - Scheduler mengirim notifikasi ke orang tua/wali melalui SMS/WhatsApp/email H-3 dan H-1.
   - Riwayat reminder tersimpan untuk audit.

## Alur Data (Flow)
1. **Pendaftaran Warga**
   - Kader/Bidan mengisi form ➜ Backend memvalidasi data ➜ Simpan ke `citizens` ➜ Notifikasi ke admin Puskesmas.
2. **Penimbangan Balita/Lansia**
   - Petugas memilih warga ➜ Input hasil timbang ➜ Backend hitung status gizi ➜ Simpan ke `child_growth_records`/`elderly_health_records` ➜ Dashboard diperbarui.
3. **Pencatatan Kehamilan & Imunisasi**
   - Bidan membuat jadwal imunisasi (`immunization_schedules`) ➜ Scheduler memonitor jadwal ➜ Reminder dikirim ➜ Setelah imunisasi, catatan disimpan ke `immunization_records` ➜ Jadwal diperbarui menjadi selesai.
4. **Laporan**
   - Admin memilih periode ➜ Backend mengagregasi data ➜ Generator PDF membuat file ➜ Laporan disimpan di `reports` & tersedia untuk diunduh.
5. **Dashboard Statistik**
   - Frontend memanggil API statistik ➜ Backend melakukan agregasi (mis. status gizi per Posyandu) ➜ Data divisualisasikan dengan grafik.

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
