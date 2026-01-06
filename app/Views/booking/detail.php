<?php
$appConfig = require __DIR__ . '/../../config.php';
$midtransClientKey = $appConfig['midtrans']['client_key'] ?? '';
$midtransSnapBase = !empty($appConfig['midtrans']['is_production'])
    ? 'https://app.midtrans.com'
    : 'https://app.sandbox.midtrans.com';
$status = strtolower($booking['payment_status'] ?? 'pending');

$badgeClass = [
    'paid' => 'success',
    'failed' => 'danger',
    'pending' => 'warning',
][$status] ?? 'secondary';
?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Detail Booking</span>
                <h2 class="section-title">Kode: <?= htmlspecialchars($booking['booking_code']) ?></h2>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge text-bg-<?= $badgeClass ?>" id="status-badge">
                    <?= $status === 'paid' ? 'Paid' : ($status === 'failed' ? 'Failed' : 'Pending Payment') ?>
                </span>
                <a class="btn btn-outline-secondary" href="<?= url('/') ?>">Kembali ke Home</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="surface-card mb-4">
                    <div class="surface-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Informasi Customer</h4>
                    </div>
                    <div class="surface-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Nama</div>
                                <div class="fw-semibold"><?= htmlspecialchars($booking['user_name']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold"><?= htmlspecialchars($booking['email']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Telepon</div>
                                <div class="fw-semibold"><?= htmlspecialchars($booking['phone']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="surface-card mb-4">
                    <div class="surface-header">
                        <h4 class="mb-0">Detail Booking</h4>
                    </div>
                    <div class="surface-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Layanan</div>
                                <div class="fw-semibold"><?= htmlspecialchars($booking['service_name']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Paket</div>
                                <div class="fw-semibold"><?= htmlspecialchars($booking['package_name'] ?? '-') ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Tanggal</div>
                                <div class="fw-semibold"><?= htmlspecialchars($booking['booking_date']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Waktu</div>
                                <div class="fw-semibold"><?= htmlspecialchars($booking['booking_time']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Order ID</div>
                                <div class="fw-semibold" id="order-id-text"><?= htmlspecialchars($booking['order_id']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="surface-card">
                    <div class="surface-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Informasi Pembayaran</h4>
                    </div>
                    <div class="surface-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Harga</span>
                            <span class="fw-semibold">Rp<?= number_format((float) $booking['total_price'], 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Metode Pembayaran</span>
                            <span class="fw-semibold">Down Payment</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Nominal DP</span>
                            <span class="fw-semibold">Rp<?= number_format((float) $booking['down_payment'], 0, ',', '.') ?></span>
                        </div>
                        <?php if (trim($midtransClientKey) === ''): ?>
                            <div class="alert alert-danger">Konfigurasi Midtrans tidak lengkap: Client Key belum diset di app/config.php.</div>
                        <?php else: ?>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" id="pay-button" <?= $status === 'paid' ? 'disabled' : '' ?>>Lanjutkan Pembayaran</button>
                                <button class="btn btn-outline-secondary" id="refresh-status">Refresh Status</button>
                            </div>
                            <div class="small text-muted mt-2" id="payment-message"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if (trim($midtransClientKey) !== ''): ?>
<script>
    const bookingCode = '<?= htmlspecialchars($booking['booking_code'], ENT_QUOTES) ?>';
    const statusBadge = document.getElementById('status-badge');
    const payButton = document.getElementById('pay-button');
    const refreshButton = document.getElementById('refresh-status');
    const paymentMessage = document.getElementById('payment-message');

    function setStatus(status) {
        let text = 'Pending Payment';
        let badge = 'text-bg-warning';
        if (status === 'paid') {
            text = 'Paid';
            badge = 'text-bg-success';
        } else if (status === 'failed') {
            text = 'Failed';
            badge = 'text-bg-danger';
        }
        statusBadge.textContent = text;
        statusBadge.className = 'badge ' + badge;
        if (status === 'paid' && payButton) {
            payButton.disabled = true;
        }
    }

    async function startPayment() {
        if (!payButton) return;
        payButton.disabled = true;
        paymentMessage.textContent = 'Memulai pembayaran...';
        try {
            const response = await fetch('<?= url('?action=booking-pay&code=') ?>' + encodeURIComponent(bookingCode), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!response.ok || !data.token) {
                throw new Error(data.error || 'Gagal memulai pembayaran');
            }

            window.snap.pay(data.token, {
                onSuccess: function () {
                    setStatus('paid');
                    paymentMessage.textContent = 'Pembayaran berhasil.';
                },
                onPending: function () {
                    setStatus('pending');
                    paymentMessage.textContent = 'Pembayaran masih menunggu penyelesaian.';
                },
                onError: function () {
                    setStatus('failed');
                    paymentMessage.textContent = 'Terjadi kesalahan pada pembayaran.';
                },
                onClose: function () {
                    paymentMessage.textContent = 'Popup ditutup sebelum menyelesaikan pembayaran.';
                    payButton.disabled = false;
                }
            });
        } catch (err) {
            payButton.disabled = false;
            paymentMessage.textContent = err.message;
        }
    }

    async function refreshStatus() {
        if (!refreshButton) return;
        refreshButton.disabled = true;
        paymentMessage.textContent = 'Memeriksa status pembayaran...';
        try {
            const response = await fetch('<?= url('?action=booking-check-status&code=') ?>' + encodeURIComponent(bookingCode), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!response.ok || !data.status) {
                throw new Error(data.error || 'Gagal memeriksa status');
            }
            setStatus(data.status);
            paymentMessage.textContent = 'Status terbaru: ' + data.status.toUpperCase();
        } catch (err) {
            paymentMessage.textContent = err.message;
        } finally {
            refreshButton.disabled = false;
        }
    }

    if (payButton) {
        payButton.addEventListener('click', (e) => {
            e.preventDefault();
            startPayment();
        });
    }

    if (refreshButton) {
        refreshButton.addEventListener('click', (e) => {
            e.preventDefault();
            refreshStatus();
        });
    }
</script>
<script src="<?= htmlspecialchars($midtransSnapBase) ?>/snap/snap.js" data-client-key="<?= htmlspecialchars($midtransClientKey) ?>"></script>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
