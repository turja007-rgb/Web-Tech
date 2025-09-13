<?php include __DIR__ . '/layout/header.php'; ?>
<!--IGNORE THIS IT'S A TEMPLATE OF ODER HISTORY -> VIEW DETAILS BUT NOT USED ANYWHERE-->
<!--IN USED ALSO CONTAIN SAME TEMPLATEON:
DIRECTORY OF app/views/partials/order_summary.php-->
<!-- Order History Card (larger) -->
<div class="col-md-5">
    <div class="card shadow-sm h-100">
        <div class="card-body">
            <h4 class="mb-3">Order History</h4>
            <?php if (!empty($orderHistory)): ?>
                <div class="table-responsive">
                    <?php foreach ($orderHistory as $singleOrder): ?>
                        <h5 class="mt-3 mb-2">Order #<?= $singleOrder['id'] ?> - <?= $singleOrder['created_at'] ?></h5>
                        <table class="table table-bordered align-middle mb-4">
                            <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($singleOrder['items'] as $orderItem): ?>
                                <tr>
                                    <td><?= htmlspecialchars($orderItem['product_name']) ?></td>
                                    <td><?= $orderItem['quantity'] ?></td>
                                    <td>$<?= number_format($orderItem['unit_price'], 2) ?></td>
                                    <td>$<?= number_format($orderItem['unit_price'] * $orderItem['quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Subtotal</td>
                                <td>$<?= number_format($singleOrder['subtotal_amount'], 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Delivery Fee</td>
                                <td>$<?= number_format($singleOrder['delivery_fee'], 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td>$<?= number_format($singleOrder['total_amount'], 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Payment Method</td>
                                <td><?= htmlspecialchars($singleOrder['payment_method']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Payment Status</td>
                                <td>
                                    <span class="badge bg-<?= $singleOrder['payment_status'] === 'paid' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($singleOrder['payment_status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Order Status</td>
                                <td>
                                    <span class="badge bg-<?= $singleOrder['status'] === 'completed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($singleOrder['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="no-orders-icon mb-3">
                        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-muted fs-5">No past orders</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/layout/footer.php'; ?>
