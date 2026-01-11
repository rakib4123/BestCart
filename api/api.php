<?php
session_start();
header('Content-Type: application/json');

require_once('../models/sliderModel.php');

$action = $_REQUEST['action'] ?? '';

$public_actions = ['get_sliders'];

if (!in_array($action, $public_actions, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown or disabled action']);
    exit();
}

switch ($action) {

    // --- HOME: SLIDERS (BANNER SECTION) ---
    case 'get_sliders':
        echo json_encode(getAllSliders());
        break;

}
