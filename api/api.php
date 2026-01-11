<?php
session_start();
header('Content-Type: application/json');

require_once('../models/productModel.php');
require_once('../models/categoryModel.php');
require_once('../models/sliderModel.php');

$action = $_REQUEST['action'] ?? '';

$public_actions = ['get_products', 'get_categories', 'get_sliders'];

if (!in_array($action, $public_actions, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown or disabled action']);
    exit();
}

switch ($action) {

    case 'get_sliders':
        echo json_encode(getAllSliders());
        break;

    case 'get_categories':
        echo json_encode(getAllCategories());
        break;

    case 'get_products':
        $term = isset($_GET['search']) ? trim($_GET['search']) : "";
        $cat  = isset($_GET['category']) ? trim($_GET['category']) : "";

        $all_products = getAllProducts($term);

        if ($cat !== "") {
            $all_products = array_values(array_filter($all_products, function ($p) use ($cat) {
                $pCat = isset($p['category']) ? trim($p['category']) : '';
                return strcasecmp($pCat, $cat) === 0;
            }));
        }

        if (isset($_GET['limit'])) {
            $limit  = (int)$_GET['limit'];
            $offset = isset($_GET['page']) ? (int)$_GET['page'] : 0;

            if ($limit < 1) $limit = 12;
            if ($offset < 0) $offset = 0;

            $output = array_slice($all_products, $offset, $limit);
            echo json_encode($output);
        } else {
            echo json_encode($all_products);
        }
        break;
}
