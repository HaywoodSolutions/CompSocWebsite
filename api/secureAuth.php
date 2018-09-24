<?php
require_once('/var/simplesamlphp/lib/_autoload.php');
$as = new SimpleSAML_Auth_Simple('default-sp');
$as->requireAuth();
$att = $as->getAttributes();
global $user = new array();
$user["accountType"] = $att["unikentaccountType"][0];
$user["email"] = $att["urn:oid:0.9.2342.19200300.100.1.3"][0];
$user["username"] = $att["urn:oid:0.9.2342.19200300.100.1.1"][0];
$session = SimpleSAML_Session::getSessionFromRequest();
$session->cleanup();

require_once '/var/www/kentcomputingsociety.co.uk/public_html/api/lists/UC17.php';

final $auth = new secureAuth17();
$user["year"] = $auth->getYearAuth($user["email"]);
$days = 30;
setcookie("year", $user["year"], time() + (86400 * $days), "/");
?>