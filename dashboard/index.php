<?php
    include_once './page.php';
    renderPageContent("Dashboard", '', '
    <a class="list-group-item list-group-item-action fadeIn" href="./profile/">
            <span><i class="fas fa-user"></i> Your Profile</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./modules/">
            <span><i class="fas fa-chalkboard-teacher"></i> Modules</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./employability/">
            <span><i class="fas fa-briefcase"></i> Employability</span>
        </a>
        <a class="list-group-item list-group-item-action fadeIn" href="./support/">
            <span><i class="fas fa-life-ring"></i> Support</span>
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