<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Rekomendasi Obat</span>
                <h2 class="section-title">Daftar Rekomendasi</h2>
            </div>
            <a class="btn btn-primary" href="<?= url('?page=admin-recommendations-create') ?>">Buat Rekomendasi</a>
        </div>
        <div class="surface-card">
            <div class="surface-body">
                <?php if ($list): ?>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pasien</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $row): ?>
                                    <tr>
                                        <td>REC-<?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                        <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($row['status']) ?></span></td>
                                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('?page=recommendation-detail&id=' . $row['id']) ?>">Detail</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Belum ada rekomendasi.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
