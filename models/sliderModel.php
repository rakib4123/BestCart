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

function addSlider($title, $subtitle, $image) {
    $con = getConnection();
    $title = mysqli_real_escape_string($con, $title);
    $subtitle = mysqli_real_escape_string($con, $subtitle);
    $image = mysqli_real_escape_string($con, $image);
    
    $sql = "INSERT INTO sliders (title, subtitle, image) VALUES ('$title', '$subtitle', '$image')";
    return mysqli_query($con, $sql);
}

function deleteSlider($id) {
    $con = getConnection();
    $id = (int)$id;
    return mysqli_query($con, "DELETE FROM sliders WHERE id=$id");
}
?>