<?php
    include_once '../../page.php';
    renderPageContent("Ask a Question", '', '
        <li class="searchable list-group-item justify-content-between align-items-center">
            <div class="form-group">
                <label for="question">Your Question</label>
                <input type="email" class="form-control" id="question" placeholder="Enter question title" size="75" />
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select class="form-control" id="category" >
                    <option value="General">General</option>
                    <option value="CO320">CO320 - Introduction to Object-Oriented Programming</option>
                    <option value="CO322">CO322 - Foundations of Computing I</option>
                    <option value="CO323">CO323 - Databases and the Web</option>
                    <option value="CO324">CO324 - Computer Systems</option>
                    <option value="CO325">CO325 - Foundations of Computing II</option>
                    <option value="CO328">CO328 - Human Computer Interaction</option>
                    <option value="CO334">CO334 - People and Computing</option>
                    <option value="CO520">CO520 - Further Object-Oriented Programming</option>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Give some more infomation</label>
                <textarea class="form-control" id="content" rows="4" placeholder="I am having problems with...."></textarea>
            </div>
            <div class="form-group">
                <label for="visibility">Visibility of Question</label>
                <select class="form-control" id="visibility" aria-describedby="visibilityHelp">
                    <option>Everyone</option>
                    <option>Students</option>
                    <option>Lecturer/Staff</option>
                    <option>Private (Lecturer emailed directly)</option>
                </select>
                <small id="visibilityHelp" class="form-text text-muted">You will be able to choose the visibility of your question</small>
            </div>
            <div class="form-group">
                <label for="visibility">Tags</label>
                <input id="tags" class="form-control" name="tags" type="tags" pattern="^#" placeholder="Tags">
                <small id="visibilityHelp" class="form-text text-muted">You will help lecturers and students find your question.</small>
            </div>
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
            const title = $("#question").val();
            const type = $("#category").val();
            const content = $("#content").val();
            const visibility = $("#visibility").val();
            const tags = $("#tags").val()
            if (title.length > 0 && type.length > 0 && content.length > 0 && visibility.length > 0) {
                db.collection("questions").doc().set({
                    title: title,
                    type: type,
                    content: content,
                    visibility: visibility,
                    ownerID: firebase.app().auth().getUid(),
                    tags: tags,
                }).then(function() {
                    alert("Sent Question");
                    window.location.href = "../";
                })
                .catch(function(error) {
                    console.error("Error writing document: ", error);
                });
            }
        }
    ');
?>