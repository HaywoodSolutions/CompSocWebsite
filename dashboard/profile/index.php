<?php
    include_once '../page.php';
    renderPageContent("Your Profile", '', '
        <li class="list-group-item justify-content-between align-items-center">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Username</span>
                </div>
                <input type="text" class="form-control" id="username" value="">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Your Name</span>
                </div>
                <input type="text" class="form-control" id="name" value="">
            </div>
            <label for="basic-url">External links</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                </div>
                <input type="text" class="form-control" id="website" value="" placeholder="Your Website">
            </div>
            
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-facebook-square"></i></span>
                </div>
                <input type="text" class="form-control" id="facebook" value="" placeholder="Facebook ID">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                </div>
                <input type="text" class="form-control" id="twitter" value="" placeholder="Twitter Username">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-steam"></i></span>
                </div>
                <input type="text" class="form-control" id="steam" value="" placeholder="Steam Username">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fab fa-github"></i></span>
                </div>
                <input type="text" class="form-control" id="github" value="" placeholder="GitHub Username">
            </div>
            
            <!--<div class="input-group mb-3">
                <a href="https://www.facebook.com/" class="form-control"><i class="fab fa-facebook-square"></i> Facebook</a>
                <a href="https://www.twitter.com/" class="form-control"><i class="fab fa-twitter-square"></i> Twitter</a>
                <a href="https://www.steam.com/" class="form-control"><i class="fab fa-steam-square"></i> Steam</a>
                <a href="https://www.steam.com/" class="form-control"><i class="fab fa-steam-square"></i> GitHub</a>
            </div>-->
        </li>
        <li class="searchable list-group-item justify-content-between align-items-center">
            <h6 class="text-muted"><small>By adding infomation you accept the <a class="blend" href="/profile?id=">Rules and Conditions</a>, also you can delete all your data <a class="blend" href="/profile?id=">here</a>.</small></h6>
        </li>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
                db.collection("users").doc(user.uid).get()
                    .then(function(doc) {
                        const user = doc.data();
                        $("#username").val(user.username);
                        $("#name").val((user.name) ? user.name : "");
                        $("#email").val(user.email);
                    })
                    .catch(function(error) {
                        console.log("Error getting documents: ", error);
                    });
        });
    ');
?>