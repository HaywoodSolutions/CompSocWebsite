<?php
    include_once '../page.php';
    renderPageContent("Job Offers", '
        <script id="job-offer-template" type="text/x-handlebars-template">
            <a class="list-group-item list-group-item-action justify-content-between align-items-center fadeIn" href="./view?questionID={{id}}">
                <h5>
                    {{name}}
                    {{#if new}}<span class="badge badge-secondary">New</span>{{/if}}
                    <span class="small">
                        <span class="badge badge-danger float-right">Deadline: {{deadline}}</span>
                    </span>
                </h5>
                <h6 class="text-muted small">{{company}} - {{location}}</h6>
                <h6 class="text-muted small">{{pay}}</h6>
                <h6 class="text-muted small">{{description}}</h6>
                <div style="height: 20px;">
                    {{#each tags}}
                        <span class="badge badge-pill badge-secondary" style="padding: 4px 10px;"><li>{{this}}</li></span>
                    {{/each}}
                </div>
            </a>
        </script>
    ',
    '',
    '
        firebase.auth().onAuthStateChanged(function(user) {
            db.collection("jobs").limit(15).get()
                .then(function(querySnapshot) {
                    querySnapshot.forEach(function(doc) {
                        var source = document.getElementById("job-offer-template").innerHTML;
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