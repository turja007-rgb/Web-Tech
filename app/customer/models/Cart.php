<?php
//require_once __DIR__ . '/../../../config/Database.php';

namespace App\Customer\Models;
use Config\Database;
use PDO;
class Cart {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function addItem($customer_id, $product_id, $quantity) : void {
        // ensure cart exists
        $cart = $this->getActiveCart($customer_id);
        if (!$cart) {
            $stmt = $this->db->prepare("INSERT INTO carts (customer_id) VALUES (?)");
            $stmt->execute([$customer_id]);
            $cart_id = $this->db->lastInsertId();
        } else {
            $cart_id = $cart['id'];
        }

        // insert or update item
        $stmt = $this->db->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
        ");
        $stmt->execute([$cart_id, $product_id, $quantity]);
    }

    public function getActiveCart($customer_id) {
        $stmt = $this->db->prepare("SELECT * FROM carts WHERE customer_id=? AND status='active' LIMIT 1");
        $stmt->execute([$customer_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) {
            $stmt2 = $this->db->prepare("
            SELECT ci.*, p.name, p.price, p.stock
            FROM cart_items ci
            JOIN products p ON ci.product_id=p.id
            WHERE ci.cart_id=?
        ");
            $stmt2->execute([$cart['id']]);
            $cart['items'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        return $cart;
    }

    public function getItems($customer_id): array {
        $cart = $this->getActiveCart($customer_id);
        return $cart['items'] ?? [];
    }
    //Return Specific Product ->Quantity for ->CustomerId
    public function getCartItemQty(int $customerId, int $productId): int {
        // First get active cart id
        $stmt = $this->db->prepare("SELECT id FROM carts WHERE customer_id=? AND status='active' LIMIT 1");
        $stmt->execute([$customerId]);
        $cartId = $stmt->fetchColumn();

        if (!$cartId) {
            return 0; // no cart, so no items
        }

        // Now check product quantity in that cart
        $stmt = $this->db->prepare("SELECT quantity FROM cart_items WHERE cart_id=? AND product_id=? LIMIT 1");
        $stmt->execute([$cartId, $productId]);
        $qty = $stmt->fetchColumn();

        return $qty !== false ? (int)$qty : 0;
    }

    public function getDeliveryFee(): float {
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE key_name = 'delivery_fee' LIMIT 1");
        $stmt->execute();
        $fee = $stmt->fetchColumn();
        return $fee !== false ? (float)$fee : 0.0;
    }
    public function updateDelivery(int $cartId, string $option, ?string $address): bool {
        $stmt = $this->db->prepare("UPDATE carts SET delivery_option=?, delivery_address=? WHERE id=?");
        return $stmt->execute([$option, $address, $cartId]);
    }
    public function updateItemQuantity(int $cartId, int $productId, int $quantity): bool {
        $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
        return $stmt->execute([$quantity, $cartId, $productId]);
    }
    //Checkout page
    public function removeItem(int $cartId, int $productId): bool{
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
        return $stmt->execute([$cartId, $productId]);
    }
    public function reduceProductStock(int $productId, int $quantity): bool {
        $stmt = $this->db->prepare(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
        );
        return $stmt->execute([$quantity, $productId, $quantity]);
    }
    public function fixNegativeStock(): bool {
        $stmt = $this->db->prepare("UPDATE products SET stock = 0 WHERE stock < 0");
        return $stmt->execute();
    }
//Get current stock
    public function getCurrentStock(int $productId): int{
        $stmt = $this->db->prepare("SELECT stock FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$productId]);
        return (int) $stmt->fetchColumn();
    }
}
