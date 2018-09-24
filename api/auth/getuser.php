<?php

require_once('/var/simplesamlphp/lib/_autoload.php');
$as = new SimpleSAML_Auth_Simple('default-sp');
$as->requireAuth();
$attributes = $as->getAttributes();

setcookie("pw", hash("sha256", ($attributes["urn:oid:0.9.2342.19200300.100.1.3"][0] + "KCSACCOUNT2255")), time() + (86400 * 30), "/");

$session = SimpleSAML_Session::getSessionFromRequest();
$session->cleanup();
?>