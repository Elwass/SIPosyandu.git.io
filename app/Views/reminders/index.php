<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Reminder Imunisasi</h2>
    </div>
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Buat Reminder</h5>
            <form method="post" action="?page=reminders-store">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Warga</label>
                        <select name="resident_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($residents as $resident): ?>
                                <option value="<?= $resident['id'] ?>"><?= htmlspecialchars($resident['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jadwal Imunisasi</label>
                        <select name="immunization_id" class="form-select">
                            <option value="">Tidak terhubung</option>
                            <?php foreach ($immunizations as $item): ?>
                                <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['resident_name'] . ' - ' . $item['vaccine_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Kirim</label>
                        <input type="date" name="schedule_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Channel</label>
                        <select name="channel" class="form-select" required>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Warga</th>
                            <th>Vaksin</th>
                            <th>Tanggal Kirim</th>
                            <th>Channel</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reminders as $reminder): ?>
                            <tr>
                                <td><?= htmlspecialchars($reminder['resident_name']) ?> (<?= htmlspecialchars($reminder['phone']) ?>)</td>
                                <td><?= htmlspecialchars($reminder['vaccine_name'] ?? '-') ?></td>
                                <td><?= date('d M Y', strtotime($reminder['schedule_date'])) ?></td>
                                <td><?= strtoupper($reminder['channel']) ?></td>
                                <td>
                                    <span class="badge <?= $reminder['status'] === 'sent' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= strtoupper($reminder['status']) ?></span>
                                </td>
                                <td>
                                    <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true) && $reminder['status'] !== 'sent'): ?>
                                        <a href="?page=reminders-sent&id=<?= $reminder['id'] ?>" class="btn btn-sm btn-outline-success">Tandai Terkirim</a>
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
