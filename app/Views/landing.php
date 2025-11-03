<?php include __DIR__ . '/layouts/header.php'; ?>
<section class="hero" id="hero">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <div class="hero-badge">Sistem Informasi Posyandu Terintegrasi</div>
                <h1 class="hero-title">Pelayanan Posyandu Kini <span>Lebih Mudah</span> Untuk Semua</h1>
                <p class="hero-text">Kelola pendaftaran warga, penimbangan gizi, jadwal imunisasi, dan laporan otomatis dalam satu platform berbasis web.</p>
                <div class="hero-actions">
                    <a href="<?= url('?page=login') ?>" class="btn btn-primary btn-lg">Masuk Dashboard</a>
                    <a href="#features" class="btn btn-outline-primary btn-lg">Jelajahi Fitur</a>
                </div>
                <div class="hero-stats">
                    <div>
                        <span class="stat-number">900+</span>
                        <span class="stat-label">Warga Terdata</span>
                    </div>
                    <div>
                        <span class="stat-number">4500+</span>
                        <span class="stat-label">Kunjungan Posyandu</span>
                    </div>
                    <div>
                        <span class="stat-number">99.7%</span>
                        <span class="stat-label">Data Tepat Waktu</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-illustration">
                    <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=900&q=80" alt="Petugas kesehatan" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="features">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Fitur Utama</span>
            <h2 class="section-title">Step by step solusi digital Posyandu</h2>
            <p class="section-subtitle">Mulai dari pendaftaran hingga laporan terintegrasi dengan hak akses sesuai peran.</p>
        </div>
        <div class="row gy-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon gradient-1">01</div>
                    <h3>Pendaftaran Warga</h3>
                    <p>Registrasi ibu hamil, balita, dan lansia dengan data demografis lengkap dan riwayat kunjungan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon gradient-2">02</div>
                    <h3>Pencatatan Penimbangan</h3>
                    <p>Input berat, tinggi, serta status gizi anak secara berkala dengan ringkasan tren otomatis.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon gradient-3">03</div>
                    <h3>Manajemen Imunisasi</h3>
                    <p>Pantau jadwal imunisasi, kirim reminder, dan tandai penyelesaian vaksinasi secara real-time.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="services">
    <div class="container">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-5">
                <span class="section-eyebrow">Fungsi Tambahan</span>
                <h2 class="section-title">Layanan terbaik dari Posyandu digital</h2>
                <p class="section-subtitle">Super admin, admin Puskesmas, bidan, dan kader mendapatkan akses sesuai tugas untuk memastikan pelayanan lancar.</p>
                <ul class="list-unstyled feature-list">
                    <li>Dashboard statistik gizi anak dan rekap kunjungan.</li>
                    <li>Reminder jadwal imunisasi via SMS/WhatsApp siap dikirim.</li>
                    <li>Cetak laporan PDF otomatis untuk kebutuhan pelaporan.</li>
                    <li>Audit log aktivitas untuk keamanan dan akuntabilitas.</li>
                </ul>
            </div>
            <div class="col-lg-7">
                <div class="service-grid">
                    <div class="service-card">
                        <h4>Admin Puskesmas</h4>
                        <p>Kelola data master, laporan lintas Posyandu, dan monitoring reminder.</p>
                    </div>
                    <div class="service-card">
                        <h4>Bidan</h4>
                        <p>Validasi data kesehatan ibu &amp; anak, memantau penimbangan, dan tindak lanjut kasus gizi.</p>
                    </div>
                    <div class="service-card">
                        <h4>Kader Posyandu</h4>
                        <p>Input data lapangan, jadwalkan imunisasi, dan catat kehadiran warga.</p>
                    </div>
                    <div class="service-card">
                        <h4>Super Admin</h4>
                        <p>Atur pengguna, hak akses, konfigurasi reminder, dan integrasi Puskesmas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--highlight">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6">
                <div class="highlight-card">
                    <h2>Ingatkan Jadwal Imunisasi Secara Otomatis</h2>
                    <p>Kader dapat membuat jadwal imunisasi dan mengirim pengingat kepada orang tua langsung dari dashboard.</p>
                    <div class="highlight-actions">
                        <a href="<?= url('?page=login') ?>" class="btn btn-light">Mulai Sekarang</a>
                        <span class="text-muted">Tidak perlu instalasi tambahan.</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="highlight-media">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80" alt="Jadwal imunisasi" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="testimonials">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Cerita Pengguna</span>
            <h2 class="section-title">Kesan mereka tentang SI Posyandu</h2>
        </div>
        <div class="row gy-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p>“Kini rekap laporan bulanan bisa selesai dalam hitungan menit. Data lebih rapi dan mudah diverifikasi.”</p>
                    <span class="testimonial-author">Nurhaliza - Admin Puskesmas</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p>“Reminder imunisasi membantu orang tua hadir tepat waktu. Anak-anak jadi tidak tertinggal vaksinasi.”</p>
                    <span class="testimonial-author">Rina - Kader Posyandu</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p>“Dashboard gizi memudahkan pemantauan kasus stunting dan tindakan cepat di lapangan.”</p>
                    <span class="testimonial-author">Dr. Ahmad - Bidan Koordinator</span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/layouts/footer.php'; ?>
