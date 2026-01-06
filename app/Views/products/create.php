<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Produk</span>
                <h2 class="section-title">Tambah Produk</h2>
            </div>
            <a class="btn btn-outline-primary" href="<?= url('?page=admin-products') ?>">Kembali</a>
        </div>
        <div class="surface-card">
            <div class="surface-body">
                <form method="post" action="<?= url('?page=admin-products-store') ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga</label>
                            <input type="number" name="price" class="form-control" min="0" step="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gambar (URL)</label>
                            <input type="text" name="image_url" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
