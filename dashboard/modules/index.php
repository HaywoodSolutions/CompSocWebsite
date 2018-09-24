<?php
    include_once '../page.php';
    renderPage("Modules", '
        <script id="module-template" type="text/x-handlebars-template">
            <a class="list-group-item list-group-item-action fadeIn" href="./view?moduleID={{code}}">
                <span><i class="fas fa-link"></i> {{code}} - {{name}}</span>
            </a>
        </script>
        <script id="module-create-template" type="text/x-handlebars-template">
            <div class="list-group-item flex-column">
                <div class="h3">Create Module</div>
                <div class="form-group">
                    <label for="moduleCode">Module Code</label>
                    <input type="email" class="form-control" id="moduleCode" placeholder="Course Code">
                </div>
                <div class="form-group">
                    <label for="moduleCode">Module Name</label>
                    <input type="email" class="form-control" id="moduleName" placeholder="Course Name">
                </div>
                <button type="submit" class="btn btn-primary" onclick="createModule()">Submit</button>
            </div>
        </script>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
            if (user) {
                db.collection("modules").get()
                    .then(function(querySnapshot) {
                        querySnapshot.forEach(function(doc) {
                            var source = document.getElementById("module-template").innerHTML;
                            var template = Handlebars.compile(source);
                            var html = template(doc.data());
                            $("#moduleList").append(html);
                        });
                    }).then(function(){
                        db.collection("users").doc(user.uid).get()
                        .then(function(querySnapshot) {
                            if (querySnapshot.data().admin == true)
                                var source = document.getElementById("module-create-template").innerHTML;
                                $("#moduleList").append(source);
                        });
                    })
                    .catch(function(error) {
                        console.log("Error getting documents: ", error);
                    });
            }
        });
        
        function createModule(){
            const code = $("#moduleCode").val();
            const name = $("#moduleName").val();
            if (code.length > 0 && name.length > 0) {
                db.collection("modules").doc().set({
                    code: code,
                    name: name,
                }).then(function() {
                    alert("Added Module");
                })
                .catch(function(error) {
                    console.error("Error writing document: ", error);
                });
            }
        }
    ');
?>