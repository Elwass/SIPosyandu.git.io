<?php
class Product extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM products ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function active(): array
    {
        $stmt = $this->db->query('SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (name, sku, price, stock, description, image_url, is_active) VALUES (:name, :sku, :price, :stock, :description, :image_url, :is_active)'
        );
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE products SET name=:name, sku=:sku, price=:price, stock=:stock, description=:description, image_url=:image_url, is_active=:is_active WHERE id=:id'
        );
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id=:id');
        $stmt->execute(['id' => $id]);
    }
}
