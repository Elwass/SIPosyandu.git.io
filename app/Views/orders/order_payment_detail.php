<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php
$user = user();
$isPatient = ($user['role'] ?? '') === 'pasien';
$clientKey = $clientKey ?? '';
$snapUrl = $snapUrl ?? 'https://app.sandbox.midtrans.com/snap/snap.js';
$hasClientKey = isset($clientKey) && $clientKey !== '';
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
                        <h5 class="mb-0">Pemenuhan & Pembayaran</h5>
                    </div>
                    <div class="surface-body">
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Metode</p>
                            <p class="fw-semibold mb-0"><?= htmlspecialchars($order['fulfillment_method']) ?></p>
                        </div>
                        <?php if ($order['fulfillment_method'] === 'DELIVERY'): ?>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Alamat Pengantaran</p>
                                <p class="fw-semibold mb-0"><?= nl2br(htmlspecialchars($order['address'] ?? '-')) ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Total Pembayaran</p>
                            <h5 class="mb-0">Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></h5>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Dibuat</p>
                            <p class="fw-semibold mb-0"><?= htmlspecialchars($order['created_at'] ?? '-') ?></p>
                        </div>
                        <?php if (!empty($order['midtrans_order_id'])): ?>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Midtrans Order</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($order['midtrans_order_id']) ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if ($isPatient && $order['fulfillment_method'] === 'SELF_BUY'): ?>
                            <div class="alert alert-secondary mt-3 mb-0">Tidak ada pembayaran online. Silakan lakukan pembelian mandiri.</div>
                        <?php endif; ?>
                        <?php if ($isPatient): ?>
                            <div class="d-grid gap-2 mt-3">
                                <?php if ($showPayButton): ?>
                                    <?php if (!$hasClientKey): ?>
                                        <div class="alert alert-danger">Client Key Midtrans belum dikonfigurasi.</div>
                                    <?php endif; ?>
                                    <button class="btn btn-success" id="pay-button" type="button" <?= $hasClientKey ? '' : 'disabled' ?>>Bayar Sekarang</button>
                                <?php endif; ?>
                                <button class="btn btn-outline-secondary" id="refresh-button" type="button">Perbarui Status</button>
                                <a class="btn btn-outline-primary" href="<?= url('?page=order-payment-detail&id=' . $order['id']) ?>">Lihat Detail Pesanan</a>
                            </div>
                            <?php if ($showPayButton && !$hasClientKey): ?>
                                <small class="text-muted d-block mt-2">Aktifkan client key Midtrans untuk melanjutkan pembayaran.</small>
                            <?php endif; ?>
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
<?php if ($showPayButton && $hasClientKey): ?>
<script src="<?= $snapUrl ?>" data-client-key="<?= htmlspecialchars($clientKey) ?>"></script>
<?php endif; ?>
<?php if ($isPatient): ?>
<script>
const paymentStatusEl = document.getElementById('payment-status');
const payButton = document.getElementById('pay-button');
const refreshButton = document.getElementById('refresh-button');
const fulfillmentOrderId = <?= (int) $order['id'] ?>;
const hasClientKey = <?= $hasClientKey ? 'true' : 'false' ?>;
const detailUrl = '<?= url('?page=order-payment-detail&id=' . $order['id']) ?>';

async function parseJsonResponse(response) {
    const text = await response.text();
    try {
        const data = JSON.parse(text);
        return { response, data };
    } catch (error) {
        console.error(text);
        alert('Gagal membaca respon server. Periksa endpoint pembayaran (mungkin mengembalikan HTML error).');
        return null;
    }
}

async function createPaymentToken() {
    if (!hasClientKey && "<?= $order['fulfillment_method'] ?>" !== 'SELF_BUY') {
        alert('Client key Midtrans belum dikonfigurasi.');
        return;
    }

    payButton.disabled = true;
    payButton.textContent = 'Memproses...';
    try {
        const res = await fetch('<?= url('?action=payment-create') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'fulfillment_order_id=' + encodeURIComponent(fulfillmentOrderId)
        });
        const parsed = await parseJsonResponse(res);
        if (!parsed) {
            payButton.disabled = false;
            payButton.textContent = 'Bayar Sekarang';
            return;
        }

        const { data } = parsed;
        if (!parsed.response.ok || !data.success) {
            alert(data.message || 'Gagal membuat pembayaran');
            payButton.disabled = false;
            payButton.textContent = 'Bayar Sekarang';
            return;
        }

        if (!data.token) {
            alert(data.message || 'Token pembayaran tidak tersedia');
            payButton.disabled = false;
            payButton.textContent = 'Bayar Sekarang';
            return;
        }

        if (typeof window.snap === 'undefined') {
            alert('Snap.js belum termuat.');
            payButton.disabled = false;
            payButton.textContent = 'Bayar Sekarang';
            return;
        }

        const redirectToDetail = () => window.location = detailUrl;
        window.snap.pay(data.token, {
            onSuccess: redirectToDetail,
            onPending: redirectToDetail,
            onError: redirectToDetail,
            onClose: () => {
                payButton.disabled = false;
                payButton.textContent = 'Bayar Sekarang';
            }
        });
    } catch (error) {
        alert(error.message);
        payButton.disabled = false;
        payButton.textContent = 'Bayar Sekarang';
    }
}

async function syncStatusAndRedirect() {
    try {
        const res = await fetch('<?= url('?action=payment-status') ?>' + '&fulfillment_order_id=' + fulfillmentOrderId);
        const parsed = await parseJsonResponse(res);
        if (!parsed) {
            return;
        }

        const { data } = parsed;
        if (!parsed.response.ok || !data.success) {
            throw new Error(data.message || 'Gagal cek status');
        }
        paymentStatusEl.textContent = data.payment_status;
        window.location = detailUrl;
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
