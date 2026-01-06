<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Pembayaran</span>
            <h2 class="section-title">Selesaikan Transaksi</h2>
            <p class="section-subtitle">Proses Midtrans Snap akan muncul. Jangan tutup halaman ini.</p>
        </div>
        <div class="surface-card">
            <div class="surface-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Order #<?= $order['id'] ?></h5>
                    <p class="text-muted mb-0">Total: Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></p>
                </div>
                <a class="btn btn-outline-primary" href="<?= url('?page=orders') ?>">Lihat Pesanan</a>
            </div>
            <div class="surface-body">
                <p>Klik tombol di bawah bila popup pembayaran tidak otomatis muncul.</p>
                <button id="pay-button" class="btn btn-primary">Bayar Sekarang</button>
            </div>
        </div>
    </div>
</section>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= htmlspecialchars($clientKey) ?>"></script>
<script>
    const token = '<?= $snapToken ?>';
    const redirectUrl = '<?= url('?page=orders') ?>';
    function openSnap() {
        window.snap.pay(token, {
            onSuccess: function () { window.location = redirectUrl; },
            onPending: function () { window.location = redirectUrl; },
            onError: function () { window.location = redirectUrl; },
            onClose: function () { window.location = redirectUrl; }
        });
    }
    document.getElementById('pay-button').addEventListener('click', openSnap);
    openSnap();
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
