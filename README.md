# SI Posyandu - Aplikasi Web PHP

Aplikasi informasi Posyandu berbasis PHP (tanpa framework) yang dapat dijalankan di XAMPP. Fitur utama:

- Autentikasi multi peran: Super Admin, Admin Puskesmas, Bidan, Kader.
- Pendaftaran warga (ibu hamil, balita, lansia).
- Pencatatan penimbangan beserta status gizi otomatis.
- Manajemen imunisasi dan reminder jadwal.
- Dashboard statistik ringkas.
- Cetak laporan PDF sederhana.

## Persyaratan

- PHP 8.1+
- MySQL/MariaDB
- XAMPP atau stack LAMP/WAMP serupa

## Instalasi

1. Salin seluruh folder proyek ini ke direktori `htdocs` (misal `C:/xampp/htdocs/si_posyandu`).
2. Import basis data menggunakan berkas `sql/schema.sql` melalui phpMyAdmin atau terminal.
3. Sesuaikan kredensial database di `app/config.php` jika diperlukan.
4. (Opsional) Setel variabel lingkungan `APP_BASE_URL` bila aplikasi tidak berjalan langsung dari direktori `public` (contoh: `http://localhost/si_posyandu/public`).
5. Akses aplikasi melalui URL sesuai penempatan Anda.

### Konfigurasi Midtrans

Tambahkan variabel lingkungan berikut (contoh di file `.env` atau konfigurasi server) tanpa menyimpan kunci asli di repositori:

```
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_MERCHANT_ID=your-merchant-id
```

Nilai bawaan di `app/config.php` menggunakan placeholder (`__MIDTRANS_CLIENT_KEY__`, dll). Gantilah dengan kredensial asli sebelum menjalankan pembayaran di lingkungan produksi.

### Uji Webhook Midtrans

1. Jalankan server lokal aplikasi (mis. `php -S localhost:8000 -t public`).
2. Jalankan `ngrok http 8000` lalu catat URL publik HTTPS.
3. Setel URL webhook di dashboard Midtrans ke `https://<ngrok-domain>/index.php?page=midtrans-webhook`.
4. Lakukan transaksi Snap; Midtrans akan memanggil endpoint tersebut dan status pembayaran akan diperbarui otomatis.

### Akun Awal

| Email | Password | Role |
| --- | --- | --- |
| `super@posyandu.test` | `password` | Super Admin |

## Struktur Folder

- `public/` : Front controller dan aset statis.
- `app/Controllers` : Logika routing halaman.
- `app/Models` : Akses data menggunakan PDO.
- `app/Views` : Template antarmuka dengan Bootstrap.
- `app/Libraries/SimplePdf.php` : Utility pembentuk PDF sederhana.
- `sql/schema.sql` : Skrip pembuatan database dan akun awal.

## Catatan

- Untuk pengiriman reminder (SMS/WhatsApp/Email) masih berupa pencatatan jadwal; integrasi gateway dapat ditambahkan kemudian.
- Komponen PDF menggunakan generator sederhana internal. Untuk tampilan lebih kaya, dapat diganti dengan pustaka seperti Dompdf/FPDF jika diinginkan.
- Variabel lingkungan `APP_TIMEZONE` dapat digunakan bila zona waktu server berbeda dengan kebutuhan operasional.
