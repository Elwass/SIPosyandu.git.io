<?php
class BpjsProfile extends BaseModel
{
    public function getByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM bpjs_profiles WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $profile = $stmt->fetch();
        return $profile ?: null;
    }

    public function upsertForUser(int $userId, array $data): void
    {
        $payload = [
            'user_id' => $userId,
            'no_bpjs' => $data['no_bpjs'] ?? null,
            'status_bpjs' => $data['status_bpjs'] ?? 'tidak_diketahui',
            'jenis_bpjs' => $data['jenis_bpjs'] ?? null,
            'faskes_tingkat_1' => $data['faskes_tingkat_1'] ?? null,
            'tanggal_validasi' => $data['tanggal_validasi'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
            'bpjs_reference_id' => $data['bpjs_reference_id'] ?? null,
            'last_bpjs_check_at' => $data['last_bpjs_check_at'] ?? null,
            'source_system' => $data['source_system'] ?? 'manual',
        ];

        $sql = 'INSERT INTO bpjs_profiles (user_id, no_bpjs, status_bpjs, jenis_bpjs, faskes_tingkat_1, tanggal_validasi, keterangan, bpjs_reference_id, last_bpjs_check_at, source_system)
                VALUES (:user_id, :no_bpjs, :status_bpjs, :jenis_bpjs, :faskes_tingkat_1, :tanggal_validasi, :keterangan, :bpjs_reference_id, :last_bpjs_check_at, :source_system)
                ON DUPLICATE KEY UPDATE
                    no_bpjs = VALUES(no_bpjs),
                    status_bpjs = VALUES(status_bpjs),
                    jenis_bpjs = VALUES(jenis_bpjs),
                    faskes_tingkat_1 = VALUES(faskes_tingkat_1),
                    tanggal_validasi = VALUES(tanggal_validasi),
                    keterangan = VALUES(keterangan),
                    bpjs_reference_id = VALUES(bpjs_reference_id),
                    last_bpjs_check_at = VALUES(last_bpjs_check_at),
                    source_system = VALUES(source_system)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($payload);
    }
}
