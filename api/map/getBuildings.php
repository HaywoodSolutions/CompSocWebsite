<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require_once '../include/DB_Functions.php';
    $db = new DB_Functions();
    header("Access-Control-Allow-Origin: *");
    // json response array
    $response = array("error" => FALSE);
    $buildings = $db->getBuildings();
    if (is_array($buildings)) {
        $response["buildings"] = $buildings;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Could not load buildings!";
        echo json_encode($response);
    }
?>