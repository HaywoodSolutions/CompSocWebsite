<?php
    include_once '../../../page.php';
    renderPage("Question View", '
        <script id="question-template" type="text/x-handlebars-template">
            <li class="list-group-item justify-content-between align-items-center fadeIn" style="background-color: rgb(230, 230, 230)">
                <div class="h5">{{title}}</div>
                <div class="lead">{{content}}</div>
                <h6 class="text-muted"><small>Asked by a <a class="blend" href="/profile?id=">Student</a> <span class="badge badge-secondary">Student</span> at 12-Mar-2017</small></h6>
                <h6 class="text-muted"><small><span onclick="shareURL()">share</span> <a>edit</a></small></h6>
            </li>
        </script>
        <script id="answer-template" type="text/x-handlebars-template">
            <li class="list-group-item justify-content-between align-items-center fadeIn">
                <div class="lead">{{content}}</div>
                <h6 class="text-muted"><small>Answered by a <a class="blend" href="/profile?id=">Student</a> <span class="badge badge-secondary">Student</span> at 12-Mar-2017</small></h6>
            </li>
        </script>
        <script id="answer-question-template" type="text/x-handlebars-template">
            <li class="list-group-item justify-content-between align-items-center fadeIn">
                <div class="form-group">
                    <label for="content" class="h6">Do you have the answer?</label>
                    <textarea class="form-control" id="content" rows="4" placeholder="This should point you in the right direction...."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" onclick="addAnswer()">Submit</button>
            </li>
        </script>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
                db.collection("questions").doc(findGetParameter("questionID")).get()
                    .then(function(doc) {
                        var source = document.getElementById("question-template").innerHTML;
                        var template = Handlebars.compile(source);
                        var html = template(doc.data());
                        $("#moduleList").append(html);
                    })
                    .then(function(){
                        db.collection("questions").doc(findGetParameter("questionID")).collection("answers").get()
                        .then(function(querySnapshot) {
                            querySnapshot.forEach(function(doc) {
                                var source = document.getElementById("answer-template").innerHTML;
                                var template = Handlebars.compile(source);
                                var html = template(doc.data());
                                $("#moduleList").append(html);
                            });
                        })
                        .then(function(){
                            var html = document.getElementById("answer-question-template").innerHTML;
                            $("#moduleList").append(html);
                        })
                        .catch(function(error) {
                            console.log("Error getting documents: ", error);
                        });
                    })
                    .catch(function(error) {
                        console.log("Error getting documents: ", error);
                    });
        });
        
        function addAnswer(){
            const content = $("#content").val();
            if (content.length > 0) {
                db.collection("questions").doc(findGetParameter("questionID")).collection("answers").doc().set({
                    content: content,
                    authorID: firebase.app().auth().getUid(),
                }).then(function() {
                    alert("Sent Answer");
                })
                .catch(function(error) {
                    console.error("Error writing document: ", error);
                });
            }
        }
        function shareURL(){
            
        }
    ');
?>