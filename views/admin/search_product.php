<?php
require_once('../../controllers/helpers.php');
setSecurityHeaders();
requireAdmin();
header('Content-Type: application/json; charset=utf-8');

require_once('../../models/productModel.php');

$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : "";

if ($term === "") {
    echo json_encode(getAllProducts());
} else {
    $products = searchProducts($term);
    echo json_encode($products);
}
?>