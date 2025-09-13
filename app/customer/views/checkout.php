<?php include __DIR__ . '/layout/header.php'; ?>

<?php
use Config\Response;

$errors = Response::getFlash('errors') ?? [];
$orderplace = Response::getFlash('orderplace') ?? '';

?>

<div class="row g-4 justify-content-center">
<!--    <h2 class="mb-4 text-center fw-bold">Checkout</h2>-->

    <?php if (!empty($items)): ?>
        <div class="card shadow-lg d-flex border-0 rounded-3" style="max-width: 670px;">
            <div class="card-body p-4">
                <h4 class="card-title mb-3 text-primary">🛒 Order Summary</h4>

                <!-- Order Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle text-center">
                        <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $subtotal = 0; $deliveryFee = 0; $total = 0; ?>
                        <?php foreach ($items as $item):
                            $line = $item['price'] * $item['quantity'];
                            $subtotal += $line;
                            $total = $subtotal;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>&#2547;<?= number_format($item['price'], 2) ?></td>
                                <td>&#2547;<?= number_format($line, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="mt-3">
                    <h5 class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>&#2547;<span id="subtotal"><?= number_format($subtotal,2) ?></span></span>
                    </h5>
                    <h5 class="d-flex justify-content-between">
                        <span>Delivery Fee:</span>
                        <span>&#2547;<span id="delivery_fee"><?= number_format($deliveryFee,2) ?></span></span>
                    </h5>
                    <h4 class="d-flex justify-content-between fw-bold text-success">
                        <span>Total:</span>
                        <span>&#2547;<span id="total"><?= number_format($total,2) ?></span></span>
                    </h4>
                </div>

                <hr class="my-4">

                <!-- Checkout Form -->
                <form method="POST" action="<?= url('/place-order') ?>" id="checkoutForm">

                    <!-- Delivery Option -->
                    <div class="mb-3">
                        <label for="delivery_option" class="form-label fw-semibold">🚚 Delivery Option</label>
                        <select name="delivery_option" data-delivery-url="<?= url('/cart/delivery-fee') ?>"
                                id="delivery_option" class="form-select <?= isset($errors['delivery_address']) ? 'is-invalid' : '' ?>" required>
                            <option value="pickup" <?= ($_POST['delivery_option'] ?? '') === 'pickup' ? 'selected' : '' ?>>Pickup</option>
                            <option value="delivery" <?= ($_POST['delivery_option'] ?? '') === 'delivery' ? 'selected' : '' ?>>Delivery</option>
                        </select>
                        <?php if(isset($errors['delivery_address'])): ?>
                            <div class="invalid-feedback"><?= displayFlash($errors, 'delivery_address', 'danger'); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Delivery Address -->
                    <div class="mb-3" id="delivery_address_wrap" style="display:<?= ($_POST['delivery_option'] ?? '') === 'delivery' ? 'block' : 'none' ?>;">
                        <label for="delivery_address" class="form-label fw-semibold">🏠 Delivery Address</label>
                        <textarea name="delivery_address" id="delivery_address" rows="2" class="form-control <?= isset($errors['delivery_address']) ? 'is-invalid' : '' ?>" placeholder="Enter your delivery address"><?= htmlspecialchars($_POST['delivery_address'] ?? '') ?></textarea>
                        <?php if(isset($errors['delivery_address'])): ?>
                            <div class="invalid-feedback"><?php  ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-semibold">💳 Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="cod" <?= ($_POST['payment_method'] ?? '') === 'cod' ? 'selected' : '' ?>>Cash on Delivery</option>
                            <option value="bkash" <?= ($_POST['payment_method'] ?? '') === 'bkash' ? 'selected' : '' ?>>bKash</option>
                            <option value="nagad" <?= ($_POST['payment_method'] ?? '') === 'nagad' ? 'selected' : '' ?>>Nagad</option>
                        </select>
                    </div>

                    <!-- Transaction ID -->
                    <div class="mb-3" id="transaction_wrap" style="display:<?= in_array($_POST['payment_method'] ?? '', ['bkash','nagad']) ? 'block' : 'none' ?>;">
                        <label for="transaction_id" class="form-label fw-semibold">🔑 Transaction ID</label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control <?= isset($errors['transaction_id']) ? 'is-invalid' : '' ?>" placeholder="e.g. CHU5WQLF83" value="<?= htmlspecialchars($_POST['transaction_id'] ?? '') ?>">
<!--                        <div class="form-text text-muted">Use only capital letters (A–Z) and numbers (0–9)</div>-->
                        <?php if(isset($errors['transaction_id'])): ?>
                            <div class="invalid-feedback"><?= displayFlash($errors,'transaction_id', 'warning'); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- General Errors -->
                    <?php if(isset($errors['general'])): displayFlash($errors,'general', 'warning'); ?>

<!--                        <div class="alert alert-danger">--><?php //= htmlspecialchars($errors['general']) ?><!--</div>-->
                    <?php endif; ?>

                    <!-- Place Order Button -->
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                        ✅ Place Order
                    </button>
                </form>
            </div>
        </div>

        <script src="<?= url('/js/checkout.js') ?>"></script>
        <script>
            // Toggle delivery address
            document.getElementById('delivery_option').addEventListener('change', function() {
                document.getElementById('delivery_address_wrap').style.display =
                    this.value === 'delivery' ? 'block' : 'none';
            });

            // Toggle transaction ID field
            document.getElementById('payment_method').addEventListener('change', function() {
                const wrap = document.getElementById('transaction_wrap');
                wrap.style.display = (this.value === 'bkash' || this.value === 'nagad') ? 'block' : 'none';
            });
        </script>
    <?php elseif (!empty($orderplace)): ?>

        <div class="d-flex justify-content-center align-items-center my-5">
            <div class="card shadow-sm border-0 rounded-4 text-center p-4" style="max-width: 500px;">
                <div class="card-body">
                    <i class="bi bi-check-circle text-success fs-1 mb-3"></i>
                    <h5 class="card-title text-success mb-3"><?= isset($customer["name"])?htmlspecialchars(($customer["name"])):'' ?></h5>
                    <h5 class="card-title text-success mb-3"> <?= htmlspecialchars($orderplace['success']) ?></h5>
<!--                    <p class="text-secondary text-success">--><?php //= htmlspecialchars($orderplace['success']) ?><!--</p>-->

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="<?= url('/')?>"
                           class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
                            Continue Shopping
                        </a>
                        <a href="<?= url('/profile')?>"
                           class="btn btn-outline-primary px-4 py-2 rounded-pill shadow-sm">
                            Check Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <script>window.addEventListener("DOMContentLoaded", function() {showToast("<?= htmlspecialchars($orderplace['success']) ?>", "success");</script>
    <?php else: ?>
        <div class="d-flex justify-content-center align-items-center my-5">
            <div class="card shadow-sm border-0 rounded-4 text-center p-4" style="max-width: 500px;">
                <div class="card-body">
                    <i class="bi bi-cart-x text-danger fs-1 mb-3"></i>
                    <h5 class="card-title text-success mb-3"><?= isset($customer["name"])?htmlspecialchars(($customer["name"])):'' ?></h5>
                    <h5 class="card-title text-muted mb-3">🛒 Your cart is empty</h5>
                    <p class="text-secondary">Looks like you haven’t added anything yet.</p>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="<?= url('/')?>"
                           class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
                            Continue Shopping
                        </a>
                        <a href="<?= url('/profile')?>"
                           class="btn btn-outline-primary px-4 py-2 rounded-pill shadow-sm">
                            Check Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
