<?php
class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insert($name, $price, $category_id, $image_path) {
        $available = 'available';
        return $this->db->insert("Products", 
            ["name", "price", "c_id", "image_path", "available"], 
            [$name, $price, $category_id, $image_path, $available]);
    }

    public function getAllCategories() {
        return $this->db->selectAll("Category");
    }

    public function getAll() {
        return $this->db->select(
            "Products",
            ["name", "price", "image_path"]
        );
    }

    public function getById($id) {
        $sql = "SELECT name, price, image_path FROM Products WHERE P_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $name, $price, $category_id, $image_path, $available) {
        $sql = "UPDATE Products SET name = ?, price = ?, c_id = ?, image_path = ?, available = ? WHERE P_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$name, $price, $category_id, $image_path, $available, $id]);
    }

    public function delete($id) {
        return $this->db->delete("Products", "P_id = ?", [$id]);
    }
}
?>
