alert = function(text){
    $("alert-container alert span").html(text);
    $("alert-container alert").fadeIn(1000,"swing",function(){$("alert-container alert").delay(500).fadeOut(1000,"swing")})
}