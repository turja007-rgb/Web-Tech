<?php
namespace App\Customer\Controllers;

use App\Customer\Models\Cart;
use App\Customer\Models\Order;
use Config\Response;

class OrderController extends CustomerController
{
    private Cart $cart;
    private Order $order;
    public function __construct()
    {
        parent::__construct();
        $this->cart = new Cart();
        $this->order = new Order();
        file_put_contents(__DIR__ . '/debug.log', "OrderController initialized\n", FILE_APPEND);
    }

    public function place(): void
    {
        $customerId = $this->requireAuth();

        $deliveryOption = $_POST['delivery_option'] ?? 'pickup';
        $deliveryAddress = $_POST['delivery_address'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $transactionId = $_POST['transaction_id'] ?? null;

        // Validate payment
        if (in_array($paymentMethod, ['bkash','nagad'])) {
            if (empty($transactionId)) {
                Response::setFlash('errors', [
                    'transaction_id' => "Transaction ID required for ".strtoupper($paymentMethod)
                ]);
                Response::redirect(url('/checkout'));
            }

            // Rules per provider
            if ($paymentMethod === 'bkash' && !preg_match('/^[A-Z0-9]{10}$/', $transactionId)) {
                Response::setFlash('errors', [
                    'transaction_id' => "bKash Transaction ID must be exactly 10 characters (A–Z, 0–9)"
                ]);
                Response::redirect(url('/checkout'));
            }

            if ($paymentMethod === 'nagad' && !preg_match('/^[A-Z0-9]{8}$/', $transactionId)) {
                Response::setFlash('errors', [
                    'transaction_id' => "Nagad Transaction ID must be exactly 8 characters (A–Z, 0–9)"
                ]);
                Response::redirect(url('/checkout'));
            }
        }


        // Validate delivery
        if ($deliveryOption === 'delivery' && empty(trim($deliveryAddress))) {
            Response::setFlash('errors', ['delivery_address'=>'Delivery address is required']);
            Response::redirect(url('/checkout'));
        }

        $cart = $this->cart->getActiveCart($customerId);
        if (!$cart || empty($cart['items'])) {
            Response::setFlash('errors', ['general'=>'Cart is empty']);
            Response::redirect(url('/cart'));
        }

        // Auto-adjust quantities if stock is insufficient and reduce stock
        $adjusted = false;
        foreach ($cart['items'] as $item) {
            $productId = $item['product_id'];
            $productName = $item['name'];
            $cartQty = (int)$item['quantity'];
            $stock = (int)$item['stock'];

            if ($stock <= 0) {
                $adjusted = true;
//                $this->cart->updateItemQuantity($cart['id'], $productId, 0);
                $this->cart->removeItem($cart['id'],$productId);

            } elseif ($cartQty > $stock && $stock != 0) {
                $adjusted = true;
                $this->cart->updateItemQuantity($cart['id'], $productId, $stock);
            }
        }

        if ($adjusted) {
            Response::setFlash('errors',['general'=>'Some Item and quantities adjusted due to insufficient stock']);
            Response::redirect(url('/checkout'));
        }

        // Update delivery info in cart
        $this->cart->updateDelivery($cart['id'], $deliveryOption, $deliveryAddress);

        // Refresh cart items
        $cart = $this->cart->getActiveCart($customerId);

        // Calculate totals
        $subtotal = array_reduce($cart['items'], fn($c,$i)=>$c+($i['price']*$i['quantity']),0);
        $deliveryFee = ($deliveryOption==='delivery') ? $this->cart->getDeliveryFee() : 0;

        // Create order
        $orderId = $this->order->createOrder(
            $customerId,
            $cart,
            $subtotal,
            $deliveryFee,
            $deliveryOption,
            $deliveryAddress,
            $paymentMethod,
            $transactionId
        );
        if (!$orderId) {
            file_put_contents(__DIR__ . '/debug.log', "Order creation failed\n", FILE_APPEND);
            die('Order creation failed');
        }

        Response::setFlash('orderplace', ['success'=>'Your order placed successfully']);
        Response::redirect(url('/checkout'));

    }
    //Render Order Summary
    public function orderSummary(): void {
        $this->requireAuth();
        $orderId = (int)($_POST['id'] ?? 0);

        if (!$orderId) {
            Response::setFlash('error','Invalid order ID.');
//            echo "<p>Invalid order ID.</p>";
            exit;
        }

        $orderSummary = $this->order->getOrderById($orderId);

        if (!$orderSummary) {
            Response::setFlash('orderInvalid','Invalid order ID.');
            echo "<p>Order not found.</p>";
            exit;
        }
        $path = dirname(__DIR__,3).'/app/customer/views/partials/order_summary.php';
        // Render partial view
        Response::view($path, compact('orderSummary'));
    }
}
