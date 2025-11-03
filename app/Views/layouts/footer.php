    </main>
    <footer class="site-footer" id="contact">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-5">
                    <h4 class="site-footer__title">Tetap terhubung dengan Posyandu</h4>
                    <p class="site-footer__subtitle">Monitoring gizi, imunisasi, dan laporan warga sekarang lebih mudah dan terintegrasi.</p>
                </div>
                <div class="col-lg-4">
                    <ul class="footer-links">
                        <li><span class="footer-label">Email</span><a href="mailto:cs@posyandu.id">cs@posyandu.id</a></li>
                        <li><span class="footer-label">Telepon</span><a href="tel:+622112345678">+62 21 1234 5678</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 text-lg-end">
                    <div class="footer-badge">Terintegrasi dengan Puskesmas</div>
                </div>
            </div>
            <div class="footer-meta">
                <span>&copy; <?= date('Y') ?> <?= htmlspecialchars(config('app.name', 'SI Posyandu')) ?>.</span>
                <span>Didukung oleh kader, bidan, dan admin Puskesmas.</span>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
