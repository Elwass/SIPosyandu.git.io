<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php
$appConfig = app_config();
$clientKey = $appConfig['midtrans']['client_key'] ?? '';
$isProduction = (bool) ($appConfig['midtrans']['is_production'] ?? false);
$snapUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
$initialPaymentStatus = $latestOrder['payment_status'] ?? 'UNPAID';
$currentFulfillmentId = $latestOrder['id'] ?? null;
$currentFulfillmentMethod = $latestOrder['fulfillment_method'] ?? null;
?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Rekomendasi Obat</span>
                <h2 class="section-title">Detail Rekomendasi REC-<?= $recommendation['id'] ?? 'N/A' ?></h2>
            </div>
            <a class="btn btn-outline-secondary" href="<?= url('?page=patient-recommendations') ?>">Kembali</a>
        </div>
        <?php if (!$recommendation): ?>
            <div class="alert alert-danger">Rekomendasi tidak ditemukan.</div>
        <?php else: ?>
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="surface-card mb-3">
                    <div class="surface-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted">Status Rekomendasi</p>
                            <h5 class="mb-0"><span class="badge bg-soft-primary text-primary" id="recommendation-status"><?= htmlspecialchars($recommendation['status']) ?></span></h5>
                        </div>
                        <div>
                            <p class="mb-1 text-muted">Pembayaran</p>
                            <h5 class="mb-0"><span class="badge bg-soft-secondary text-secondary" id="payment-status"><?= htmlspecialchars($initialPaymentStatus) ?></span></h5>
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
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($recommendation['resident_name'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Kategori</p>
                                <p class="fw-semibold mb-0 text-uppercase"><?= htmlspecialchars($recommendation['category'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">NIK</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($recommendation['nik'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Telepon</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($recommendation['phone'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="surface-card mb-3">
                    <div class="surface-header">
                        <h5 class="mb-0">Detail Obat</h5>
                    </div>
                    <div class="surface-body">
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Obat</th>
                                        <th>Qty</th>
                                        <th>Aturan Pakai</th>
                                        <th>Catatan</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $subtotal = 0; foreach ($recommendation['items'] as $item): $line = ((int)$item['qty']) * ((int)$item['price']); $subtotal += $line; ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="<?= url($item['image']) ?>" alt="<?= htmlspecialchars($item['medicine_name']) ?>" style="width:60px;height:60px;object-fit:cover;" class="rounded">
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($item['medicine_name']) ?></td>
                                            <td><?= (int)$item['qty'] ?> <?= htmlspecialchars($item['unit']) ?></td>
                                            <td><?= htmlspecialchars($item['dosage']) ?></td>
                                            <td><?= htmlspecialchars($item['note']) ?></td>
                                            <td>Rp<?= number_format($line, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">Subtotal</th>
                                        <th>Rp<?= number_format($subtotal, 0, ',', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="surface-card mb-3">
                    <div class="surface-header">
                        <h5 class="mb-0">Proses Rekomendasi</h5>
                    </div>
                    <div class="surface-body">
                        <form id="fulfillment-form">
                            <input type="hidden" name="recommendation_id" value="<?= $recommendation['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Metode Pemenuhan</label>
                                <select class="form-select" name="fulfillment_method" id="fulfillment_method" required>
                                    <option value="PICKUP" <?= $currentFulfillmentMethod === 'PICKUP' ? 'selected' : '' ?>>Pickup (ambil di posyandu)</option>
                                    <option value="DELIVERY" <?= $currentFulfillmentMethod === 'DELIVERY' ? 'selected' : '' ?>>Delivery (antar)</option>
                                    <option value="SELF_BUY" <?= $currentFulfillmentMethod === 'SELF_BUY' ? 'selected' : '' ?>>Self Buy (beli sendiri)</option>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="delivery-address">
                                <label class="form-label">Alamat Pengantaran</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="Alamat lengkap"></textarea>
                            </div>
                            <button class="btn btn-primary w-100" type="submit" id="process-button">Bayar Sekarang</button>
                        </form>
                    </div>
                </div>
                <div class="surface-card">
                    <div class="surface-header">
                        <h5 class="mb-0">Pembayaran</h5>
                    </div>
                    <div class="surface-body">
                        <?php if (!$clientKey): ?>
                            <div class="alert alert-danger">Client Key Midtrans belum diisi di app/config.php</div>
                        <?php endif; ?>
                        <p class="mb-2">Total: <strong id="payment-total">Rp<?= number_format($subtotal, 0, ',', '.') ?></strong></p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" id="pay-button" type="button">Bayar Sekarang</button>
                            <button class="btn btn-outline-secondary" id="refresh-button" type="button" <?= $currentFulfillmentId ? '' : 'disabled' ?>>Refresh Status</button>
                        </div>
                        <small class="text-muted d-block mt-2">Pembayaran online berlaku untuk Pickup & Delivery.</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php if ($clientKey): ?>
<script src="<?= $snapUrl ?>" data-client-key="<?= htmlspecialchars($clientKey) ?>"></script>
<?php endif; ?>
<script>
const form = document.getElementById('fulfillment-form');
const payButton = document.getElementById('pay-button');
const refreshButton = document.getElementById('refresh-button');
const fulfillmentMethod = document.getElementById('fulfillment_method');
const deliveryAddress = document.getElementById('delivery-address');
const recommendationStatusEl = document.getElementById('recommendation-status');
let currentOrderId = <?= $currentFulfillmentId ? (int) $currentFulfillmentId : 'null' ?>;
let paymentStatusEl = document.getElementById('payment-status');
let hasClientKey = Boolean("<?= $clientKey ?>");

fulfillmentMethod.addEventListener('change', () => {
    if (fulfillmentMethod.value === 'DELIVERY') {
        deliveryAddress.classList.remove('d-none');
    } else {
        deliveryAddress.classList.add('d-none');
    }

    if (!hasClientKey && fulfillmentMethod.value !== 'SELF_BUY') {
        payButton.disabled = true;
    } else if (!payButton.disabled) {
        payButton.disabled = false;
    }
});

function setPayButtonLoading(isLoading) {
    const requiresGateway = fulfillmentMethod.value !== 'SELF_BUY';
    payButton.disabled = isLoading || (requiresGateway && !hasClientKey);
    payButton.textContent = isLoading ? 'Memproses...' : 'Bayar Sekarang';
}

async function createPaymentAndPay(eventSource = 'button') {
    if (!hasClientKey && fulfillmentMethod.value !== 'SELF_BUY') {
        alert('Client key Midtrans belum dikonfigurasi.');
        return;
    }

    const payload = {
        recommendation_id: form.recommendation_id.value,
        fulfillment_method: fulfillmentMethod.value,
        address: form.address ? form.address.value : ''
    };

    setPayButtonLoading(true);
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

        currentOrderId = data.fulfillment_order_id || currentOrderId;
        paymentStatusEl.textContent = data.token ? 'PENDING' : 'UNPAID';
        refreshButton.disabled = !currentOrderId;

        if (!data.token) {
            alert('Pesanan tercatat. Silakan proses pembelian mandiri.');
            return;
        }

        if (typeof window.snap === 'undefined') {
            alert('Snap.js belum termuat. Periksa konfigurasi client key.');
            return;
        }

        window.snap.pay(data.token, {
            onSuccess: function () { syncStatus(); },
            onPending: function () { paymentStatusEl.textContent = 'PENDING'; },
            onError: function () { alert('Pembayaran gagal. Coba lagi.'); },
            onClose: function () { setPayButtonLoading(false); }
        });
    } catch (error) {
        alert(error.message);
    } finally {
        if (eventSource !== 'snap-close') {
            setPayButtonLoading(false);
        }
    }
}

async function syncStatus(showAlert = false) {
    if (!currentOrderId) {
        if (showAlert) alert('Belum ada pesanan yang dibuat.');
        return;
    }

    try {
        const res = await fetch('<?= url('?action=payment-sync-status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fulfillment_order_id: currentOrderId })
        });
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.error || 'Gagal cek status');
        }
        paymentStatusEl.textContent = data.payment_status;
        if (data.recommendation_status) {
            recommendationStatusEl.textContent = data.recommendation_status;
        }
        if (showAlert) {
            alert('Status diperbarui: ' + data.payment_status);
        }
    } catch (error) {
        alert(error.message);
    }
}

form.addEventListener('submit', (e) => {
    e.preventDefault();
    createPaymentAndPay();
});

payButton.addEventListener('click', () => {
    createPaymentAndPay();
});

refreshButton.addEventListener('click', () => {
    syncStatus(true);
});

if (!hasClientKey) {
    const requiresGateway = fulfillmentMethod.value !== 'SELF_BUY';
    if (requiresGateway) {
        payButton.disabled = true;
    }
}

<?php if ($currentFulfillmentId): ?>
refreshButton.disabled = false;
<?php endif; ?>
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
