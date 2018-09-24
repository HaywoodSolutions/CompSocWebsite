function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return null;
}

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

var config = {
    apiKey: "AIzaSyCVCxvf8qDkJTBNCG79fdKcUXcPGgEPYs0",
    authDomain: "compsoc-a07cd.firebaseapp.com",
    databaseURL: "https://compsoc-a07cd.firebaseio.com",
    projectId: "compsoc-a07cd",
    storageBucket: "compsoc-a07cd.appspot.com",
    messagingSenderId: "937798497231"
};
firebase.initializeApp(config);
const db = firebase.firestore();
const storage = firebase.storage();

firebase.auth().onAuthStateChanged(function(user) {
    if (user) {
        setCookie("loggedIn", true, 30);
        if (getCookie("userProcessed") != "true") {
            var name = "";
            switch(getCookie("accountType")){
                case "staff": name = "a Staff Member"; break;
                case "ugtstudent":
                case "pgtstudent": name = "a Student"; break;
                case "alum": name = "a Alumni"; break;
            }
            db.collection("users").doc(user.uid).set({
                username: getCookie("username"),
                email: getCookie("email"),
                accountType: getCookie("accountType"),
                name: name,
                created: new Date(),
            })
            .then(function() {
                console.log("Document successfully written!");
                setCookie("userProcessed", true, 30);
            })
            .catch(function(error) {
                console.error("Error writing document: ", error);
                setCookie("userProcessed", false, 30);
            });
        } else {
            db.collection("users").doc(user.uid).update({
                lastSignOn: new Date(),
            })
            .then(function() {
                console.log("Document successfully written!");
                setCookie("userProcessed", true, 30);
            })
            .catch(function(error) {
                console.error("Error writing document: ", error);
                setCookie("userProcessed", false, 30);
            });
        }
    }
});

$(function(){
    if (getCookie("email") != null && getCookie("pw") != null){
        firebase.auth().signInWithEmailAndPassword(getCookie("email"), getCookie("pw")).catch(function(error) {
          // Handle Errors here.
          var errorCode = error.code;
          var errorMessage = error.message;
          if (errorCode == "auth/user-not-found") {
              firebase.auth().createUserWithEmailAndPassword(getCookie("email"), getCookie("pw")).catch(function(error) {
                  // Handle Errors here.
                  var errorCode = error.code;
                  var errorMessage = error.message;
                  location.replace("https://kentcomputingsociety.co.uk/auth"); 
              });
          } 
        });
    } else location.replace("https://kentcomputingsociety.co.uk/auth"); 
})