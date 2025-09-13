<?php
$logged_in = isset($_SESSION['customer_id']);
require ROOT . '/config/helpers.php';
use App\Customer\Models\Cart;
$cart = new Cart();
$cartCount = 0;
if ($logged_in){
    $cartData = $cart->getActiveCart($_SESSION['customer_id']);
    $cartCount = count($cartData['items'] ?? []);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bakery App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="<?= url('/public/css/custom.css')?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?= url('/') ?>">Bakery</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php if ($logged_in): ?>
                    <li class="nav-item me-lg-3">
                        <a class="nav-link" href="<?= url('/profile') ?>">
                            <i class="bi bi-person-circle me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item me-lg-3 position-relative">
                        <a class="nav-link" href="<?= url('/cart') ?>">
                            <i class="bi bi-cart-fill me-1"></i> Cart
                            <span class="badge bg-danger rounded-pill cart-badge" id="cart-count"><?= $cartCount ?? 0 ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="<?= url('/logout') ?>">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-lg-2">
                        <a class="btn btn-outline-primary btn-sm rounded-pill px-3" href="<?= url('/login') ?>">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm rounded-pill px-3 ms-lg-2" href="<?= url('/register') ?>">
                            <i class="bi bi-person-plus-fill me-1"></i> Signup
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="flex-fill">
<div class="container mt-4">
