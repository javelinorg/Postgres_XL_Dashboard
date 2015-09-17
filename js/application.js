// ++++++++++++++++++++++++++++++++++++++++++
// Code to help the Javelin Data Processing site
// Good luck making any sence of it all. --Richard
$(document).ready(function () {
  // Enable the tool tips
  $("[data-toggle=tooltip]").tooltip();
  $("[data-toggle=popover]").popover();

  // do we need to count down to something?
  var countdownTimer, countdownCurrent = 6000;
  if($("[id=timer]").length){
    countdownTimer = $.timer(function() {
      var min = parseInt(countdownCurrent/6000);
      var sec = parseInt(countdownCurrent/100)-(min*60);
      var micro = pad(countdownCurrent-(sec*100)-(min*6000),2);
      var output = "00"; if(min > 0) {output = pad(min,2);}
//      $("[id=timer]").html(output+":"+pad(sec,2)+":"+micro+" seconds");
      $("[id=timer]").html(pad(sec,2)+" seconds");
      if(countdownCurrent == 0) {
        countdownTimer.stop();
        location.reload();
//        countdownReset();
      } else {
        countdownCurrent-=7;
        if(countdownCurrent < 0) {countdownCurrent=0;}
      }
    }, 70, true);
  }
  if($("#timerpause").length){
    $("#timerpause").click(function() {
      // pause the timer
      if($("#timerpause").val() == "play"){
        $("#timerpause").val("pause");
        $("#timerpause").html('<i class="fa fa-pause"></i> Pause');
      }else{
        $("#timerpause").val("play");
        $("#timerpause").html('<i class="fa fa-play"></i> Resume');
      }
      countdownTimer.toggle();
    });
  }
  if($("#pauseToAddServer").length){
    $("#pauseToAddServer").click(function() {
      // pause the timer
      if($("#timerpause").val() == "play"){
        $("#timerpause").val("pause");
        $("#timerpause").html('<i class="fa fa-pause"></i> Pause');
      }else{
        $("#timerpause").val("play");
        $("#timerpause").html('<i class="fa fa-play"></i> Resume');
      }
      countdownTimer.toggle();
    });
  }

  if($('.popup-marker').length){
    var isVisible = false;
    var clickedAway = false;

    $('.popup-marker').popover({
            html: true,
            trigger: 'manual'
        }).click(function(e) {
            $(this).popover('show');
            clickedAway = false
            isVisible = true
            e.preventDefault()
        });

    $(document).click(function(e) {
      if(isVisible & clickedAway)
      {
        $('.popup-marker').popover('hide')
        isVisible = clickedAway = false
      }
      else
      {
        clickedAway = true
      }
    });
  }

});

function toggleTimer(objTimer){
      // pause the timer
  if($("#timerpause").length){
    if($("#timerpause").val() == "play"){
      $("#timerpause").val("pause");
      $("#timerpause").html('<i class="fa fa-pause"></i> Pause');
    }else{
      $("#timerpause").val("play");
      $("#timerpause").html('<i class="fa fa-play"></i> Resume');
    }
    objTimer.toggle();
  }
}

// Padding function
function pad(number, length) {
  var str = '' + number;
  while (str.length < length) {str = '0' + str;}
  return str;
}
function countdownReset() {
  var newCount = 6000;
  if(newCount > 0) {countdownCurrent = newCount;}
  countdownTimer.stop().once();
}

//var choices = ["rock", "spock", "paper", "lizard", "scissors"];
var choices = ["rock", "paper", "scissors"];
var map = {};
choices.forEach(function(choice, i) {
    map[choice] = {};
    for (var j = 0, half = (choices.length-1)/2; j < choices.length; j++) {
        var opposition = (i+j)%choices.length
        if (!j)
            map[choice][choice] = "<p class=\"bg-info\">I rolled <i class=\"fa fa-hand-" + choices[opposition] + "-o\"></i> (" + choices[opposition] + "). It was a tie!</p>";
        else if (j <= half)
            map[choice][choices[opposition]] = "<p class=\"bg-warning\">I picked <i class=\"fa fa-hand-" + choices[opposition] + "-o\"></i> (" + choices[opposition] + "). I win!</p>";
        else
            map[choice][choices[opposition]] = "<p class=\"bg-success\">I chose <i class=\"fa fa-hand-" + choices[opposition] + "-o\"></i> (" + choices[opposition] + "). You win!</p>";
    }
})

function rps(choice1){
  var intRand = Math.floor(Math.random() * 3) + 1;
  var strMyChoice = choices[intRand - 1];
  console.log(strMyChoice);
  var myreturn = (map[choice1] || {})[strMyChoice] || "Invalid choice";
  $("#rpsResults").html(myreturn);
  console.log(myreturn);
}

function formatBytes(bytes, si) {
    var thresh = si ? 1000 : 1024;
    if(Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }
    var units = si
        ? ['kB','MB','GB','TB','PB','EB','ZB','YB']
        : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
    var u = -1;
    do {
        bytes /= thresh;
        ++u;
    } while(Math.abs(bytes) >= thresh && u < units.length - 1);
    return bytes.toFixed(1)+' '+units[u];
}