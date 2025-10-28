<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Warga</h2>
        <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true)): ?>
            <a href="?page=residents-create" class="btn btn-success">Tambah Warga</a>
        <?php endif; ?>
    </div>
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Kategori</th>
                            <th>No. KK</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($residents as $resident): ?>
                            <tr>
                                <td><?= htmlspecialchars($resident['name']) ?></td>
                                <td><?= htmlspecialchars($resident['nik']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($resident['category'])) ?></td>
                                <td><?= htmlspecialchars($resident['family_number']) ?></td>
                                <td><?= htmlspecialchars($resident['phone']) ?></td>
                                <td><?= htmlspecialchars($resident['address']) ?></td>
                                <td>
                                    <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true)): ?>
                                        <a class="btn btn-sm btn-outline-success" href="?page=residents-edit&id=<?= $resident['id'] ?>">Edit</a>
                                    <?php endif; ?>
                                    <?php if (in_array(user()['role'], ['super_admin', 'admin'], true)): ?>
                                        <a class="btn btn-sm btn-outline-danger" href="?page=residents-delete&id=<?= $resident['id'] ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
