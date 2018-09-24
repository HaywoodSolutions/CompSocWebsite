<?php
    include_once '../../page.php';
    renderPageContent("Modules", '', '
        <a class="list-group-item list-group-item-action fadeIn" href="./lecture-view?moduleID='.$_GET["moduleID"].'">
            <span><i class="fas fa-link"></i> Lectures</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./materials?moduleID='.$_GET["moduleID"].'">
            <span><i class="fas fa-link"></i> Materials</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="/dashboard/support/questions?moduleID='.$_GET["moduleID"].'">
            <span><i class="fas fa-link"></i> Support</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./assessments?moduleID='.$_GET["moduleID"].'">
            <span><i class="fas fa-link"></i> Tasks/Assessments</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./papers?moduleID='.$_GET["moduleID"].'">
            <span><i class="fas fa-link"></i> Past Papers</span>
        </a>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
        });
    ');
?>