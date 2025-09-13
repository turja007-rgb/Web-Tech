<?php
namespace App\Customer\Controllers;

use App\Customer\Models\Product;
use Config\Response;

class ProductController {
    private Product $product;

    public function __construct() {
        $this->product = new Product();
    }

    public function index():void {
        $products = $this->product->getAllProducts();

        Response::view(__DIR__ . '/../views/products.php', [
            'products' => $products
        ]);
    }
}
