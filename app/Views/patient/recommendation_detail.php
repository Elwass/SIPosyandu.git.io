<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php
$user = user();
$isPatient = ($user['role'] ?? '') === 'pasien';
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
                <div class="surface-card" id="payment-card">
                    <div class="surface-header">
                        <h5 class="mb-0">Pemenuhan & Pembayaran</h5>
                    </div>
                    <div class="surface-card" id="payment-section">
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
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Total</p>
                                <h5 class="mb-0">Rp<?= number_format($subtotal, 0, ',', '.') ?></h5>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" id="pay-button" type="submit">Bayar Sekarang</button>
                                <button class="btn btn-outline-secondary" id="refresh-button" type="button" <?= $currentFulfillmentId ? '' : 'disabled' ?>>Perbarui Status</button>
                                <?php if ($currentFulfillmentId): ?>
                                    <a class="btn btn-outline-primary" href="<?= url('?page=order-payment-detail&id=' . $currentFulfillmentId) ?>">Lihat Detail Pesanan</a>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Total</p>
                                <h5 class="mb-0">Rp<?= number_format($subtotal, 0, ',', '.') ?></h5>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" id="pay-button" type="submit">Bayar Sekarang</button>
                                <button class="btn btn-outline-secondary" id="refresh-button" type="button" <?= $currentFulfillmentId ? '' : 'disabled' ?>>Perbarui Status</button>
                                <?php if ($currentFulfillmentId): ?>
                                    <a class="btn btn-outline-primary" href="<?= url('?page=order-payment-detail&id=' . $currentFulfillmentId) ?>">Lihat Detail Pesanan</a>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted d-block mt-2">Pembayaran online hanya diproses di halaman detail pesanan.</small>
                        </form>
                        <div class="alert alert-info d-none mt-3 mb-0" id="self-buy-info">Metode <strong>Self Buy</strong> dipilih. Silakan beli obat secara mandiri sesuai rekomendasi.</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php if ($isPatient): ?>
<script>
const form = document.getElementById('fulfillment-form');
const payButton = document.getElementById('pay-button');
const processButton = document.getElementById('process-button');
const refreshButton = document.getElementById('refresh-button');
const fulfillmentMethod = document.getElementById('fulfillment_method');
const deliveryAddress = document.getElementById('delivery-address');
const selfBuyInfo = document.getElementById('self-buy-info');
const recommendationStatusEl = document.getElementById('recommendation-status');
const paymentStatusEl = document.getElementById('payment-status');
const detailBaseUrl = '<?= url('?page=order-payment-detail&id=') ?>';
let currentOrderId = <?= $currentFulfillmentId ? (int) $currentFulfillmentId : 'null' ?>;

function toggleFulfillmentUi() {
    const method = fulfillmentMethod.value;
    if (method === 'DELIVERY') {
        deliveryAddress.classList.remove('d-none');
    } else {
        deliveryAddress.classList.add('d-none');
    }

    if (method === 'SELF_BUY') {
        selfBuyInfo.classList.remove('d-none');
        payButton.textContent = 'Simpan & Lihat Pesanan';
    } else {
        selfBuyInfo.classList.add('d-none');
        payButton.textContent = 'Bayar Sekarang';
    }
}

function setButtonState(loading = false) {
    const paid = paymentStatusEl.textContent.toUpperCase() === 'PAID';
    payButton.disabled = loading || paid;
}

async function createOrderAndRedirect() {
    setButtonState(true);
    const payload = {
        recommendation_id: form.recommendation_id.value,
        fulfillment_method: fulfillmentMethod.value,
        address: form.address ? form.address.value : '',
        request_snap: false
    };

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

        currentOrderId = data.fulfillment_order_id || data.order_id || currentOrderId;
        if (!currentOrderId) {
            throw new Error('Pesanan belum terbentuk.');
        }

        window.location = detailBaseUrl + currentOrderId;
    } catch (error) {
        alert(error.message);
        setButtonState(false);
    }
}

form.addEventListener('submit', (e) => {
    e.preventDefault();
    createOrderAndRedirect();
});

refreshButton.addEventListener('click', () => {
    if (!currentOrderId) {
        alert('Belum ada pesanan yang dibuat.');
        return;
    }
    window.location = detailBaseUrl + currentOrderId;
});

toggleFulfillmentUi();
setButtonState(false);
<?php if ($currentFulfillmentId): ?>
refreshButton.disabled = false;
<?php endif; ?>
</script>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
