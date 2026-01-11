<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('../../models/orderModel.php');
    require_once('../../models/productModel.php');

function renderOrderItems($raw) {
    if (!$raw) return 'Manual Order';

    $decoded = json_decode($raw, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $parts = [];
        foreach ($decoded as $it) {
            if (!is_array($it)) continue;
            $name = $it['name'] ?? ($it['product_name'] ?? 'Item');
            $qty  = $it['qty'] ?? ($it['quantity'] ?? 1);
            $name = trim((string)$name);
            $qty  = (int)$qty;
            if ($name === '') $name = 'Item';
            if ($qty < 1) $qty = 1;
            $parts[] = $name . ' (x' . $qty . ')';
        }
        if (empty($parts)) return 'Manual Order';

        $shown = array_slice($parts, 0, 2);
        $more  = count($parts) - count($shown);
        $out = implode(', ', $shown);
        if ($more > 0) $out .= ' +' . $more . ' more';
        return htmlspecialchars($out);
    }

    // Not JSON, show a safe trimmed preview
    $raw = trim((string)$raw);
    if ($raw === '') return 'Manual Order';
    $raw = mb_substr($raw, 0, 80);
    return htmlspecialchars($raw);
}



// Partial AJAX refresh: return only table rows
if (isAjax() && (isset($_GET['partial']) && $_GET['partial'] === 'order_rows')) {
    $orders = getAllOrders();
    if (!empty($orders)) {
        foreach ($orders as $o) {
            $color = "#f59e0b"; // Pending
            if ($o['status'] == 'In Process' || $o['status'] == 'Processing') $color = "#6366f1";
            if ($o['status'] == 'Completed' || $o['status'] == 'Delivered') $color = "#10b981";
            if ($o['status'] == 'Shipped') $color = "#3b82f6";
            if ($o['status'] == 'Cancelled') $color = "#ef4444";
            if ($o['status'] == 'In Process') $color = "#8b5cf6";
            ?>
            <tr>
                <td>#<?= $o['id'] ?></td>
                <td>
                    <b><?= htmlspecialchars($o['customer_name']) ?></b><br>
                    <span style="color:#0369a1; font-size:0.85rem;"><?= isset($o['email']) ? htmlspecialchars($o['email']) : '' ?></span><br>
                    <span style="color:#64748b; font-size:0.8rem;">
                        <i data-lucide="package" style="width:12px; vertical-align:middle;"></i>
                        <?= renderOrderItems($o['order_items'] ?? '') ?>
                    </span>
                </td>
                <td>৳<?= number_format($o['total_amount'], 2) ?></td>
                <td>
                    <span style="color:<?= $color ?>; font-weight:bold; background:<?= $color ?>15; padding:4px 8px; border-radius:4px; font-size:0.8rem;">
                        <?= $o['status'] ?>
                    </span>
                </td>
                <td>
                    <a href="edit_order.php?id=<?= $o['id'] ?>" class="btn btn-secondary" style="padding:5px 10px; font-size:0.8rem;">Edit</a>
                    <a
                      href="../../controllers/adminOrderController.php?delete=<?= $o['id'] ?>"
                      class="btn btn-danger"
                      style="padding:5px 10px; font-size:0.8rem;"
                      data-ajax-link="true"
                      data-confirm="true"
                      data-confirm-text="Delete this order?"
                      data-refresh-target="#ordersTableBody"
                      data-refresh-url="manage_orders.php?partial=order_rows"
                    >Del</a>
                </td>
            </tr>
            <?php
        }
    }
    exit;
}

require_once('layout.php');

// FETCH ALL ORDERS (No PHP Search needed anymore)
$orders = getAllOrders();
$products = getAllProducts();

    // Prepare Price List for JS
    $price_list_for_js = [];
    foreach($products as $p){
        $price_list_for_js[$p['name']] = (float)$p['price'];
    }
?>

<div class="header-title">Manage Orders</div>

