<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Rekomendasi Obat</span>
                <h2 class="section-title">Buat Rekomendasi</h2>
            </div>
        </div>
        <form method="post" action="<?= url('?page=admin-recommendations-store') ?>" class="surface-card">
            <div class="surface-body">
                <div class="mb-3">
                    <label class="form-label">Pilih Warga</label>
                    <select name="resident_id" class="form-select" required>
                        <option value="">-- Pilih Warga --</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['name']) ?> (<?= htmlspecialchars($patient['category']) ?>) - <?= htmlspecialchars($patient['nik']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Keluhan/diagnosa"></textarea>
                </div>
                <h6 class="fw-bold">Obat</h6>
                <div id="items">
                    <div class="row g-3 mb-3 item-row">
                        <div class="col-md-4">
                            <label class="form-label">Obat</label>
                            <select class="form-select" name="items[0][medicine_id]" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($medicines as $med): ?>
                                    <option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['name']) ?> (<?= htmlspecialchars($med['unit']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Qty</label>
                            <input class="form-control" type="number" min="1" name="items[0][qty]" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aturan Pakai</label>
                            <input class="form-control" name="items[0][dosage]" placeholder="3x1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Catatan</label>
                            <input class="form-control" name="items[0][note]" placeholder="Opsional">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary" id="add-item">Tambah Obat</button>
            </div>
            <div class="surface-footer text-end">
                <button class="btn btn-primary" type="submit">Simpan Rekomendasi</button>
            </div>
        </form>
    </div>
</section>
<script>
let itemIndex = 1;
const template = () => `
<div class="row g-3 mb-3 item-row">
    <div class="col-md-4">
        <label class="form-label">Obat</label>
        <select class="form-select" name="items[${itemIndex}][medicine_id]" required>
            <option value="">-- Pilih --</option>
            <?php foreach ($medicines as $med): ?>
                <option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['name']) ?> (<?= htmlspecialchars($med['unit']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Qty</label>
        <input class="form-control" type="number" min="1" name="items[${itemIndex}][qty]" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Aturan Pakai</label>
        <input class="form-control" name="items[${itemIndex}][dosage]" placeholder="3x1" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Catatan</label>
        <input class="form-control" name="items[${itemIndex}][note]" placeholder="Opsional">
    </div>
</div>`;

document.getElementById('add-item').addEventListener('click', () => {
    const wrapper = document.getElementById('items');
    wrapper.insertAdjacentHTML('beforeend', template());
    itemIndex++;
});
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
