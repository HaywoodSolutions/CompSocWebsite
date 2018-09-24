<?php
    function renderPage($title, $templates, $lastScript){
        if ($_COOKIE["loggedIn"] == "false") {
            include '../../auth/index.php';
        }
        echo "<!DOCTYPE html>
                <html>
                    <head>
                        <link rel='stylesheet' href='/css/core.css'>
                        <link rel='stylesheet' href='/css/account-type.css'>
                        <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.0.13/css/all.css' integrity='sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp' crossorigin='anonymous'>
                        <script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.min.js'></script>
                        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'>
                        <script src='https://www.gstatic.com/firebasejs/5.4.1/firebase.js'></script>
                        
                        <script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
                        <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
                        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js' integrity='sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy' crossorigin='anonymous'></script>
                        <script src='/js/core.js'></script>
                        <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'/>
                        <link rel='stylesheet' href='/css/tags-input.css'>
                        <script src='/js/tags-input.js'></script>
                        <title>$title</title>
                    </head>
                    <body>
                        <alert-container>
                            <alert style='display: none;'>
                                <i class='fas fa-check'></i>
                                <span id='text'>Link Copied</span>
                            </alert>
                        </alert-container>
                        <div class='page'>
                            <div class='content'>
                                <div class='bg full' style='background-image: url(/archive/hackkent18imgs/IMG_9970.jpg)'>
                                    <div class='container'>
                                        <div class='row'>
                                            <div class='col-sm-12 col-md-12'>
                                                <div class='logo-container'>
                                                    <img src='/images/KCSlogo5500.png' class='logo small' />
                                                </div>
                                            </div>
                                        </div>
                                        $templates
                                            <div class='row'>
                                                <div class='col-sm-12 col-md-12'>
                                                    <ul class='list-group' id='moduleList'>
                                                        <li class='list-group-item justify-content-between align-items-center d-flex active'>
                                                            <a class='quickLink' href='../' role='button'>
                                                                <i class='fas fa-chevron-left'></i>
                                                            </a>
                                                            <h3>$title</h3>
                                                            ".
                                                            (($title !='Your Profile')?"<a class='quickLink' href='/dashboard/profile' role='button'>
                                                                <notification-container>
                                                                    <notification>
                                                                        6
                                                                    </notification>
                                                                </notification-container>
                                                                <i class='fas fa-user'></i>
                                                            </a>":"<div class='emptyLink'></div>")
                                                        ."</li>
                                                        <div id='main-list'>
                                                        </div>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script src='/js/firebase.js'></script>
                            <script>
                                $lastScript
                            </script>
                    </body>
                </html>";
    }

function renderPageContent($title, $templates, $content, $lastScript){
        if ($_COOKIE["loggedIn"] == "false") {
            include '../../auth/index.php';
        }
        echo "<!DOCTYPE html>
                <html>
                    <head>
                        <link rel='stylesheet' href='/css/core.css'>
                        <link rel='stylesheet' href='/css/account-type.css'>
                        <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.0.13/css/all.css' integrity='sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp' crossorigin='anonymous'>
                        <script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.min.js'></script>
                        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'>
                        <script src='https://www.gstatic.com/firebasejs/5.4.1/firebase.js'></script>
                        
                        <script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
                        <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
                        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js' integrity='sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy' crossorigin='anonymous'></script>
                        <script src='/js/core.js'></script>
                        <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'/>
                        <link rel='stylesheet' href='/css/tags-input.css'>
                        <script src='/js/tags-input.js'></script>
                        <title>$title</title>
                    </head>
                    <body>
                        <alert-container>
                            <alert style='display: none;'>
                                <i class='fas fa-check'></i>
                                <span id='text'>Link Copied</span>
                            </alert>
                        </alert-container>
                        <div class='page'>
                            <div class='content'>
                                <div class='bg full' style='background-image: url(/archive/hackkent18imgs/IMG_9970.jpg)'>
                                    <div class='container'>
                                        <div class='row'>
                                            <div class='col-sm-12 col-md-12'>
                                                <div class='logo-container'>
                                                    <img src='/images/KCSlogo5500.png' class='logo small' />
                                                </div>
                                            </div>
                                        </div>
                                        $templates
                                            <div class='row'>
                                                <div class='col-sm-12 col-md-12'>
                                                    <ul class='list-group' id='moduleList'>
                                                        <li class='list-group-item justify-content-between align-items-center d-flex active'>
                                                            <a class='quickLink' href='../' role='button'>
                                                                <i class='fas fa-chevron-left'></i>
                                                            </a>
                                                            <h3>$title</h3>
                                                            ".
                                                            (($title !='Your Profile')?"<a class='quickLink' href='/dashboard/profile' role='button'>
                                                                <notification-container>
                                                                    <notification>
                                                                        6
                                                                    </notification>
                                                                </notification-container>
                                                                <i class='fas fa-user'></i>
                                                            </a>":"<div class='emptyLink'></div>")
                                                        ."</li>
                                                        <div id='main-list'>
                                                            ".$content."
                                                        </div>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script src='/js/firebase.js'></script>
                            <script>
                                $lastScript
                            </script>
                    </body>
                </html>";
    }
?>