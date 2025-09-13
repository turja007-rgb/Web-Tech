<?php
namespace App\Customer\Models;

use Config\Database;
use PDO;

class Order {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        file_put_contents(__DIR__.'/debug.log', "Order model initialized\n", FILE_APPEND);
    }

    public function createOrder(
        int $customerId,
        array $cart,
        float $subtotal,
        float $deliveryFee,
        string $deliveryOption,
        ?string $deliveryAddress,
        string $paymentMethod,
        ?string $transactionId = null
    ): int {
        file_put_contents(__DIR__.'/debug.log', "createOrder called for customer $customerId\n", FILE_APPEND);

        $this->db->beginTransaction();

        // Insert order (DB calculates total_amount automatically)
        $stmt = $this->db->prepare("
            INSERT INTO orders 
            (customer_id, cart_id, subtotal_amount, delivery_fee, delivery_option, delivery_address, payment_method, payment_transaction_id)
            VALUES (?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $customerId,
            $cart['id'],
            $subtotal,
            $deliveryFee,
            $deliveryOption,
            $deliveryAddress,
            $paymentMethod,
            $transactionId
        ]);
        $orderId = (int)$this->db->lastInsertId();
        file_put_contents(__DIR__.'/debug.log', "Order inserted with ID $orderId\n", FILE_APPEND);

        // Insert order items and reduce stock
        foreach ($cart['items'] as $item) {
            // Insert order item
            $stmtItem = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                VALUES (?,?,?,?)
            ");
            $stmtItem->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);

            // Reduce stock
            $stmtStock = $this->db->prepare("
                UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?
            ");
            $stmtStock->execute([$item['quantity'], $item['product_id']]);
        }

        // Mark cart as checked out
        $stmtCart = $this->db->prepare("UPDATE carts SET status='checked_out' WHERE id=?");
        $stmtCart->execute([$cart['id']]);

        $this->db->commit();
        return $orderId;
    }
    //Fetching Customer All Order History
    public function getCustomerOrderHistory(int $customerId): array {
        $stmt = $this->db->prepare("
        SELECT o.*, oi.product_id, oi.quantity, oi.unit_price, p.name AS product_name
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC
    ");
        $stmt->execute([$customerId]);
        $orderRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group items under each order
        $orderHistory = [];
        foreach ($orderRows as $row) {
            $orderId = $row['id'];

            if (!isset($orderHistory[$orderId])) {
                $orderHistory[$orderId] = [
                    'id' => $orderId,
                    'subtotal_amount' => $row['subtotal_amount'],
                    'delivery_fee' => $row['delivery_fee'],
                    'total_amount' => $row['total_amount'],
                    'payment_method' => $row['payment_method'],
                    'payment_status' => $row['payment_status'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
                    'items' => []
                ];
            }

            $orderHistory[$orderId]['items'][] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price']
            ];

        }

        return array_values($orderHistory);
    }
    //Fetching Order Summary For Single Order
    public function getOrderById(int $orderId): ?array {
        $stmt = $this->db->prepare("
        SELECT * FROM orders WHERE id = ?
    ");
        $stmt->execute([$orderId]);
        $orderSummary = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$orderSummary) {
            return null;
        }

        // Get items for this order
        $stmtItems = $this->db->prepare("
        SELECT oi.product_id, p.name AS product_name, oi.quantity, oi.unit_price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
        $stmtItems->execute([$orderId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $orderSummary['items'] = $items;
        return $orderSummary;
    }

}
