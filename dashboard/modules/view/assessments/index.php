<?php
    include_once '../../../page.php';
    renderPage("Assessments", '
        <script id="assessment-template" type="text/x-handlebars-template">
            <div class="list-group-items flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">Assessment {{number}} - {{name}}</h5>
                    <small class="text-muted">{{date}}</small>
                </div>
                <p class="mb-1">Weighting: {{percentage}}%</p>
            </div>
        </script>
        <script id="assessment-create-template" type="text/x-handlebars-template">
            <div class="list-group-item flex-column fadeIn">
                <div class="h3">Create Assessment</div>
                <div class="form-group">
                    <label for="number">Number</label>
                    <input type="number" class="form-control" id="number" value="1" min="1" max="25">
                </div>
                <div class="form-group">
                    <label for="name">Name of Assessment</label>
                    <input type="text" class="form-control" id="name" placeholder="Name">
                </div>
                <div class="form-group">
                    <label for="date">Date of Assessment</label>
                    <input type="text" class="form-control" id="date" placeholder="01-Jan-2018">
                </div>
                <div class="form-group">
                    <label for="percentage">Assessment Perctentage</label>
                    <input type="number" class="form-control" id="percentage" value="1" min="25" max="100">
                </div>
                <button type="submit" class="btn btn-primary" onclick="createAssessment()">Submit</button>
            </div>
        </script>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
            if (user) {
                db.collection("modules").doc(findGetParameter("moduleID")).collection("assessments").get()
                    .then(function(querySnapshot) {
                        querySnapshot.forEach(function(doc) {
                            var source = document.getElementById("assessment-template").innerHTML;
                            var template = Handlebars.compile(source);
                            var html = template(doc.data());
                            $("#moduleList").append(html);
                        });
                    }).then(function(){
                        db.collection("users").doc(user.uid).get()
                        .then(function(querySnapshot) {
                            if (querySnapshot.data().admin == true)
                                var source = document.getElementById("assessment-create-template").innerHTML;
                                $("#moduleList").append(source);
                        });
                    })
                    .catch(function(error) {
                        console.log("Error getting documents: ", error);
                    });
            }
         });
            
            function createAssessment(){
                const number = parseInt($("#number").val());
                const name = $("#name").val();
                const date = $("#date").val();
                const percentage = parseFloat($("#percentage").val());
                if (number >= 1 && name.length > 0 && date.length > 0  && percentage >= 1) {
                    db.collection("modules").doc(findGetParameter("moduleID")).collection("assessments").doc().set({
                        number: number,
                        name: name,
                        date: date,
                        percentage: percentage,
                    }).then(function() {
                        alert("Added Assessement");
                    })
                    .catch(function(error) {
                        console.error("Error writing document: ", error);
                    });
                }
            }
    ');
?>