<?php include __DIR__ . '/layout/header.php'; ?>

<?php if (!empty($items)): ?>
    <div class="d-flex justify-content-center my-4">
        <div class="card shadow-sm rounded-4 w-100" style="max-width: 900px;">
            <div class="card-body">
                <h4 class="mb-4 fw-bold text-dark">Your Cart</h4>

                <!-- Cart Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center shadow-sm mb-4">
                        <thead class="table-success">
                        <tr>
                            <th class="text-start">Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <?php
                        $subtotal = 0;
                        foreach ($items as $item):
                            $line = $item['price'] * $item['quantity'];
                            $subtotal += $line;
                            ?>
                            <tr>
                                <!-- Product Name -->
                                <td class="text-start fw-semibold">
                                    <?= htmlspecialchars($item['name']) ?>
                                </td>

                                <!-- Quantity -->
                                <td>
                                    <div class="input-group input-group-sm mx-auto" style="width: 120px;">
                                        <button class="btn btn-outline-secondary decrement-btn" type="button">-</button>
                                        <input type="number"
                                               class="form-control text-center quantity-input"
                                               data-product-id="<?= $item['product_id'] ?>"
                                               value="<?= $item['quantity'] ?>"
                                               min="1" max="<?= $item['stock'] ?>">
                                        <button class="btn btn-outline-secondary increment-btn" type="button">+</button>
                                    </div>
                                </td>

                                <!-- Price -->
                                <td class="fw-semibold text-muted">
                                    <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i>
                                    <span class="unit-price" data-value="<?= $item['price'] ?>"><?= number_format($item['price'], 2) ?></span>
                                </td>

                                <!-- Total -->
                                <td class="fw-bold text-success">
                                    <i class="fa-solid fa-bangladeshi-taka-sign me-1"></i>
                                    <span class="line-total" data-value="<?= $line ?>"><?= number_format($line, 2) ?></span>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-outline-primary btn-sm update-item-btn"
                                                data-product-id="<?= $item['product_id'] ?>"
                                                data-product-url="<?= url('/cart/update-quantity') ?>">
                                            <i class="bi bi-arrow-repeat me-1"></i> Update
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm remove-item-btn"
                                                data-product-id="<?= $item['product_id'] ?>"
                                                data-product-url="<?= url('/cart/remove-item') ?>">
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Subtotal & Checkout -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <!-- Subtotal -->
                    <h5 class="fw-bold mb-0 text-dark">
                        Subtotal:
                        <span class="text-success">
        <i class="fa-solid fa-bangladeshi-taka-sign me-1" style="font-size: 1.2rem;"></i>
        <span id="subtotal"><?= number_format($subtotal, 2) ?></span>
    </span>
                    </h5>

                    <a href="<?= url('/checkout') ?>" class="btn checkout-btn">
                        <i class="bi bi-bag-check-fill me-2"></i> Checkout
                    </a>
                </div>

            </div>
        </div>
    </div>

    <style>
        .checkout-btn {
            background: linear-gradient(135deg, #28a745, #218838);
            color: #fff;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }
        .checkout-btn:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(40, 167, 69, 0.4);
        }
    </style>

<?php else: ?>
    <!-- Empty Cart State -->
    <div class="d-flex justify-content-center align-items-center my-5">
        <div class="card shadow-sm border-0 rounded-4 text-center p-4" style="max-width: 500px;">
            <div class="card-body">
                <i class="bi bi-cart-x text-danger fs-1 mb-3"></i>
                <h5 class="card-title text-danger  mb-3"><?= isset($customer['name'])? $customer['name'] : ''?></h5>
                <h5 class="card-title text-muted mb-3">🛒 Your cart is empty</h5>
                <p class="text-secondary">Looks like you haven’t added anything yet.</p>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="<?= url('/') ?>" class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="<?= url('/public/js/cartActions.js') ?>"></script>
<?php include __DIR__ . '/layout/footer.php'; ?>
