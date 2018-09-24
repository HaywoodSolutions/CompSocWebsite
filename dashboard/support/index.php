<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    include_once '../page.php';
    renderPageContent("Support Hub", '', '
        <a class="list-group-item list-group-item-action fadeIn" href="./ask/">
            <span class="fas fa-question"></span><span> Ask a Question</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./questions">
            <span class="fas fa-search"></span><span> Find a Question</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./own">
            <span class="fas fa-history"></span><span> Your Questions</span> <span class="badge badge-secondary">5</span>
        </a>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
            db.collection("users").doc(user.uid).get()
                .then(function(doc) {
                    const user = doc.data();
                })
                .catch(function(error) {
                    console.log("Error getting documents: ", error);
                });
        });
    ');
?>