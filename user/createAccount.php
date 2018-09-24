<?php
    require_once '/var/www/kentcomputingsociety.co.uk/public_html/api/include/DB_Functions.php';
    $days = 30;
    if(isset($_GET["firstname"]) && isset($_GET["secondname"])) {
        setcookie("firstname", $_GET["firstname"], time() + (86400 * $days), "/");
        setcookie("secondname", $_GET["secondname"], time() + (86400 * $days), "/");
    } else if(!isset($_COOKIE["firstname"]) || !isset($_COOKIE["secondname"])) {
        header('Location: '.'/user/finish');
    }

    if(isset($_GET["accountType"]) && isset($_COOKIE["username"]) && isset($_COOKIE["email"])) {
        setcookie("username", $_GET["username"], time() + (86400 * $days), "/");
        setcookie("email", $_GET["email"], time() + (86400 * $days), "/");
        setcookie("accountType", $_GET["accountType"], time() + (86400 * $days), "/");
        echo 3;
    } else if(!isset($_COOKIE["accountType"]) || !isset($_COOKIE["username"]) || !isset($_COOKIE["email"])) {
        header('Location: '.'/user/auth');
    }

    if(isset($_COOKIE["username"]) && isset($_COOKIE["email"]) && isset($_COOKIE["accountType"]) && isset($_COOKIE["firstname"]) && isset($_COOKIE["secondname"])) {
        header('Location: '.'/user/auth');
    }
?>