<h5>Order #<?= $orderSummary['id']??'' ?> - <?= $orderSummary['created_at'] ?></h5>
<table class="table table-bordered align-middle mb-3">
    <thead class="table-light">
    <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orderSummary['items'] as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['unit_price'], 2) ?></td>
            <td>$<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3" class="text-end fw-bold">Subtotal</td>
        <td>$<?= number_format($orderSummary['subtotal_amount'], 2) ?></td>
    </tr>
    <tr>
        <td colspan="3" class="text-end fw-bold">Delivery Fee</td>
        <td>$<?= number_format($orderSummary['delivery_fee'], 2) ?></td>
    </tr>
    <tr>
        <td colspan="3" class="text-end fw-bold">Total</td>
        <td>$<?= number_format($orderSummary['total_amount'], 2) ?></td>
    </tr>
    <tr>
        <td colspan="3" class="text-end fw-bold">Payment Method</td>
        <td><?= htmlspecialchars($orderSummary['payment_method']) ?></td>
    </tr>
    <tr>
        <td colspan="3" class="text-end fw-bold">Payment Status</td>
        <td>
                <span class="badge bg-<?= $orderSummary['payment_status'] === 'paid' ? 'success' : 'danger' ?>">
                    <?= ucfirst($orderSummary['payment_status']) ?>
                </span>
        </td>
    </tr>
    <tr>
        <td colspan="3" class="text-end fw-bold">Order Status</td>
        <td>
                <span class="badge bg-<?= $orderSummary['status'] === 'completed' ? 'success' : 'warning' ?>">
                    <?= ucfirst($orderSummary['status']) ?>
                </span>
        </td>
    </tr>
    </tbody>
</table>