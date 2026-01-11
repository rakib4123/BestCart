<?php
require_once('helpers.php');
requireAdmin();

require_once('../models/orderModel.php');

function normalizeOrderStatus($raw){
    $s = strtolower(trim((string)$raw));
    $s = str_replace(['_', '-'], ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);

    $map = [
        'pending'     => 'Pending',
        'in process'  => 'In Process',
        'processing'  => 'In Process',
        'process'     => 'In Process',
        'shipped'     => 'Shipped',
        'delivered'   => 'Completed',
        'completed'   => 'Completed',
        'complete'    => 'Completed',
        'cancelled'   => 'Cancelled',
        'canceled'    => 'Cancelled',
        'cancel'      => 'Cancelled'
    ];

    return $map[$s] ?? 'Pending';
}

// ADD ORDER (Place Order)
if (isset($_POST['add_order'])) {
    require_csrf();

    $data = [
        'customer_name'    => $_POST['customer'] ?? '',
        'email'            => $_POST['email'] ?? '',
        'total_amount'     => $_POST['amount'] ?? 0,
        // In manage_orders.php you used product_search. Keep it as order_items (single text field).
        'order_items'      => $_POST['items'] ?? ($_POST['product_search'] ?? 'Manual Entry'),
        'billing_address'  => $_POST['billing'] ?? '',
        'shipping_address' => $_POST['shipping'] ?? '',
        'order_date'       => $_POST['date'] ?? date('Y-m-d'),
        'status'           => $_POST['status'] ?? 'Pending'
    ];

    // PHP validation
    [$okC, $cust] = v_required($data['customer_name'], 2, 80);
    if (!$okC) { if (isAjax()) jsonOut(false, "Customer name is required"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }

    // Email is optional for manual orders
    $em = '';
    $emailTrim = trim((string)$data['email']);
    if ($emailTrim !== '') {
        [$okE, $em] = v_email($emailTrim);
        if (!$okE) { if (isAjax()) jsonOut(false, "Valid email is required"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }
    }

    [$okA, $amt] = v_float($data['total_amount'], 0, 99999999);
    if (!$okA) { if (isAjax()) jsonOut(false, "Invalid amount"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }

    [$okI, $items] = v_required($data['order_items'], 1, 500);
    if (!$okI) { if (isAjax()) jsonOut(false, "Order items are required"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }

    $data['customer_name'] = $cust;
    $data['email'] = $em;
    $data['total_amount'] = $amt;
    $data['order_items'] = $items;
    $data['status'] = normalizeOrderStatus($data['status']);

    $ok = addOrder($data);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Order placed successfully");
        jsonOut(false, "Failed to place order");
    } else {
        header("Location: ../views/admin/manage_orders.php");
        exit;
    }
}

// UPDATE ORDER
if (isset($_POST['update_order'])) {
    require_csrf();

    $id = (int)($_POST['order_id'] ?? 0);
    $existing = getOrderById($id);

    if (!$existing) {
        if (isAjax()) jsonOut(false, "Invalid order");
        header("Location: ../views/admin/manage_orders.php?err=1");
        exit;
    }

    // Keep existing values if edit form doesn't send all fields
    $emailRaw = trim((string)($_POST['email'] ?? ($existing['email'] ?? '')));
    $itemsRaw = trim((string)($_POST['items'] ?? ($_POST['product_search'] ?? ($existing['order_items'] ?? ''))));

    $data = [
        'id'               => $id,
        'customer_name'    => $_POST['customer'] ?? ($existing['customer_name'] ?? ''),
        'email'            => $emailRaw,
        'total_amount'     => $_POST['amount'] ?? ($existing['total_amount'] ?? 0),
        'order_items'      => $itemsRaw,
        'billing_address'  => $_POST['billing'] ?? ($existing['billing_address'] ?? ''),
        'shipping_address' => $_POST['shipping'] ?? ($existing['shipping_address'] ?? ''),
        'order_date'       => $_POST['date'] ?? ($existing['order_date'] ?? date('Y-m-d')),
        'status'           => $_POST['status'] ?? ($existing['status'] ?? 'Pending')
    ];

    // PHP validation
    [$okId, $id] = v_int($data['id'], 1, null);
    if (!$okId) { if (isAjax()) jsonOut(false, "Invalid order"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }

    [$okC, $cust] = v_required($data['customer_name'], 2, 80);
    if (!$okC) { if (isAjax()) jsonOut(false, "Customer name is required"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }

    // Email is optional (manual orders may not have email)
    $em = '';
    $emailTrim = trim((string)$data['email']);
    if ($emailTrim !== '') {
        [$okE, $em] = v_email($emailTrim);
        if (!$okE) { if (isAjax()) jsonOut(false, "Valid email is required"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }
    }

    [$okA, $amt] = v_float($data['total_amount'], 0, 99999999);
    if (!$okA) { if (isAjax()) jsonOut(false, "Invalid amount"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }

    [$okI, $items] = v_required($data['order_items'], 1, 500);
    if (!$okI) { if (isAjax()) jsonOut(false, "Order items are required"); header("Location: ../views/admin/manage_orders.php?err=1"); exit; }

    $data['customer_name'] = $cust;
    $data['email'] = $em;
    $data['total_amount'] = $amt;
    $data['order_items'] = $items;
    $data['status'] = normalizeOrderStatus($data['status']);

    $ok = updateOrder($data);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Order updated successfully");
        jsonOut(false, "Failed to update order");
    } else {
        header("Location: ../views/admin/manage_orders.php");
        exit;
    }
}

// DELETE ORDER
if (isset($_GET['delete'])) {
    require_csrf();
    $id = (int)$_GET['delete'];

    $ok = deleteOrder($id);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Order deleted", ['id' => $id]);
        jsonOut(false, "Failed to delete order");
    } else {
        header("Location: ../views/admin/manage_orders.php");
        exit;
    }
}

if (isAjax()) jsonOut(false, "Invalid request");
header("Location: ../views/admin/manage_orders.php");
exit;
?>
