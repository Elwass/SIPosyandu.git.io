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
                <form class="row g-3" method="post" action="<?= url('?page=admin-medicines-store') ?>" enctype="multipart/form-data">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <label class="form-label">Gambar</label>
                        <input class="form-control" type="file" name="image" accept="image/*">
                        <small class="text-muted">JPG/PNG/WebP maksimal 2MB.</small>
                    </div>
                    <div class="col-md-12 d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Tambah Obat</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($editMedicine): ?>
        <div class="surface-card mb-3">
            <div class="surface-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Obat</h5>
                <a class="btn btn-sm btn-outline-secondary" href="<?= url('?page=admin-medicines') ?>">Batal</a>
            </div>
            <div class="surface-body">
                <form class="row g-3" method="post" action="<?= url('?page=admin-medicines-update') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= (int) $editMedicine['id'] ?>">
                    <div class="col-md-3">
                        <label class="form-label">Nama Obat</label>
                        <input class="form-control" name="name" value="<?= htmlspecialchars($editMedicine['name']) ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Satuan</label>
                        <input class="form-control" name="unit" value="<?= htmlspecialchars($editMedicine['unit']) ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Harga</label>
                        <input class="form-control" name="price" type="number" min="0" step="1" value="<?= (int) $editMedicine['price'] ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stok</label>
                        <input class="form-control" name="stock" type="number" min="0" step="1" value="<?= (int) $editMedicine['stock'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gambar</label>
                        <input class="form-control" type="file" name="image" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak diganti.</small>
                        <div class="mt-2">
                            <?php if (!empty($editMedicine['image'])): ?>
                                <img src="<?= url($editMedicine['image']) ?>" alt="Preview" class="img-thumbnail" style="max-width: 140px; max-height: 140px; object-fit: cover;">
                            <?php else: ?>
                                <span class="text-muted">Belum ada gambar</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-12 d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_edit" <?= $editMedicine['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active_edit">Aktif</label>
                        </div>
                        <button class="btn btn-success" type="submit">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $row): ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?= url($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="rounded" style="width:60px; height:60px; object-fit:cover;">
                            <?php else: ?>
                                <span class="text-muted small">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['unit']) ?></td>
                        <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
                        <td><?= (int) $row['stock'] ?></td>
                        <td><span class="badge bg-soft-primary text-primary"><?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= url('?page=admin-medicines&edit_id=' . (int) $row['id']) ?>">Edit</a>
                            <form class="d-inline" method="post" action="<?= url('?page=admin-medicines-delete') ?>" onsubmit="return confirm('Hapus obat ini?');">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
