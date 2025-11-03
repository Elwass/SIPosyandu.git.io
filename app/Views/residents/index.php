<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--page">
    <div class="container">
        <div class="section-heading d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
            <div>
                <span class="section-eyebrow">Manajemen Data</span>
                <h2 class="section-title mb-0">Data Warga Posyandu</h2>
                <p class="section-subtitle">Kelola pendaftaran ibu hamil, balita, dan lansia untuk memastikan pelayanan tepat sasaran.</p>
            </div>
            <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true)): ?>
                <a href="<?= url('?page=residents-create') ?>" class="btn btn-primary mt-3 mt-lg-0">Tambah Warga</a>
            <?php endif; ?>
        </div>
        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success alert-modern"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="surface-card">
            <div class="surface-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Kategori</th>
                                <th>No. KK</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($residents as $resident): ?>
                                <tr>
                                    <td><?= htmlspecialchars($resident['name']) ?></td>
                                    <td><?= htmlspecialchars($resident['nik']) ?></td>
                                    <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars(ucfirst($resident['category'])) ?></span></td>
                                    <td><?= htmlspecialchars($resident['family_number']) ?></td>
                                    <td><?= htmlspecialchars($resident['phone']) ?></td>
                                    <td><?= htmlspecialchars($resident['address']) ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true)): ?>
                                                <a class="btn btn-outline-primary" href="<?= url('?page=residents-edit&id=' . $resident['id']) ?>">Edit</a>
                                            <?php endif; ?>
                                            <?php if (in_array(user()['role'], ['super_admin', 'admin'], true)): ?>
                                                <a class="btn btn-outline-danger" href="<?= url('?page=residents-delete&id=' . $resident['id']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
