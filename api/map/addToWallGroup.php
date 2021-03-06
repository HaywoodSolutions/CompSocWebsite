<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require_once '../include/DB_Functions.php';
    $db = new DB_Functions();
    header("Access-Control-Allow-Origin: *");
    // json response array
    $response = array("error" => FALSE);
    if (isset($_GET["wall_group_id"]) && $_GET["latlng"])) {
        $wall_group_id = $_GET["wall_group_id"];
        $latlngs = $_GET["latlng"];
        $response = $db->addToWallGroup($wall_group_id, $latlngs);
        if ($response != null || $response != []) {
            echo json_encode($response);
        }
    }
    $response["error"] = TRUE;
    $response["error_msg"] = "Could not load course materials!";
    echo json_encode($response);
?>