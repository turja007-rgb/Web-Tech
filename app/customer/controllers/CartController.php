<?php
namespace App\Customer\Controllers;
use App\Customer\Models\Cart;
use Config\Response;
// use JetBrains\PhpStorm\NoReturn;

require_once dirname(__DIR__, 3) . '/config/helpers.php';
class CartController extends CustomerController {
    private Cart $cart;

    public function __construct() {
        parent::__construct();
        $this->cart = new Cart();
    }
//    public function add():void {
///*        if (!isset($_SESSION['customer_id'])) {
//            Response::redirect(url('/login'));
//
//        }*/
//        $customerId = $this->requireAuth(); //Check if user logged in or not
//
//        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//            $productId = $_POST['product_id'] ?? null;
//            $quantity  = $_POST['quantity'] ?? 1;
//
//            if ($productId) {
//                $this->cart->addItem($customerId, $productId, $quantity);
////                Response::redirect(url('/cart')); //After Add To Cart IF want redirect
//                $cartData = $this->cart->getActiveCart($customerId);
//                header('Content-Type: application/json');
//                echo json_encode(['success'=>true, 'cartCount' => count($cartData['items'] ?? [])]);
//                exit();
//
//            }
//        }
//
////        Response::redirect(url('/products'));
//        header('Content-Type: application/json');
//        echo json_encode(['success'=>false, 'message'=>'Invalid request']);
//    }
    public function add(): void {
        $customerId = $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $requestedQty = (int) ($_POST['quantity'] ?? 1);

            if ($productId) {
                // Get current stock
                $stock = $this->cart->getCurrentStock($productId);

                if ($stock <= 0) {
                    // Product out of stock
                    $this->cart->fixNegativeStock();
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Product is out of stock'
                    ]);
                    exit();
                }

                // Check if product already exists in customer's cart
                $existingQty = $this->cart->getCartItemQty($customerId, $productId);

                // Requested total = existing in cart + newly requested
                $totalRequested = $existingQty + $requestedQty;

                // Final quantity allowed = min(total requested, stock)
                $finalQty = min($totalRequested, $stock);

                // Determine how much new quantity we can actually add
                $quantityToAdd = $finalQty - $existingQty;

                if ($quantityToAdd <= 0) {
                    // Cart already has max stock
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'You already have the maximum available stock in your cart'
                    ]);
                    exit();
                }

                // Add (or update) cart with allowed quantity
                $this->cart->addItem($customerId, $productId, $quantityToAdd);

                // Respond with updated cart count
                $cartData = $this->cart->getActiveCart($customerId);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'cartCount' => count($cartData['items'] ?? []),
                    'addedQty' => $quantityToAdd,
                    'requestedQty' => $requestedQty,
                    'finalQty' => $finalQty // total quantity in cart for that product
                ]);
                exit();
            }

        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

    public function view() : void{
/*        if (!isset($_SESSION['customer_id'])) {
            Response::redirect(url('/login'));
        }*/
        $customerId = $this->requireAuth();
        $items = $this->cart->getItems($customerId);
        // Subtotal
        $subtotal = array_reduce($items, fn($carry, $item) =>
            $carry + ($item['price'] * $item['quantity']), 0);

        // Default delivery fee (0 at first, user chooses later)
        $deliveryFee = 0;
        $total = $subtotal + $deliveryFee;
        //Fetch Customer Data
        $customer = $this->getCustomer($customerId);
        Response::view(__DIR__ . '/../views/cart.php', [
            'items' => $items,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total' => $total,
            'customer'=> $customer
        ]);
    }

//    When User Selects Delivery
         public function deliveryFee(): void {
        $customerId = $this->requireAuth();

        // Get current items
        $items = $this->cart->getItems($customerId);

        // Calculate subtotal
        $subtotal = array_reduce($items, fn($carry, $item) =>
            $carry + ($item['price'] * $item['quantity']), 0);

        // Get delivery fee from Cart model
        $deliveryFee = $this->cart->getDeliveryFee();

        $total = $subtotal + $deliveryFee;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total' => $total
        ]);
        exit();
    }
    // Update quantity
    public function updateQuantity(): void {
        $customerId = $this->requireAuth();

        $productId = (int)($_POST['product_id'] ?? 0);
        $requestedQty = max(0, (int)($_POST['quantity'] ?? 1));

        if (!$productId) {
            echo json_encode(['success'=>false, 'message'=>'Invalid product']);
            exit;
        }

        $stock = $this->cart->getCurrentStock($productId);
        $finalQty = min($requestedQty, $stock);

        if ($finalQty === 0) {
            // Remove item if quantity is 0 or stock unavailable
            $cart = $this->cart->getActiveCart($customerId);
            if ($cart) $this->cart->removeItem($cart['id'], $productId);

            echo json_encode(['success'=>true, 'message'=>'Item removed due to 0 quantity or no stock']);
            exit;
        }

        // Update quantity
        $cart = $this->cart->getActiveCart($customerId);
        if ($cart) $this->cart->updateItemQuantity($cart['id'], $productId, $finalQty);

        echo json_encode([
            'success'=>true,
            'finalQty'=>$finalQty,
            'message'=>($finalQty < $requestedQty)
                ? "Quantity adjusted to $finalQty due to stock limit"
                : 'Quantity updated'
        ]);
    }

    // Remove item
    public function remove(): void {
        $customerId = $this->requireAuth();
        $productId = (int)($_POST['product_id'] ?? 0);

        if (!$productId) {
            echo json_encode(['success'=>false, 'message'=>'Invalid product']);
            exit;
        }

        $cart = $this->cart->getActiveCart($customerId);
        if ($cart) $this->cart->removeItem($cart['id'], $productId);

        echo json_encode(['success'=>true, 'message'=>'Item removed from cart']);
    }


}

