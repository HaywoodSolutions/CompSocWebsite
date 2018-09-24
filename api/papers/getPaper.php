<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require_once '../include/DB_Functions.php';
    $db = new DB_Functions();
    header("Access-Control-Allow-Origin: *");
    // json response array
    $response = array("error" => FALSE);
    if (isset($_GET["module_id"]) && isset($_GET["year"])) {
        $module_id = $_GET["module_id"];
        $year = $_GET["year"];
        $paper = $db->getPaper($module_id, $year);
        if ($paper != null) {
            $response["paper"] = $paper;
            echo json_encode($response);
        } else {
            $response["error"] = TRUE;
            $response["error_msg"] = "Could not load paper!";
            echo json_encode($response);
        }
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Do not have required request!";
        echo json_encode($response);
    }
?>