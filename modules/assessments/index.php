<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once '../../../../api/include/DB_Functions.php';
    $db = new DB_Functions();
    if (isset($_GET["module_id"])) {
        $module_id = $_GET["module_id"];
    } else {
        header("Location: /redirect?url=/dashboard/module&message=No module selected");
        exit;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="/css/core.css">
        <link rel="stylesheet" href="/css/account-type.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    </head>
    <body>
        <div class="page">
            <div class="content">
                <div class="bg full" style="background-image: url(/archive/hackkent18imgs/IMG_9970.jpg)">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="logo-container">
                                        <img src="/images/KCSlogo5500.png" class="logo small" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <ul class="list-group" id="listFilter">
                                        <li class="list-group-item justify-content-between align-items-center d-flex active">
                                            <a class="btn btn-secondary back" href="javascript:history.back()" role="button"></a>
                                            <h3><?php echo $module_id; ?> Assessments</h3>
                                            <a class="btn btn-secondary home" href="/" role="button"></a>
                                        </li>
                                        <?php 
                                            $assessments = $db->getCourseAssessments($module_id);
                                            if (sizeof($assessments) <= 0){
                                                ?>
                                                    <div class="list-group-item">
                                                        <span>There are no material available for this course at the moment</span>
                                                    </div>
                                                <?php
                                            } else {
                                                foreach($assessments as $assessment) {
                                                    ?>
                                                        <div class="list-group-item flex-column align-items-start">
                                                            <div class="d-flex w-100 justify-content-between">
                                                                <h5 class="mb-1">Assessment <?php echo $assessment["number"]; ?> - <?php echo $assessment["name"]; ?></h5>
                                                                <!-- <small class="text-muted">23-Aug-2018</small> -->
                                                            </div>
                                                            <p class="mb-1">Weighting: <?php echo $assessment["percentage"]; ?>%</p>
                                                        </div>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </ul>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>