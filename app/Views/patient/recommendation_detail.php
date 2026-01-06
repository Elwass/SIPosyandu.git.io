<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php $appConfig = app_config(); $clientKey = $appConfig['midtrans']['client_key'] ?? ''; ?>
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
                            <h5 class="mb-0"><span class="badge bg-soft-secondary text-secondary" id="payment-status">UNPAID</span></h5>
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
                                    <option value="PICKUP">Pickup (ambil di posyandu)</option>
                                    <option value="DELIVERY">Delivery (antar)</option>
                                    <option value="SELF_BUY">Self Buy (beli sendiri)</option>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="delivery-address">
                                <label class="form-label">Alamat Pengantaran</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="Alamat lengkap"></textarea>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Buat Pesanan / Proses</button>
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
                            <button class="btn btn-success" id="pay-button" type="button" disabled>Bayar Sekarang</button>
                            <button class="btn btn-outline-secondary" id="refresh-button" type="button" disabled>Refresh Status</button>
                        </div>
                        <small class="text-muted d-block mt-2">Pembayaran online berlaku untuk Pickup & Delivery.</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= htmlspecialchars($clientKey) ?>"></script>
<script>
const form = document.getElementById('fulfillment-form');
const payButton = document.getElementById('pay-button');
const refreshButton = document.getElementById('refresh-button');
const fulfillmentMethod = document.getElementById('fulfillment_method');
const deliveryAddress = document.getElementById('delivery-address');
let currentOrderId = null;
let paymentStatusEl = document.getElementById('payment-status');

fulfillmentMethod.addEventListener('change', () => {
    if (fulfillmentMethod.value === 'DELIVERY') {
        deliveryAddress.classList.remove('d-none');
    } else {
        deliveryAddress.classList.add('d-none');
    }
});

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
        recommendation_id: form.recommendation_id.value,
        fulfillment_method: fulfillmentMethod.value,
        address: form.address ? form.address.value : ''
    };
    const res = await fetch('<?= url('?action=create-fulfillment-order') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.order_id) {
        currentOrderId = data.order_id;
        if (fulfillmentMethod.value === 'SELF_BUY') {
            paymentStatusEl.textContent = 'UNPAID';
        } else {
            payButton.disabled = false;
            refreshButton.disabled = false;
        }
        alert('Pesanan berhasil dibuat dengan ID ' + currentOrderId);
    } else {
        alert(data.message || 'Gagal membuat pesanan');
    }
});

payButton.addEventListener('click', async () => {
    if (!currentOrderId) return alert('Buat pesanan terlebih dahulu');
    const res = await fetch('<?= url('?action=pay-fulfillment-order') ?>&id=' + currentOrderId, { method: 'POST' });
    const data = await res.json();
    if (data.token) {
        window.snap.pay(data.token, {
            onSuccess: function (result) { paymentStatusEl.textContent = 'PAID'; },
            onPending: function () { paymentStatusEl.textContent = 'PENDING'; },
            onError: function (err) { alert('Pembayaran gagal'); },
            onClose: function () { console.log('Popup ditutup'); }
        });
    } else {
        alert(data.message || 'Gagal memulai pembayaran');
    }
});

refreshButton.addEventListener('click', async () => {
    if (!currentOrderId) return alert('Buat pesanan terlebih dahulu');
    const res = await fetch('<?= url('?action=check-fulfillment-status') ?>&id=' + currentOrderId, { method: 'POST' });
    const data = await res.json();
    if (data.payment_status) {
        paymentStatusEl.textContent = data.payment_status;
    } else {
        alert(data.message || 'Gagal cek status');
    }
});
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
