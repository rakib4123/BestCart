<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('layout.php');
require_once('../../models/orderModel.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = ($id > 0) ? getOrderById($id) : null;

if (!$order) {
    header("Location: manage_orders.php");
    exit;
}


$emailVal = $order['email'] ?? '';
function addressOnly($raw){
    $raw = (string)$raw;
    $trim = trim($raw);
    if($trim === '') return '';
    $decoded = json_decode($trim, true);
    if(json_last_error() === JSON_ERROR_NONE && is_array($decoded)){
        $addr  = trim((string)($decoded['address'] ?? $decoded['addresss'] ?? ''));
        $city  = trim((string)($decoded['city'] ?? ''));
        $postal= trim((string)($decoded['postal'] ?? $decoded['zip'] ?? ''));
        $parts = array_values(array_filter([$addr, $city, $postal], fn($v)=>$v!=='' ));
        return implode(', ', $parts);
    }
    
    return $raw;
}

$shippingVal = addressOnly($order['shipping_address'] ?? '');
$billingVal  = addressOnly($order['billing_address'] ?? '');

if ($emailVal && !filter_var($emailVal, FILTER_VALIDATE_EMAIL)) {
    $emailVal = '';
}


$statusVal = $order['status'] ?? 'Pending';
if ($statusVal === 'Processing') $statusVal = 'In Process';
if ($statusVal === 'Delivered') $statusVal = 'Completed';
?>

<div class="header-title">Edit Order #<?= $order['id'] ?></div>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form method="post" action="../../controllers/adminOrderController.php" data-ajax="true">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

        
        <div class="form-row">
            <div class="input-group">
                <label>Customer Name</label>
                <input type="text" name="customer" class="form-control" value="<?= htmlspecialchars($order['customer_name']) ?>" required>
            </div>
            <div class="input-group">
                <label>Customer Email (optional)</label>
                <input type="email" name="email" class="form-control" value="<?= e($emailVal) ?>" placeholder="client@example.com">
            </div>

            <div class="input-group">
                <label>Total Amount (৳)</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="<?= $order['total_amount'] ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Shipping Address</label>
                <textarea name="shipping" class="form-control"><?= htmlspecialchars($shippingVal) ?></textarea>
            </div>
            <div class="input-group">
                <label>Billing Address</label>
                <textarea name="billing" class="form-control"><?= htmlspecialchars($billingVal) ?></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Order Date</label>
                <input type="date" name="date" class="form-control" value="<?= $order['order_date'] ?>" required>
            </div>
            
            <div class="input-group">
                <label>Order Status</label>
                <select name="status" class="form-control" style="font-weight:bold;">
                    <option value="Pending" <?= $statusVal=='Pending'?'selected':'' ?>>Pending</option>
                    <option value="In Process" <?= $statusVal=='In Process'?'selected':'' ?>>In Process</option>
                    <option value="Shipped" <?= $statusVal=='Shipped'?'selected':'' ?>>Shipped</option>
                    <option value="Completed" <?= $statusVal=='Completed'?'selected':'' ?>>Completed</option>
                    <option value="Cancelled" <?= $statusVal=='Cancelled'?'selected':'' ?>>Cancelled</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" name="update_order" class="btn btn-primary" style="flex: 1;">Update Order</button>
            <a href="manage_orders.php" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
</div>

<?php require_once('footer.php'); ?>