<?php
    include_once '../../page.php';
    renderPage("Find a Question", '
        <script id="question-template" type="text/x-handlebars-template">
            <a class="list-group-item list-group-item-action justify-content-between align-items-center fadeIn" href="./view?questionID={{id}}">
                <h5>{{title}}</h5>
                <h6 class="text-muted"><small> <span class="badge badge-secondary">Student</span> Unanswered since 12-Mar-2017</small></h6>
            </a>
        </script>
        <script id="filter-template" type="text/x-handlebars-template">
            <div class="list-group-item justify-content-between align-items-center">
                <div class="input-group">
                    <select class="custom-select" id="category-search" aria-label="Example select with button addon">
                        <option selected value="">All Catagories...</option>
                        <option value="CO320">CO320</option>
                        <option value="CO322">CO322</option>
                        <option value="CO323">CO323</option>
                    </select>
                    <input type="text" class="form-control" id="text-search" placeholder="Search...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">Filter</button>
                    </div>
                </div>
            </div>
        </script>
    ',
    '
        var source = document.getElementById("filter-template").innerHTML;
        var template = Handlebars.compile(source);
        var html = template();
        $("#moduleList li:first-child").after(html);
        firebase.auth().onAuthStateChanged(function(user) {
            db.collection("questions").limit(15).get()
                .then(function(querySnapshot) {
                    querySnapshot.forEach(function(doc) {
                        var source = document.getElementById("question-template").innerHTML;
                        var template = Handlebars.compile(source);
                        const data = doc.data();
                        data.id = doc.id;
                        var html = template(data);
                        $("#main-list").append(html);
                    });
                })
                .catch(function(error) {
                    console.log("Error getting documents: ", error);
                });
        });
        
        var updateResults = function() {
            var doc = db.collection("questions");
            const category = $("#category-search").val();
            const text = $("#text-search").val();
            if (category.length > 0) {
                doc = doc.where("category", "==", category);
            }
            if (text.length > 0) {
                doc = doc.where("question", "==", text);
            }
            $("#main-list").empty();
            doc.limit(15).get()
                .then(function(querySnapshot) {
                    querySnapshot.forEach(function(doc) {
                        var source = document.getElementById("question-template").innerHTML;
                        var template = Handlebars.compile(source);
                        const data = doc.data();
                        data.id = doc.id;
                        var html = template(data);
                        $("#main-list").append(html);
                    });
                })
                .catch(function(error) {
                    console.log("Error getting documents: ", error);
                });
        }
        
        $("#category-search").change(updateResults); 
        $("#text-search").keyup(updateResults); 
    ');
?>