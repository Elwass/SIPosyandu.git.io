<?php
class Resident extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM residents ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM residents WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $resident = $stmt->fetch();
        return $resident ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO residents (name, nik, family_number, address, phone, birth_date, gender, category) VALUES (:name, :nik, :family_number, :address, :phone, :birth_date, :gender, :category)');
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE residents SET name=:name, nik=:nik, family_number=:family_number, address=:address, phone=:phone, birth_date=:birth_date, gender=:gender, category=:category WHERE id=:id');
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM residents WHERE id=:id');
        $stmt->execute(['id' => $id]);
    }
}
