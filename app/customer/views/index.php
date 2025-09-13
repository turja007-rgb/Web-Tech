<?php include __DIR__ . '/layout/header.php'; ?>

    <h2 class="mb-3">Our Products</h2>

    <!-- Feedback container for Bootstrap alerts -->
<!--    <div id="cart-feedback" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>-->

    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <?php if ($product['image_url']): ?>
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p><?= htmlspecialchars($product['description'] ?? '') ?></p>
                        <p>Stock: <?= htmlspecialchars($product['stock'] ?? 0) ?></p>
                        <p><strong>$<?= number_format($product['price'], 2) ?></strong></p>

                        <div class="mt-auto">
                            <?php if ($authId !== null): ?>
                                <?php if (($product['stock'] ?? 0) > 0): ?>
                                    <form class="add-to-cart-form" method="POST" action="<?= url('/cart_add')?>">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="number"
                                               name="quantity"
                                               min="1"

                                               value="1"
                                               class="form-control mb-2"
                                               style="max-width:100px;">
                                        <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled >Out of Stock</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?= url('/login')?>" class="btn btn-secondary  w-100">Login to add</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php include __DIR__ . '/layout/footer.php'; ?>