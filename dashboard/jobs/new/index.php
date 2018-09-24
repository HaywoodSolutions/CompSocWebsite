<?php
    include_once '../../page.php';
    renderPageContent("Post a Job", '', '
        <li class="searchable list-group-item justify-content-between align-items-center">
            <div class="form-group">
                <label for="name">Job Title</label>
                <input type="text" class="form-control" id="name" placeholder="Enter Job Title" size="75" />
            </div>
            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date" class="form-control" id="deadline" placeholder="Job Deadline" />
            </div>
            <div class="form-group">
                <label for="company">Company Name</label>
                <input type="text" class="form-control" id="company" placeholder="Your Company Name" />
            </div>
            <div class="form-group">
                <label for="location">Job Location</label>
                <input type="text" class="form-control" id="location" placeholder="Job Location" />
            </div>
            <div class="form-group">
                <label for="pay">Payment (optional)</label>
                <input type="text" class="form-control" id="pay" placeholder="Job Pay Rate" />
            </div>
            <div class="form-group">
                <label for="tags">Tags (minimum 2)</label>
                <input type="tags" class="form-control" id="tags" />
                <small id="emailHelp" class="form-text text-muted">Use required years like "1st year", "2nd year", "masters", and Job type "year placement", "summer internship".</small>
            </div>
            
            <h6 class="text-muted"><small>By posting this Job opportunity we will added to our website and our automated newsletter. May take up to 2 days to approve job submissions.</small></h6>
            
            <script>[].forEach.call(document.querySelectorAll(`input[type="tags"]`), tagsInput);</script>
            <button type="submit" onclick="askQuestion()" class="btn btn-primary">Submit</button>
        </li>
        <li class="searchable list-group-item justify-content-between align-items-center">
            <h6 class="text-muted"><small>By proceeding you accept the <a class="blend" href="/profile?id=">Rules and Conditions</a></small></h6>
        </li>
    </ul>
    ',
    '
        function askQuestion(){
            const name = $("#name").val();
            const deadline = $("#deadline").val();
            const company = $("#company").val();
            const location = $("#location").val();
            const pay = $("#pay").val();
            const tags = $("#tags").val();
            if (name.length > 0 && company.length > 0 && location.length > 0 && tags.length > 1) {
                db.collection("jobs").doc().set({
                    name: name,
                    deadline: deadline,
                    company: company,
                    location: location,
                    pay: pay,
                    tags: tags,
                    ownerID: firebase.app().auth().getUid(),
                }).then(function() {
                    alert("Submited Job");
                    window.location.href = "../";
                })
                .catch(function(error) {
                    console.error("Error writing document: ", error);
                });
            }
        }
    ');
?>