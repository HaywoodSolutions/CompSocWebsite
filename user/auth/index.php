<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('/var/simplesamlphp/lib/_autoload.php');
$as = new SimpleSAML_Auth_Simple('default-sp');
$as->requireAuth();
$att = $as->getAttributes();
$user["accountType"] = $att["unikentaccountType"][0];
$user["email"] = $att["urn:oid:0.9.2342.19200300.100.1.3"][0];
$user["username"] = $att["urn:oid:0.9.2342.19200300.100.1.1"][0];
$session = SimpleSAML_Session::getSessionFromRequest();
$session->cleanup();
require_once '/var/www/kentcomputingsociety.co.uk/public_html/api/include/DB_Functions.php';
$db = new DB_Functions();
$days = 30;
setcookie("username", $user["username"], time() + (86400 * $days), "/");
setcookie("email", $user["email"], time() + (86400 * $days), "/");
setcookie("accountType", $user["accountType"], time() + (86400 * $days), "/");
$setup = $db->isUserExisted($user["username"]);
if (!$setup){
    if ($db->storeUser($_COOKIE["username"], $_COOKIE["email"], $_COOKIE["accountType"], $_COOKIE["firstname"], $_COOKIE["secondname"])){
        echo file_get_contents("/var/www/kentcomputingsociety.co.uk/public_html/user/finishing/index.html");
        header('Refresh: 3; URL=http://kentcomputingsociety.co.uk/');
    } else header('Location: '.'/user/createAccount.php');
} else if ($setup) {
    setcookie("auth", 1, time() + (86400 * $days), "/");
    header('Location: '.'/');
}
?>