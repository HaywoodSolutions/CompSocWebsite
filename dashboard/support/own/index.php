<?php
    include_once '../../page.php';
    renderPage("Your Questions", '
        <script id="question-template" type="text/x-handlebars-template">
            <a class="list-group-item list-group-item-action justify-content-between align-items-center fadeIn" href="./view?questionID={{id}}">
                <h5>{{title}}</h5>
                <h6 class="text-muted"><small> <span class="badge badge-secondary">Student</span> Unanswered since 12-Mar-2017</small></h6>
            </a>
        </script>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
                db.collection("questions").where("ownerID", "==", user.uid).limit(15).get()
                    .then(function(querySnapshot) {
                        querySnapshot.forEach(function(doc) {
                            var source = document.getElementById("question-template").innerHTML;
                            var template = Handlebars.compile(source);
                            const data = doc.data();
                            data.id = doc.id;
                            var html = template(data);
                            $("#moduleList").append(html);
                        });
                    })
                    .catch(function(error) {
                        console.log("Error getting documents: ", error);
                    });
        });
    ');
?>