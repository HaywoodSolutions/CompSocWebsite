window.onload = function() {

    
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 
    });
    
    // Video
    var video = document.getElementById("video");

    // Buttons
    var playButton = document.getElementById("play-pause");
    
    var timeline = $('timeline')

    // Sliders
    var progressBar = document.getElementById("progress-bar");
    var volumeBar = document.getElementById("volume-bar");
    
    $("#open-side-pannel").click(function(){
        $("#side-pannel").toggle();
        $(this).toggleClass("dark");
    });
    
    // Event listener for the play/pause button
    playButton.addEventListener("click", function() {
      if (video.paused == true) {
        // Play the video
        video.play();

        // Update the button text to 'Pause'
        playButton.innerHTML = '<i class="fa fa-pause"></i>';
      } else {
        // Pause the video
        video.pause();

        // Update the button text to 'Play'
        playButton.innerHTML = '<i class="fa fa-play"></i>';
      }
    });
    /*
    // Event listener for the mute button
    muteButton.addEventListener("click", function() {
      if (video.muted == false) {
        // Mute the video
        video.muted = true;

        // Update the button text
        muteButton.innerHTML = '<span class="glyphicon glyphicon-volume-off" aria-hidden="true"></span>';
      } else {
        // Unmute the video
        video.muted = false;

        // Update the button text
        muteButton.innerHTML = '<span class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>';
      }
    });*/
    /*
    // Event listener for the seek bar
    progressBar.addEventListener("change", function() {
      // Calculate the new time
      var time = video.duration * (seekBar.value / 100);

      // Update the video time
      video.currentTime = time;
    });*/
    
    $( "#timeline" ).click(function(e){
        var parentOffset = $(this).parent().offset(); 
        var relX = e.pageX - parentOffset.left - 10;
        widthX = $(this).parent().width();
        var time = video.duration * (relX / widthX);

        // Update the video time
        video.currentTime = time;
    });
    
    // Update the seek bar as the video plays
    video.addEventListener("timeupdate", function() {
        // Calculate the slider value
        var value = (100 / video.duration) * video.currentTime;
        $('#progress_bar').css('width', value + "%");
        
        $("progress-timer").text( parseInt(video.currentTime).toHHMMSS() + " / " + parseInt(video.duration).toHHMMSS() );
        
        var chapters = document.getElementsByClassName("chapter")
        for (chapter in chapters){
            if (video.currentTime >= getTime(chapter.getAttribute("start")) && video.currentTime < getTime(chapter.getAttribute("end"))){
                chapter.classList.add("active");
            } else chapter.classList.remove("active");
        }
        
        var links = document.getElementsByClassName("quick-links")
        for (link in links){
            percentage = getTime(link.getAttribute("start")) / video.duration*100
            link.style.left = percentage+"%";
        }
    });
    
    // Pause the video when the slider handle is being dragged
    $( "#timeline" ).mousedown(function() {
      video.pause();
    });

    // Play the video when the slider handle is dropped
    $( "#timeline" ).mouseup(function() {
      video.play();
    });
    
    getSections("lecture19.cpt", video.duration);
    //getTranscript("lecture19.srt", timemin, timemax);
}

Number.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+':'+minutes+':'+seconds;
}

playTime = function (time){ //"00:02:02,729"
    video.currentTime = getTime(time);
}

getTime = function (time){ //"00:02:02,729"
    time = time.split(/:|,/);
    timesec = parseInt(parseInt(time[0] * 60) + time[1] * 60) + parseInt(time[2]) + parseFloat(time[3] / 1000);
    return timesec;
}


toUsefulTime = function (time){ //"00:02:02,729"
    time = time.split(/:|,/);
    text = "";
    if (parseInt(time[0]) > 0)
        text += time[0]+"h";
    if (parseInt(time[1]) > 0)
        text += time[1]+"m";
    if (parseInt(time[2]) > 0)
        text += time[2]+"s";
    return text;
}

timeDiff = function (time1, time2){ //"00:02:02,729"
    time1 = time1.split(/:|,/);
    time2 = time2.split(/:|,/);
    timediff = parseInt(time2[1]) - parseInt(time1[1]);
    if (timediff <= 1) 
        return timediff+" min";
    else 
        return timediff+" mins";
}

var video = {};

updateSections = function() {
    document.getElementById("sidebar-container").innerHTML = "";
    for (sec of video.sections){
        var id = "section-"+video.sections.indexOf(sec);
        var starttime = video.sections[video.sections.indexOf(sec)].time;
        var endtime = video.sections[video.sections.indexOf(sec)+1];
        if (endtime) {
            endtime = endtime.time;
        } else {
            endtime = video.duration;
        }
        document.getElementById("sidebar-container").innerHTML += `<div class="tree-item">
              <h4 class="list-group-item-heading">`+sec.title+`</h4>
              <p class="list-group-item-text">`+ getPeriodTime(video.sections[video.sections.indexOf(sec)], video.sections[video.sections.indexOf(sec)+1]) +`</p>
            <button id='`+id+`' class="disabled" timemin="`+starttime+`" timemax="`+endtime+`" onclick="sectionToggle('`+id+`')">
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
            </button>
            <div id="`+id+`-captions" class="captions ">
            </div>
        </div>`;
    }
}

sectionToggle= function(id){
    element = $("#"+id);
    caption = $("#"+id+"-captions");
    if (element.hasClass("active")){
        element.addClass("disabled");
        element.removeClass("active");
        caption.addClass("disabled");
    } else if (!element.hasClass("filled")){
        element.addClass("active");
        element.removeClass("disabled");
        element.addClass("filled");
        getTranscript("lecture19.srt", element.attr("timemin"), element.attr("timemax"), id+"-captions");
    } else {
        element.addClass("active");
        element.removeClass("disabled");
        caption.removeClass("disabled");
    }
}


/*<mark- type="caption" chapter="1" class="">
        <span>00:00</span>
        <a start="0" href="?t=0m0s">[NO SPEECH]</a>
       </mark->*/
function getPeriodTime(time1, time2){
    console.log(time1, time2)
    if (time2 == undefined) return "1 min";
    return timeDiff(time1.time, time2.time);
}
    
getNext = function(list, object) {
    if (list[list.length - 1] == object) {
        return null;
    } else return list[list.find(object)];
}

updateTranscript = function(timemin, timemax, id) {
    for (var caption of video.transcript) {
        if (caption.time >= timemin && caption.time <= timemax){
            $("#"+id).append(`<mark- type="caption" class="">
        <span>`+getNiceTime(caption.time)+`</span>
        <a start="0">`+caption.title+`</a>
       </mark->`);
        }
    }
}

getNiceTime = function(time1){
    time1 = time1.split(/:|,/);
    str = "";
    if (time1[0] != 00){
        str += time1[0].padStart(2, "0") + ":";
    }
    str += time1[1].padStart(2, "0") + ":";
    str += time1[2].padStart(2, "0");
    return str;
}

getSections = function (url, duration){
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", url, false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                video.sections = JSON.parse(rawFile.responseText.toString());
                updateSections();
            }
        }
    }
    rawFile.send(null);
}

getTranscript = function (url, timemin, timemax, id){
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", url, false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                video.transcript = JSON.parse(rawFile.responseText.toString());
                updateTranscript(timemin, timemax, id);
            }
        }
    }
    rawFile.send(null);
}