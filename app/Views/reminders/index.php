<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--page">
    <div class="container">
        <div class="section-heading">
            <span class="section-eyebrow">Reminder Imunisasi</span>
            <h2 class="section-title">Otomatisasi Pengingat Vaksin</h2>
            <p class="section-subtitle">Kirim notifikasi jadwal imunisasi melalui SMS, WhatsApp, atau email agar keluarga hadir tepat waktu.</p>
        </div>
        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success alert-modern"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="surface-card mb-5">
            <div class="surface-header">
                <h3>Buat Reminder Baru</h3>
            </div>
            <div class="surface-body">
                <form method="post" action="<?= url('?page=reminders-store') ?>" class="form-grid">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Warga</label>
                            <select name="resident_id" class="form-select form-select-modern" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($residents as $resident): ?>
                                    <option value="<?= $resident['id'] ?>"><?= htmlspecialchars($resident['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jadwal Imunisasi</label>
                            <select name="immunization_id" class="form-select form-select-modern">
                                <option value="">Tidak terhubung</option>
                                <?php foreach ($immunizations as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['resident_name'] . ' - ' . $item['vaccine_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Kirim</label>
                            <input type="date" name="schedule_date" class="form-control form-control-modern" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Channel</label>
                            <select name="channel" class="form-select form-select-modern" required>
                                <option value="sms">SMS</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions text-end mt-4">
                        <button class="btn btn-primary" type="submit">Simpan Reminder</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="surface-card">
            <div class="surface-header">
                <h3>Daftar Reminder</h3>
            </div>
            <div class="surface-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Warga</th>
                                <th>Vaksin</th>
                                <th>Tanggal Kirim</th>
                                <th>Channel</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reminders as $reminder): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reminder['resident_name']) ?> <span class="text-muted d-block small"><?= htmlspecialchars($reminder['phone']) ?></span></td>
                                    <td><?= htmlspecialchars($reminder['vaccine_name'] ?? '-') ?></td>
                                    <td><?= date('d M Y', strtotime($reminder['schedule_date'])) ?></td>
                                    <td><span class="badge bg-soft-primary text-primary"><?= strtoupper($reminder['channel']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $reminder['status'] === 'sent' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' ?>"><?= strtoupper($reminder['status']) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true) && $reminder['status'] !== 'sent'): ?>
                                            <a href="<?= url('?page=reminders-sent&id=' . $reminder['id']) ?>" class="btn btn-sm btn-outline-primary">Tandai Terkirim</a>
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
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
