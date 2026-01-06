<?php

class Medicine extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM medicines ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function active(): array
    {
        $stmt = $this->db->query('SELECT * FROM medicines WHERE is_active = 1 ORDER BY name');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM medicines WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO medicines (name, unit, price, stock, is_active) VALUES (:name, :unit, :price, :stock, :is_active)');
        $stmt->execute([
            'name' => $data['name'],
            'unit' => $data['unit'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'is_active' => $data['is_active'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE medicines SET name = :name, unit = :unit, price = :price, stock = :stock, is_active = :is_active WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'unit' => $data['unit'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'is_active' => $data['is_active'],
        ]);
    }
}
