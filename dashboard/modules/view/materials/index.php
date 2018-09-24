<?php
    include_once '../../../page.php';
    renderPage("Materials", '
        <script id="material-template" type="text/x-handlebars-template">
            <a class="list-group-item list-group-item-action fadeIn" target="_blank" href="/getfile?id={{fileID}}">
                <span><i class="fas fa-external-link-alt"></i> {{name}}</span>
            </a>
        </script>
        <script id="material-create-template" type="text/x-handlebars-template">
            <div class="list-group-item flex-column fadeIn">
                <div class="h3">Create Material</div>
                <div class="form-group">
                    <label for="name">File Name</label>
                    <input type="email" class="form-control" id="name" placeholder="File Name">
                </div>
                <div class="form-group">
                    <label for="file">File</label>
                    <input type="file" class="form-control" id="file" placeholder="File">
                </div>
                <button type="submit" class="btn btn-primary" onclick="createModule()">Submit</button>
            </div>
        </script>
    ',
    '
        firebase.auth().onAuthStateChanged(function(user) {
            if (user) {
                db.collection("modules").doc(findGetParameter("moduleID")).collection("materials").get()
                    .then(function(querySnapshot) {
                        querySnapshot.forEach(function(doc) {
                            var source = document.getElementById("material-template").innerHTML;
                            var template = Handlebars.compile(source);
                            var html = template(doc.data());
                            $("#moduleList").append(html);
                        });
                    }).then(function(){
                        db.collection("users").doc(user.uid).get()
                        .then(function(querySnapshot) {
                            if (querySnapshot.data().admin == true)
                                var source = document.getElementById("material-create-template").innerHTML;
                                $("#moduleList").append(source);
                        });
                    })
                    .catch(function(error) {
                        console.log("Error getting documents: ", error);
                    });
            }
        });
    ');
?>