<div class="card">
    <h3 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Place New Order</h3>
    <form method="post" action="../../controllers/adminOrderController.php" data-ajax="true" data-reset="true" data-refresh-target="#ordersTableBody" data-refresh-url="manage_orders.php?partial=order_rows">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-row" style="background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0;">
            <div class="input-group">
                <label>Find Product</label>
                <input type="text" name="product_search" id="productInput" list="products_list" class="form-control" placeholder="Type product name..." oninput="calculateTotal()" autocomplete="off">
                <datalist id="products_list">
                    <?php foreach($products as $p){ ?>
                        <option value="<?= htmlspecialchars($p['name']) ?>"></option>
                    <?php } ?>
                </datalist>
            </div>
            <div class="input-group">
                <label>Quantity</label>
                <input type="number" name="order_qty" id="orderQty" class="form-control" value="1" min="1" oninput="calculateTotal()">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Customer Name</label>
                <input type="text" name="customer" class="form-control" required>
            </div>
            <div class="input-group">
                <label>Customer Email</label>
                <input type="email" name="email" class="form-control" placeholder="client@example.com">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Total Amount (৳)</label>
                <input type="number" step="0.01" name="amount" id="totalAmount" class="form-control" style="background-color:#e2e8f0; font-weight:bold;">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Shipping Address</label>
                <textarea name="shipping" class="form-control" rows="2"></textarea>
            </div>
            <div class="input-group">
                <label>Billing Address</label>
                <textarea name="billing" class="form-control" rows="2"></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Order Date</label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="input-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Pending">Pending</option>
                    <option value="In Process">In Process</option>
                    <option value="Completed">Completed</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <button type="submit" name="add_order" class="btn btn-primary">
            <i data-lucide="shopping-bag"></i> Place Order
        </button>
    </form>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    
    <div style="padding:15px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
        <h4 style="margin:0;">Order List</h4>
        
        <div style="position:relative;">
            <i data-lucide="search" style="position:absolute; left:10px; top:10px; width:18px; color:#94a3b8;"></i>
            <input type="text" id="orderSearchInput" class="form-control" placeholder="Filter ID, Name, Email..." onkeyup="filterOrders()" style="width:300px; padding-left:35px;">
        </div>
    </div>

    <table id="ordersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Info</th>
                <th>Total Amount (৳)</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="ordersTableBody">
            <?php foreach($orders as $o){ ?>
            <tr>
                <td>#<?= $o['id'] ?></td>
                <td>
                    <b><?= htmlspecialchars($o['customer_name']) ?></b><br>
                    <span style="color:#0369a1; font-size:0.85rem;"><?= isset($o['email']) ? htmlspecialchars($o['email']) : '' ?></span><br>
                    <span style="color:#64748b; font-size:0.8rem;">
                        <i data-lucide="package" style="width:12px; vertical-align:middle;"></i> 
                        <?= renderOrderItems($o['order_items'] ?? '') ?>
                    </span>
                </td>
                <td>৳<?= number_format($o['total_amount'], 2) ?></td>
                <td>
                    <?php 
                        $color = "#f59e0b"; 
                        if($o['status'] == 'Completed') $color = "#10b981"; 
                        if($o['status'] == 'Shipped') $color = "#3b82f6";   
                        if($o['status'] == 'Cancelled') $color = "#ef4444"; 
                        if($o['status'] == 'In Process') $color = "#8b5cf6"; 
                    ?>
                    <span style="color:<?= $color ?>; font-weight:bold; background:<?= $color ?>15; padding:4px 8px; border-radius:4px; font-size:0.8rem;">
                        <?= $o['status'] ?>
                    </span>
                </td>
                <td>
                    <a href="edit_order.php?id=<?= $o['id'] ?>" class="btn btn-secondary" style="padding:5px 10px; font-size:0.8rem;">Edit</a>
                    <a
                      href="../../controllers/adminOrderController.php?delete=<?= $o['id'] ?>"
                      class="btn btn-danger"
                      style="padding:5px 10px; font-size:0.8rem;"
                      data-ajax-link="true"
                      data-confirm="true"
                      data-confirm-text="Delete this order?"
                      data-refresh-target="#ordersTableBody"
                      data-refresh-url="manage_orders.php?partial=order_rows"
                    >Del</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once('footer.php'); ?>

<script>
    // 1. Calculator Logic
    const productPrices = <?php echo json_encode($price_list_for_js); ?>;

    function calculateTotal() {
        var nameInput = document.getElementById('productInput').value;
        var qtyInput = document.getElementById('orderQty').value;
        var totalInput = document.getElementById('totalAmount');
        var price = productPrices[nameInput] || 0;
        var qty = parseInt(qtyInput) || 1;
        if(price > 0){
            totalInput.value = (price * qty).toFixed(2);
        }
    }

    // 2. Filter Logic
    function filterOrders() {
        var input = document.getElementById("orderSearchInput");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("ordersTable");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            // Check ID (Col 0) and Info (Col 1)
            var tdId = tr[i].getElementsByTagName("td")[0];
            var tdInfo = tr[i].getElementsByTagName("td")[1];
            
            if (tdInfo || tdId) {
                var txtId = tdId.textContent || tdId.innerText;
                var txtInfo = tdInfo.textContent || tdInfo.innerText;
                
                // If either ID OR Info matches, show it
                if (txtId.toUpperCase().indexOf(filter) > -1 || txtInfo.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>