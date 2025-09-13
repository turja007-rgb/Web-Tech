<?php
//require_once __DIR__ . '/../../../config/Database.php';
namespace App\Customer\Models;
use Config\Database;
use PDO;
class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllProducts() {
        $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

