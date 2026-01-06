<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php
$user = user();
$isPatient = ($user['role'] ?? '') === 'pasien';
$appConfig = app_config();
$clientKey = $appConfig['midtrans']['client_key'] ?? '';
$isProduction = (bool) ($appConfig['midtrans']['is_production'] ?? false);
$snapUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
$showPayButton = $isPatient && $order['fulfillment_method'] !== 'SELF_BUY' && strtoupper((string) $order['payment_status']) !== 'PAID';
?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Pembayaran & Pemesanan</span>
                <h2 class="section-title">Order #<?= htmlspecialchars($order['id']) ?></h2>
                <p class="section-subtitle mb-0">Metode: <?= htmlspecialchars($order['fulfillment_method']) ?> | Status Pembayaran: <?= htmlspecialchars($order['payment_status']) ?></p>
            </div>
            <a class="btn btn-outline-secondary" href="<?= url('?page=recommendation-detail&id=' . $order['recommendation_id']) ?>">Kembali ke Rekomendasi</a>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="surface-card mb-3">
                    <div class="surface-body d-flex justify-content-between flex-wrap gap-3 align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Status Rekomendasi</p>
                            <h5 class="mb-0"><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($order['recommendation_status']) ?></span></h5>
                        </div>
                        <div>
                            <p class="mb-1 text-muted">Status Pembayaran</p>
                            <h5 class="mb-0"><span class="badge bg-soft-secondary text-secondary" id="payment-status"><?= htmlspecialchars($order['payment_status']) ?></span></h5>
                        </div>
                    </div>
                </div>

                <div class="surface-card mb-3">
                    <div class="surface-header">
                        <h5 class="mb-0">Data Warga</h5>
                    </div>
                    <div class="surface-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Nama</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($order['resident_name'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Kategori</p>
                                <p class="fw-semibold mb-0 text-uppercase"><?= htmlspecialchars($order['category'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">NIK</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($order['nik'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Telepon</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($order['phone'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="surface-card">
                    <div class="surface-header">
                        <h5 class="mb-0">Rincian Item</h5>
                    </div>
                    <div class="surface-body">
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Obat</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subtotal = 0; foreach ($recommendation['items'] as $item): $line = ((int)$item['qty']) * ((int)$item['price']); $subtotal += $line; ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['medicine_name']) ?></td>
                                            <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                                            <td><?= (int)$item['qty'] ?> <?= htmlspecialchars($item['unit']) ?></td>
                                            <td>Rp<?= number_format($line, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Subtotal Obat</th>
                                        <th>Rp<?= number_format($subtotal, 0, ',', '.') ?></th>
                                    </tr>
                                    <?php if ($order['fulfillment_method'] === 'DELIVERY'): ?>
                                        <tr>
                                            <th colspan="3">Biaya Pengantaran</th>
                                            <th>Rp<?= number_format($order['delivery_fee'], 0, ',', '.') ?></th>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th>Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php if ($order['fulfillment_method'] === 'SELF_BUY'): ?>
                            <div class="alert alert-info mb-0 mt-3">Metode <strong>Self Buy</strong> dipilih. Pasien membeli obat secara mandiri sesuai rekomendasi.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="surface-card">
                    <div class="surface-header">
                        <h5 class="mb-0">Pemenuhan</h5>
                    </div>
                    <div class="surface-body">
                        <p class="mb-1 text-muted">Metode</p>
                        <p class="fw-semibold"><?= htmlspecialchars($order['fulfillment_method']) ?></p>
                        <?php if ($order['fulfillment_method'] === 'DELIVERY'): ?>
                            <p class="mb-1 text-muted">Alamat Pengantaran</p>
                            <p class="fw-semibold"><?= nl2br(htmlspecialchars($order['address'] ?? '-')) ?></p>
                        <?php endif; ?>
                        <p class="mb-1 text-muted">Dibuat</p>
                        <p class="fw-semibold"><?= htmlspecialchars($order['created_at'] ?? '-') ?></p>
                        <?php if (!empty($order['midtrans_order_id'])): ?>
                            <p class="mb-1 text-muted">Midtrans Order</p>
                            <p class="fw-semibold"><?= htmlspecialchars($order['midtrans_order_id']) ?></p>
                        <?php endif; ?>
                        <?php if ($isPatient && $order['fulfillment_method'] === 'SELF_BUY'): ?>
                            <div class="alert alert-secondary mt-3 mb-0">Tidak ada pembayaran online. Silakan lakukan pembelian mandiri.</div>
                        <?php endif; ?>
                        <?php if ($showPayButton): ?>
                            <?php if (!$clientKey): ?>
                                <div class="alert alert-danger">Client Key Midtrans belum dikonfigurasi.</div>
                            <?php endif; ?>
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-success" id="pay-button" type="button" <?= $clientKey ? '' : 'disabled' ?>>Bayar Sekarang</button>
                                <button class="btn btn-outline-secondary" id="refresh-button" type="button">Perbarui Status</button>
                            </div>
                        <?php elseif ($isPatient): ?>
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-outline-secondary" id="refresh-button" type="button">Perbarui Status</button>
                            </div>
                        <?php endif; ?>
                        <?php if ($isPatient && strtoupper((string) $order['payment_status']) === 'PAID'): ?>
                            <div class="alert alert-success mt-3 mb-0">Pembayaran sudah diterima.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if ($showPayButton && $clientKey): ?>
<script src="<?= $snapUrl ?>" data-client-key="<?= htmlspecialchars($clientKey) ?>"></script>
<?php endif; ?>
<?php if ($isPatient): ?>
<script>
const paymentStatusEl = document.getElementById('payment-status');
const payButton = document.getElementById('pay-button');
const refreshButton = document.getElementById('refresh-button');
const fulfillmentOrderId = <?= (int) $order['id'] ?>;
const recommendationId = <?= (int) $order['recommendation_id'] ?>;
const fulfillmentMethod = "<?= $order['fulfillment_method'] ?>";
const hasClientKey = Boolean("<?= $clientKey ?>");

async function createPaymentToken() {
    if (!hasClientKey && fulfillmentMethod !== 'SELF_BUY') {
        alert('Client key Midtrans belum dikonfigurasi.');
        return;
    }

    const payload = {
        recommendation_id: recommendationId,
        fulfillment_method: fulfillmentMethod,
        fulfillment_order_id: fulfillmentOrderId,
        address: <?= json_encode($order['address'] ?? '') ?>,
        delivery_fee: <?= (int) $order['delivery_fee'] ?>
    };

    payButton.disabled = true;
    payButton.textContent = 'Memproses...';
    try {
        const res = await fetch('<?= url('?action=payment-create') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.error || 'Gagal membuat pembayaran');
        }

        if (!data.token) {
            window.location = '<?= url('?page=order-payment-detail&id=' . $order['id']) ?>';
            return;
        }

        if (typeof window.snap === 'undefined') {
            alert('Snap.js belum termuat.');
            return;
        }

        window.snap.pay(data.token, {
            onSuccess: function () { syncStatusAndRedirect(); },
            onPending: function () { paymentStatusEl.textContent = 'PENDING'; },
            onError: function () { alert('Pembayaran gagal.'); },
            onClose: function () { payButton.disabled = false; payButton.textContent = 'Bayar Sekarang'; }
        });
    } catch (error) {
        alert(error.message);
        payButton.disabled = false;
        payButton.textContent = 'Bayar Sekarang';
    }
}

async function syncStatusAndRedirect() {
    try {
        const res = await fetch('<?= url('?action=payment-sync-status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fulfillment_order_id: fulfillmentOrderId })
        });
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.error || 'Gagal cek status');
        }
        paymentStatusEl.textContent = data.payment_status;
        if (data.redirect_url) {
            window.location = data.redirect_url;
        }
    } catch (error) {
        alert(error.message);
    }
}

if (payButton) {
    payButton.addEventListener('click', createPaymentToken);
}
if (refreshButton) {
    refreshButton.addEventListener('click', syncStatusAndRedirect);
}
</script>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
