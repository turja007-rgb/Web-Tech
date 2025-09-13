<?php
// app/customer/models/Customer.php
namespace App\Customer\Models;
use Config\Database;
use PDO;

class Customer {
    private PDO $db;
    public function __construct(){ $this->db = Database::getConnection(); }

    public function findByEmail(string $email): ?array {
        $st = $this->db->prepare("SELECT * FROM customers WHERE email = ?");
        $st->execute([$email]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(array $data): int {
        $st = $this->db->prepare(
            "INSERT INTO customers(name,email,password_hash,phone) VALUES (?,?,?,?)"
        );
        $st->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['phone'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }
    //Profile Update
    public function update(int $id, array $data): void {
        $fields = [];
        $params = [];

        if (isset($data['name'])) { $fields[] = 'name = ?'; $params[] = $data['name']; }
        if (isset($data['email'])) { $fields[] = 'email = ?'; $params[] = $data['email']; }
        if (isset($data['phone'])) { $fields[] = 'phone = ?'; $params[] = $data['phone']; }
        if (!empty($data['password'])) {
            $fields[] = 'password_hash = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) return;

        $params[] = $id;

        $st = $this->db->prepare("UPDATE customers SET ".implode(', ',$fields)." WHERE id = ?");
        $st->execute($params);
    }

}
