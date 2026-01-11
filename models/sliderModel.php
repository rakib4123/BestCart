<?php
require_once('db.php');

function getAllSliders() {
    $con = getConnection();
    $result = mysqli_query($con, "SELECT * FROM sliders ORDER BY id DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

?>