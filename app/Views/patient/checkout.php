<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Checkout</span>
                <h2 class="section-title">Metode Ambil & Ringkasan</h2>
            </div>
            <a class="btn btn-outline-primary" href="<?= url('?page=cart') ?>">Kembali ke Keranjang</a>
        </div>
        <?php if (!$items): ?>
            <div class="alert alert-info">Keranjang kosong. Silakan pilih produk terlebih dahulu.</div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="surface-card">
                        <div class="surface-header"><h4>Detail Pengambilan</h4></div>
                        <div class="surface-body">
                            <form method="post" action="<?= url('?page=orders-checkout') ?>">
                                <div class="mb-3">
                                    <label class="form-label">Metode</label>
                                    <select class="form-select" name="pickup_method" id="pickup_method">
                                        <option value="PICKUP">PICKUP (Ambil di lokasi)</option>
                                        <option value="DELIVERY">DELIVERY (Dikirim)</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="delivery_address" style="display:none;">
                                    <label class="form-label">Alamat Pengiriman</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Tulis alamat lengkap"></textarea>
                                </div>
                                <button class="btn btn-primary" type="submit">Lanjutkan Pembayaran</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="surface-card">
                        <div class="surface-header"><h4>Ringkasan</h4></div>
                        <div class="surface-body">
                            <ul class="list-group list-group-flush mb-3">
                                <?php foreach ($items as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><?= htmlspecialchars($item['product']['name']) ?> (x<?= $item['quantity'] ?>)</span>
                                        <span>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="d-flex justify-content-between fw-semibold">
                                <span>Total</span>
                                <span>Rp<?= number_format($total, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<script>
    const pickupSelect = document.getElementById('pickup_method');
    const deliveryField = document.getElementById('delivery_address');
    if (pickupSelect) {
        const toggleAddress = () => {
            deliveryField.style.display = pickupSelect.value === 'DELIVERY' ? 'block' : 'none';
        };
        pickupSelect.addEventListener('change', toggleAddress);
        toggleAddress();
    }
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
