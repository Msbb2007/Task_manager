<?php

namespace model\modelDao;

use model\db\connector;
use model\mainClasses\categories;

class categoryDao {
    private \PDO $db;

    public function __construct(Connector $connector) {
        $this->db = $connector->getConnection();
    }

    public function createCategory(string $name): bool {
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name]);
    }

    public function getAllCategories(): array {
        $sql = "SELECT * FROM categories";
        $stmt = $this->db->query($sql);
        
        $categories = [];
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $categories[] = new categories($result);
        }
        return $categories;
    }
    public function updateCategory(int $id, string $newName): bool {
        $sql = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newName, $id]);
    }
    public function deleteCategory(int $id): bool {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

}
