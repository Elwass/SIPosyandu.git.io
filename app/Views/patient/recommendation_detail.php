<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php
$user = user();
$isPatient = ($user['role'] ?? '') === 'pasien';
$initialPaymentStatus = strtoupper($latestOrder['payment_status'] ?? 'UNPAID');
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
                    <div class="surface-body">
                        <?php if (!$latestOrder): ?>
                            <div class="alert alert-info mb-0">Belum ada pesanan pemenuhan untuk rekomendasi ini.</div>
                        <?php else: ?>
                            <p class="mb-1 text-muted">Metode Pemenuhan</p>
                            <p class="fw-semibold mb-2"><?= htmlspecialchars($latestOrder['fulfillment_method']) ?></p>
                            <?php if ($latestOrder['fulfillment_method'] === 'DELIVERY' && !empty($latestOrder['address'])): ?>
                                <p class="mb-1 text-muted">Alamat</p>
                                <p class="fw-semibold mb-2"><?= nl2br(htmlspecialchars($latestOrder['address'])) ?></p>
                            <?php endif; ?>
                            <p class="mb-1 text-muted">Status Pembayaran</p>
                            <p class="fw-semibold mb-2"><span class="badge bg-soft-secondary text-secondary" id="payment-status"><?= htmlspecialchars($initialPaymentStatus) ?></span></p>
                            <p class="mb-1 text-muted">Total</p>
                            <h5 class="mb-3">Rp<?= number_format($latestOrder['total_amount'] ?? $subtotal, 0, ',', '.') ?></h5>

                            <?php if ($latestOrder['fulfillment_method'] === 'SELF_BUY'): ?>
                                <div class="alert alert-info">Metode <strong>Self Buy</strong> dipilih. Pembayaran online tidak diperlukan.</div>
                            <?php endif; ?>

                            <div class="d-grid gap-2">
                                <?php $showPayButton = ($initialPaymentStatus === 'UNPAID' || $initialPaymentStatus === 'PENDING') && ($latestOrder['fulfillment_method'] ?? '') !== 'SELF_BUY'; ?>
                                <?php if ($showPayButton): ?>
                                    <?php if (!$clientKey): ?>
                                        <div class="alert alert-danger">Client Key Midtrans belum diisi di app/config.php</div>
                                    <?php endif; ?>
                                    <button class="btn btn-success" id="pay-button" type="button" <?= $clientKey ? '' : 'disabled' ?>>Bayar Sekarang</button>
                                <?php endif; ?>
                                <button class="btn btn-outline-secondary" id="refresh-button" type="button" <?= $currentFulfillmentId ? '' : 'disabled' ?>>Perbarui Status</button>
                                <?php if ($currentFulfillmentId): ?>
                                    <a class="btn btn-outline-primary" href="<?= url('?page=order-payment-detail&id=' . $currentFulfillmentId) ?>">Lihat Detail Pesanan</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php if ($isPatient && $currentFulfillmentId): ?>
    <?php if (($latestOrder['fulfillment_method'] ?? '') !== 'SELF_BUY' && $clientKey): ?>
        <script src="<?= $snapUrl ?>" data-client-key="<?= htmlspecialchars($clientKey) ?>"></script>
    <?php endif; ?>
    <script>
        const payButton = document.getElementById('pay-button');
        const refreshButton = document.getElementById('refresh-button');
        const paymentStatusEl = document.getElementById('payment-status');
        const fulfillmentOrderId = <?= (int) $currentFulfillmentId ?>;
        const detailBaseUrl = '<?= url('?page=order-payment-detail&id=') ?>';

        async function parseJsonResponse(response) {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (error) {
                console.error('Respon bukan JSON:', text);
                throw new Error('Respon pembayaran tidak valid');
            }
        }

        async function createPayment() {
            if (!payButton) return;
            payButton.disabled = true;
            payButton.textContent = 'Memproses...';
            try {
                const res = await fetch('<?= url('?action=payment-create') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ fulfillment_order_id: fulfillmentOrderId })
                });
                const data = await parseJsonResponse(res);
                if (!res.ok || data.success === false) {
                    throw new Error(data.message || 'Gagal membuat pembayaran');
                }

                if (!data.token) {
                    window.location = detailBaseUrl + fulfillmentOrderId;
                    return;
                }

                if (typeof window.snap === 'undefined') {
                    throw new Error('Snap.js belum termuat');
                }

                const redirectToDetail = () => window.location = detailBaseUrl + fulfillmentOrderId;
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

        async function refreshStatus() {
            if (!fulfillmentOrderId) return;
            try {
                const res = await fetch('<?= url('?action=payment-status') ?>' + '&fulfillment_order_id=' + fulfillmentOrderId);
                const data = await parseJsonResponse(res);
                if (!res.ok || data.success === false) {
                    throw new Error(data.message || 'Gagal memeriksa status');
                }
                paymentStatusEl.textContent = data.payment_status;
            } catch (error) {
                alert(error.message);
            }
        }

        if (payButton) {
            payButton.addEventListener('click', createPayment);
        }
        if (refreshButton) {
            refreshButton.addEventListener('click', refreshStatus);
        }
    </script>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
