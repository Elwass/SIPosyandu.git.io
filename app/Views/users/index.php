<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--page">
    <div class="container">
        <div class="section-heading">
            <span class="section-eyebrow">Hak Akses</span>
            <h2 class="section-title">Manajemen Pengguna</h2>
            <p class="section-subtitle">Tambahkan super admin, admin Puskesmas, bidan, dan kader untuk kolaborasi lintas peran.</p>
        </div>
        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success alert-modern"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="surface-card h-100">
                    <div class="surface-header">
                        <h3>Tambah Pengguna</h3>
                    </div>
                    <div class="surface-body">
                        <form method="post" action="<?= url('?page=users-store') ?>" class="form-grid">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control form-control-modern" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-modern" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-modern" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select form-select-modern" required>
                                    <option value="admin">Admin Puskesmas</option>
                                    <option value="midwife">Bidan</option>
                                    <option value="kader">Kader Posyandu</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                            <div class="form-actions text-end">
                                <button class="btn btn-primary" type="submit">Simpan Pengguna</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="surface-card h-100">
                    <div class="surface-header">
                        <h3>Daftar Pengguna</h3>
                    </div>
                    <div class="surface-body">
                        <div class="table-responsive">
                            <table class="table table-modern align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['name']) ?></td>
                                            <td><?= htmlspecialchars($item['email']) ?></td>
                                            <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars(str_replace('_', ' ', $item['role'])) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
