<?php
namespace App\Customer\Controllers;
use App\Customer\Models\Cart;
use Config\Response;

class CheckoutController extends CustomerController {
    private Cart $cart;
    public function __construct() {
        parent::__construct();
        $this->cart = new Cart();
    }
    public function checkout(): void {
        $customerId = $this->requireAuth();

        // Get cart items
        $items = $this->cart->getItems($customerId);

        // Calculate subtotal
        $subtotal = array_reduce($items, fn($carry, $item) =>
            $carry + ($item['price'] * $item['quantity']), 0);

        // Default delivery fee (0 for now)
        $deliveryFee = 0;
        $total = $subtotal + $deliveryFee;
        // Fetch customer info using the property
        $customer = $this->getCustomer($customerId);

        Response::view(__DIR__ . '/../views/checkout.php', [
            'items' => $items,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total' => $total,
            'customer' => $customer
        ]);
    }

}
