<?php
session_start();
header('Content-Type: application/json');

require_once('../models/sliderModel.php');
require_once('../models/categoryModel.php');

$action = $_REQUEST['action'] ?? '';

$public_actions = ['get_sliders', 'get_categories'];

if (!in_array($action, $public_actions, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown or disabled action']);
    exit();
}

switch ($action) {

    // banner section 
    case 'get_sliders':
        echo json_encode(getAllSliders());
        break;

    // categories ection 
    case 'get_categories':
        echo json_encode(getAllCategories());
        break;

}
