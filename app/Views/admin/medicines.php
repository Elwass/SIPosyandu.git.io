<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Apotek</span>
                <h2 class="section-title">Daftar Obat</h2>
            </div>
        </div>
        <div class="surface-card mb-3">
            <div class="surface-body">
                <form class="row g-3" method="post" action="<?= url('?page=admin-medicines-store') ?>">
                    <div class="col-md-4">
                        <label class="form-label">Nama Obat</label>
                        <input class="form-control" name="name" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Satuan</label>
                        <input class="form-control" name="unit" placeholder="tablet/botol" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Harga</label>
                        <input class="form-control" name="price" type="number" min="0" step="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stok</label>
                        <input class="form-control" name="stock" type="number" min="0" step="1" value="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Tambah Obat</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['unit']) ?></td>
                        <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
                        <td><?= (int) $row['stock'] ?></td>
                        <td><span class="badge bg-soft-primary text-primary"><?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
