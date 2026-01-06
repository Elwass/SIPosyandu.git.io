<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Detail Rekomendasi</span>
                <h2 class="section-title">REC-<?= htmlspecialchars($recommendation['id'] ?? 'N/A') ?></h2>
            </div>
            <a class="btn btn-outline-secondary" href="<?= url('?page=recommendations') ?>">Kembali</a>
        </div>

        <?php if (!$recommendation): ?>
            <div class="alert alert-danger">Rekomendasi tidak ditemukan.</div>
        <?php else: ?>
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="surface-card mb-3">
                        <div class="surface-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <p class="mb-1 text-muted">Status Rekomendasi</p>
                                <form class="d-flex align-items-center gap-2 flex-wrap" method="POST" action="<?= url('?action=recommendation-update-status') ?>">
                                    <input type="hidden" name="recommendation_id" value="<?= (int) $recommendation['id'] ?>">
                                    <select class="form-select form-select-sm" name="status">
                                        <?php foreach (['SENT', 'CONFIRMED', 'FULFILLED', 'CANCELLED'] as $status): ?>
                                            <option value="<?= $status ?>" <?= $recommendation['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-primary btn-sm" type="submit">Update</button>
                                </form>
                            </div>
                            <div>
                                <p class="mb-1 text-muted">Status Pembayaran</p>
                                <h5 class="mb-0"><span class="badge bg-soft-secondary text-secondary"><?= htmlspecialchars($latestOrder['payment_status'] ?? 'UNPAID') ?></span></h5>
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

                    <div class="surface-card">
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
                            <h5 class="mb-0">Pemenuhan</h5>
                        </div>
                        <div class="surface-body">
                            <?php if ($latestOrder): ?>
                                <p class="mb-1 text-muted">Metode</p>
                                <p class="fw-semibold"><?= htmlspecialchars($latestOrder['fulfillment_method']) ?></p>
                                <?php if ($latestOrder['fulfillment_method'] === 'DELIVERY'): ?>
                                    <p class="mb-1 text-muted">Alamat Pengantaran</p>
                                    <p class="fw-semibold"><?= nl2br(htmlspecialchars($latestOrder['address'] ?? '-')) ?></p>
                                <?php endif; ?>
                                <p class="mb-1 text-muted">Total</p>
                                <p class="fw-semibold">Rp<?= number_format($latestOrder['total_amount'], 0, ',', '.') ?></p>
                                <p class="mb-1 text-muted">Status Pembayaran</p>
                                <p class="fw-semibold mb-1"><?= htmlspecialchars($latestOrder['payment_status'] ?? 'UNPAID') ?></p>
                                <?php if (!empty($latestOrder['midtrans_order_id'])): ?>
                                    <p class="mb-1 text-muted">Midtrans Order</p>
                                    <p class="fw-semibold mb-1"><?= htmlspecialchars($latestOrder['midtrans_order_id']) ?></p>
                                <?php endif; ?>
                                <p class="mb-1 text-muted">Dibuat</p>
                                <p class="fw-semibold"><?= htmlspecialchars($latestOrder['created_at'] ?? '-') ?></p>
                                <div class="d-grid mt-3">
                                    <a class="btn btn-outline-primary" href="<?= url('?page=order-payment-detail&id=' . $latestOrder['id']) ?>">Lihat Detail Pesanan</a>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">Belum ada pesanan pemenuhan dibuat oleh pasien.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
