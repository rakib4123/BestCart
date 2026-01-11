<?php
require_once('db.php');

function getAllOrders($search = ''){
    $con = getConnection();

    if($search !== ''){
        $s = mysqli_real_escape_string($con, $search);
        $sql = "SELECT * FROM orders
                WHERE id LIKE '%$s%'
                   OR customer_name LIKE '%$s%'
                   OR email LIKE '%$s%'
                ORDER BY id DESC";
    }else{
        $sql = "SELECT * FROM orders ORDER BY id DESC";
    }

    $res = mysqli_query($con, $sql);
    $data = [];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }
    }
    return $data;
}


function getOrderById($id){
    $con = getConnection();
    $id = (int)$id;
    $sql = "SELECT * FROM orders WHERE id={$id} LIMIT 1";
    $res = mysqli_query($con, $sql);
    if($res && mysqli_num_rows($res) === 1){
        return mysqli_fetch_assoc($res);
    }
    return null;
}

function addOrder($o){
    $con = getConnection();

    $name   = mysqli_real_escape_string($con, $o['customer_name'] ?? '');
    $email  = mysqli_real_escape_string($con, $o['email'] ?? '');
    $amount = (float)($o['total_amount'] ?? 0);
    $status = mysqli_real_escape_string($con, $o['status'] ?? 'Pending');
    $date   = mysqli_real_escape_string($con, $o['order_date'] ?? date('Y-m-d'));
    $ship   = mysqli_real_escape_string($con, $o['shipping_address'] ?? '');
    $bill   = mysqli_real_escape_string($con, $o['billing_address'] ?? '');
    $items  = mysqli_real_escape_string($con, $o['order_items'] ?? '');

    $sql = "INSERT INTO orders (customer_name, email, total_amount, status, order_date, shipping_address, billing_address, order_items)
            VALUES ('$name', '$email', $amount, '$status', '$date', '$ship', '$bill', '$items')";
    return mysqli_query($con, $sql);
}

function updateOrder($o){
    $con = getConnection();

    $id     = (int)($o['id'] ?? 0);
    $name   = mysqli_real_escape_string($con, $o['customer_name'] ?? '');
    $email  = mysqli_real_escape_string($con, $o['email'] ?? '');
    $amount = (float)($o['total_amount'] ?? 0);
    $status = mysqli_real_escape_string($con, $o['status'] ?? 'Pending');
    $date   = mysqli_real_escape_string($con, $o['order_date'] ?? date('Y-m-d'));
    $ship   = mysqli_real_escape_string($con, $o['shipping_address'] ?? '');
    $bill   = mysqli_real_escape_string($con, $o['billing_address'] ?? '');
    $items  = mysqli_real_escape_string($con, $o['order_items'] ?? '');

    if($id <= 0){ return false; }

    $sql = "UPDATE orders SET
                customer_name='$name',
                email='$email',
                total_amount=$amount,
                status='$status',
                order_date='$date',
                shipping_address='$ship',
                billing_address='$bill',
                order_items='$items'
            WHERE id=$id";
    return mysqli_query($con, $sql);
}

function deleteOrder($id){
    $con = getConnection();
    $id = (int)$id;
    if($id <= 0){ return false; }
    $sql = "DELETE FROM orders WHERE id=$id";
    return mysqli_query($con, $sql);
}
function getSalesByDate($days = 7){
    $con = getConnection();

    
    $sql = "
        SELECT 
            DATE(order_date) AS order_date,
            COUNT(*) AS total_orders,
            COALESCE(SUM(total_amount), 0) AS total_sales
        FROM orders
        WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL ".(int)$days." DAY)
        GROUP BY DATE(order_date)
        ORDER BY DATE(order_date) DESC
    ";

    $res = mysqli_query($con, $sql);

    $data = [];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }
    }
    return $data;
}



if(!function_exists('addOrderReturnId')){
function addOrderReturnId($o){
    $con = getConnection();

    $name   = mysqli_real_escape_string($con, $o['customer_name'] ?? '');
    $email  = mysqli_real_escape_string($con, $o['email'] ?? '');
    $amount = (float)($o['total_amount'] ?? 0);
    $status = mysqli_real_escape_string($con, $o['status'] ?? 'Pending');
    $date   = mysqli_real_escape_string($con, $o['order_date'] ?? date('Y-m-d'));
    $ship   = mysqli_real_escape_string($con, $o['shipping_address'] ?? '');
    $bill   = mysqli_real_escape_string($con, $o['billing_address'] ?? '');
    $items  = mysqli_real_escape_string($con, $o['order_items'] ?? '');

    $sql = "INSERT INTO orders (customer_name, email, total_amount, status, order_date, shipping_address, billing_address, order_items)
            VALUES ('$name', '$email', $amount, '$status', '$date', '$ship', '$bill', '$items')";

    $ok = mysqli_query($con, $sql);
    if(!$ok){ return false; }
    return mysqli_insert_id($con);
}
}

if(!function_exists('getOrdersByEmail')){
function getOrdersByEmail($email){
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);
    $sql = "SELECT * FROM orders WHERE email='$email' ORDER BY id DESC";
    $res = mysqli_query($con, $sql);
    $data = [];
    if($res){
        while($row = mysqli_fetch_assoc($res)) $data[] = $row;
    }
    return $data;
}
}

?>
