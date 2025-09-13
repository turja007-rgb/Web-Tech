<?php include __DIR__ . "/layout/header.php"; ?>

<div class="container my-4">
    <div class="row g-4 justify-content-center">

        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-4">Profile Information</h4>
                    <form id="profileForm" method="POST" action="<?= url("/profile_update") ?>">
                        <?php
                        $fields = [
                            "name" => ["label" => "Name", "type" => "text", "value" => $customer["name"]],
                            "email" => ["label" => "Email", "type" => "email", "value" => $customer["email"]],
                            "phone" => ["label" => "Phone", "type" => "text", "value" => $customer["phone"] ?? ""],
                            "password" => ["label" => "New Password", "type" => "password", "value" => "", "placeholder" => "Enter new password"],
                            "current_password" => ["label" => "Current Password", "type" => "password", "value" => "", "placeholder" => "Required to update"]
                        ];
                        ?>
                        <?php foreach ($fields as $name => $f): ?>
                            <div class="mb-3 profile-field">
                                <label class="form-label fw-bold"><?= $f["label"] ?></label>
                                <div class="field-display d-flex align-items-center justify-content-between border rounded px-3 py-2">
                                    <span><?= htmlspecialchars($f["value"]) ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-btn">Edit</button>
                                </div>
                                <input type="<?= $f["type"] ?>"
                                       name="<?= $name ?>"
                                       value="<?= htmlspecialchars($f["value"]) ?>"
                                       placeholder="<?= $f["placeholder"] ?? $f["value"] ?>"
                                       class="form-control mt-2 d-none editable-input">
                            </div>
                        <?php endforeach; ?>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order History Card -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body d-flex flex-column" style="height: 600px; padding: 0;">

                    <!-- Fixed Header -->
                    <div class="p-3 border-bottom bg-white" style="flex: 0 0 auto;">
                        <h4 class="mb-0">Order History</h4>
                    </div>

                    <!-- Scrollable Orders List -->
                    <div class="overflow-auto p-3" style="flex: 1 1 auto;">
                        <?php if (!empty($orderHistory)): ?>
                            <?php foreach ($orderHistory as $order): ?>
                                <div class="card shadow-sm mb-3">
                                    <div class="card-body">

                                        <!-- Order ID & Date -->
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><strong>Order #<?= $order["id"] ?></strong></span>
                                            <span><strong><?= $order["created_at"] ?></strong></span>
                                        </div>

                                        <!-- Products Table -->
                                        <div class="table-responsive mb-2">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Price</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $subtotal = 0;
                                                foreach ($order["items"] as $item):
                                                    $itemTotal = $item["quantity"] * $item["unit_price"];
                                                    $subtotal += $itemTotal;
                                                    ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($item["product_name"]) ?></td>
                                                        <td class="text-center"><?= $item["quantity"] ?></td>
                                                        <td class="text-end">৳<?= number_format($item["unit_price"], 2) ?></td>
                                                        <td class="text-end">৳<?= number_format($itemTotal, 2) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="d-flex justify-content-end mb-1">
                                            <p class="mb-0 fw-bold">Subtotal: ৳<?= number_format($subtotal, 2) ?></p>
                                        </div>

                                        <!-- Grand Total -->
                                        <div class="d-flex justify-content-end mb-2">
                                            <p class="mb-0 fs-6"><strong>Grand Total: ৳<?= number_format($order["total_amount"], 2) ?></strong></p>
                                        </div>

                                        <!-- Payment & Status -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-3">
                                                <span>
                                                    <strong>Payment Status:</strong>
                                                    <span class="badge bg-<?= $order["payment_status"] === "paid" ? "success" : "danger" ?>">
                                                        <?= ucfirst($order["payment_status"]) ?>
                                                    </span>
                                                </span>
                                                <span>
                                                    <strong>Order Status:</strong>
                                                    <span class="badge bg-<?= $order["status"] === "completed" ? "success" : "warning" ?>">
                                                        <?= ucfirst($order["status"]) ?>
                                                    </span>
                                                </span>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-sm btn-primary view-order-btn"
                                                    data-order-id="<?= $order["id"] ?>"
                                                    data-summary-url="<?= url("/order/summary") ?>">
                                                View Details
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x fs-1 mb-3"></i>
                                <p class="text-muted fs-5">No past orders</p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalTitle">Order Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <p class="text-center text-muted">Loading...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>

</style>

<script src="<?= url("/public/js/orderSummary.js") ?>"></script>
<?php include __DIR__ . "/layout/footer.php"; ?>
