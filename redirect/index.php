<?php
    $message = "";
    if (isset($_GET["url"]) && isset($_GET["message"])) {
        $url = $_GET["url"];
        $message = $_GET["message"];
        header("refresh:2; url=".$url); 
    } else if (isset($_GET["url"])) {
        $url = $_GET["url"];
        header("refresh:2; url=".$url);
    } else {
        header("refresh:2; url=/");
        exit;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="/css/core.css">
        <link rel="stylesheet" href="/css/loader.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <div class="page">
            <div class="content">
                <div class="header full" style="background-image: url(/archive/hackkent18imgs/IMG_9970.jpg)">
                    <div class="header-content">
                        <div class="container">
                            <h1>We are redirecting you</h1>
                            <h2><?php echo $message; ?></h2>
                            <h1><div class="loader">
                              <span>{</span>
                                <img src="/images/KCSlogo5500.png" class="logo" />
                                <span>}</span>
                            </div></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>