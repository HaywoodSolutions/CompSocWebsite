<?php
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
setcookie("auth", $db->isUserExisted($user["username"]), time() + (86400 * $days), "/");
?>