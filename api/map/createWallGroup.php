<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require_once '../include/DB_Functions.php';
    $db = new DB_Functions();
    header("Access-Control-Allow-Origin: *");
    // json response array
    $response = array("error" => FALSE);
    if (isset($_GET["name"]) && isset($_GET["type"]) && isset($_GET["building_id"])) {
        $name = $_GET["name"];
        $type = $_GET["type"];
        $building_id = $_GET["building_id"];
        $response = $db->createWallGroup($name, $type, $building_id);
        if ($response != null || $response != []) {
            echo json_encode($response);
        }
    }
    $response["error"] = TRUE;
    $response["error_msg"] = "Could not load course materials!";
    echo json_encode($response);
?>