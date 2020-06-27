<?php
 // Global database/session initialization.
 require_once "./config.php";
?>
<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="./styles.css">
  <link rel="stylesheet" type="text/css" href="./player/video-js.css">
  <link rel="stylesheet" type="text/css" href="./player/videojs-resolution-switcher.css">
  <script src="./player/video.js"></script>
  <script src="./assets/jquery-1.11.3.js"></script>
 </head>
 <body>
  <div class="experienceContainer">
  <article class="videoContainer">
   <?php
    $stmt = $pdo->prepare("SELECT title, date, primary_game, secondary_game, length, embed_code, thumb FROM full WHERE id = :id");
    $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
    $param_id = trim($_GET["id"]);
    $stmt->execute();
    $result = $stmt->fetch();
   ?>
   <video-js id="content_video" class="video-js" controls preload="auto" autoplay="true" data-setup='{"aspectRatio": "16:9", "playbackRates": [0.5, 1, 1.5, 2], "html5": {"hls": {"overrideNative":true}}}' poster="<?php echo $result['thumb'] ?>">
   <?php echo $result['embed_code']; ?>
   </video-js>
   <script src="https://kushking.tips/player/videojs-resolution-switcher.js"></script>
   <a class="button-blue" style="margin-right: 5px;" href="/full-recordings.php">Back to List</a>
   <a class="button-blue" href="https://twitter.com/intent/tweet?button_hashtag=<?php echo $hashtag ?>">Tweet #<?php echo $hashtag ?></a>
  
   <?php
    // If you're an admin, display an edit button.
    if($_SESSION["admin"] == true){
     echo "<a class=\"button-orange\" id=\"right-button\" href=\"/experience-edit.php?id=" . trim($_GET["id"]) . "\">Edit Episode</a>";
    }elseif($_SESSUION["id"] == ""){ // If you're not logged in, display a login button.
     echo "<a class=\"button-orange\" id=\"right-button\" href=\"/login.php?id=" . trim($_GET["id"]) . "\">Log In</a>";
    }
   ?>
   
   <br/>
   <h1 class="full-title"><?php echo $result['title']; ?></h1>
   <?php $rawdate = strtotime($result["date"]);
   $prettydate = date("l, d F Y @ g:i A", $rawdate); ?>
   <p class="full-date"><?php echo $prettydate; ?></p>
   
   <hr style="width: 50%; border: dashed; border-width: 1px;" />
   
   <div class="experience-info">
    <p class="primary-game"><span style="font-weight: bold;">Primary game:</span> <?php echo $result['primary_game']; ?>
    <p class="secondary-game"><span style="font-weight: bold;">Secondary game:</span> <?php echo $result['secondary_game']; ?>
    <p class="runtime"><span style="font-weight: bold;">Runtime:</span> <?php echo $result['length']; ?>
   </div>
   
  </article>
  <article class="chatContainer">
   <div id="display-cues-1" class="display-cues">
    <p id="start-point-1"><p>
   </div>
  </article>
  </div>
  <?php require_once "./footer.php"; ?>
  
  <script>
   function matchHeight() {
    var vidHeight = $("#content_video_html5_api").height();
    document.getElementById("display-cues-1").style.height = vidHeight + "px";
    console.log(vidHeight);
   }
  
   $(window).resize(function() {
    matchHeight();
   });
   
   $(document).ready(function() {
    console.log("Running chat setup!");
    
    matchHeight();
    
    // Create an object to manipulate the VideoJS player.
    myPlayer = videojs('content_video');
    
    var cuesTime = [];
    var onHover = [];
    jumpToTime = function (video_id, t){
     myPlayer.currentTime(t + 0.01);
    }
    video_id = 1;
    var track = document.getElementById("entrack-" + video_id).track; // get text track from track element
    console.log(track);
    
    // Wait until the WebVTT file has been downloaded.
    function checkTracks() {
     if (myPlayer.textTracks_.getTrackById("entrack-1") == null) {
      console.log("Track is null, waiting 1 second...");
      window.setTimeout(checkTracks, 1000);
     } else {
      if (myPlayer.textTracks_.getTrackById("entrack-1").cues == null) {
       console.log("Chat is null, waiting 1 second...");
       window.setTimeout(checkTracks, 1000);
      } else {
       if (myPlayer.textTracks_.getTrackById("entrack-1").cues < 1) {
        console.log("Chat log is not loaded yet, waiting 1 second...");
        window.setTimeout(checkTracks, 1000);
       } else {
        var cues = myPlayer.textTracks_.getTrackById("entrack-1").cues;
        console.log("Number of chat messages: " + cues.length);
        // get list of cues
        for (var i = 0; i < cues.length; i++) {
         // console.log("found a cue - " + i);
         if(i == 0){
          cuesTime[video_id] = [];
          cuesTime[video_id][i] = 0;
          $('#display-cues-'+video_id).append('<p onclick="jumpToTime(' + video_id + ', ' + cues[i].startTime + ')" data-start-time-video-' + video_id + '="0" class="cue-active" id=' + i + '>' + cues[i].text + '</p>');
         }else{
          cuesTime[video_id][i] = cues[i].startTime;
          $('#display-cues-'+video_id).append('<p onclick="jumpToTime(' + video_id + ', ' + cues[i].startTime + ')" data-start-time-video-' + video_id + '="' + cues[i].startTime + '" class="cue-hidden" id=' + i + '>' + cues[i].text + '</p>');
         }
        }
        onHover[video_id] = false;
        $('#display-cues-'+video_id).scrollTop(0);
        // track.mode = "hidden"; 
        
        onTrackLoad = function(video_id) {
         
         // console.log("The track is: " + track);
         // console.log("The video is: " + video_id);
         // console.log("Track cues: " + track.cues.length);
         
         myPlayer.on("timeupdate", function(event) {
          var video_id = "1";
          // console.log("time changed");
          var currentTime = myPlayer.currentTime();
          // console.log(myPlayer.currentTime());
          
          var cueTimeCurrent = 0;
          for (var i = 0; i < cuesTime[video_id].length; i++) {
           if((currentTime - cuesTime[video_id][i]) <= 0){
             cueTimeCurrent = cuesTime[video_id][i-1];
            break;
           }
           cueTimeCurrent = cuesTime[video_id][i];
          }
          if(currentTime == 0){
           cueTimeCurrent = 0;
          }
          
          // When a cue passes, remove the "active" highlight.
          $('#display-cues-' + video_id + ' .cue-active').removeClass('cue-active');
          
          // When a cue becomes active, unhide it and set the "active" highlight.
          $('[data-start-time-video-' + video_id + '="' + cueTimeCurrent + '"]').removeClass('cue-hidden');
          $('[data-start-time-video-' + video_id + '="' + cueTimeCurrent + '"]').addClass('cue-active');
         
          var offsetTop = $('[data-start-time-video-' + video_id + '="' + cueTimeCurrent + '"]').offset().top - $('#start-point-'+video_id).offset().top;
          if(!onHover[video_id]){
           // This is an attempt to make the chat window scroll so the last visible message is at the bottom... currently a litle broken, it might be easier to just modify the cue generator so there's never two "active" messages at the same time.
           var scrollOffset = offsetTop - document.getElementById('display-cues-1').offsetHeight + (document.getElementsByClassName('cue-active')[document.getElementsByClassName('cue-active').length - 1].offsetHeight * 2) + document.getElementsByClassName('cue-active')[document.getElementsByClassName('cue-active').length - 1].style.marginBottom;
           $('#display-cues-' + video_id).scrollTop(scrollOffset);
           // console.log(scrollOffset);
          }
          //console.log(offsetTop);
         });
         
         myPlayer.on("seeking", function(event) {
          console.log("Video playback jumped!");
          
          // In the event of a jump, unhide all previous chat messages immediately (because we skipped over them.)
          currentChatMessage = document.getElementsByClassName('cue-active')[0].id;
          for (i = 0; i < currentChatMessage; i++) {
           document.getElementById(i).classList.remove('cue-hidden');
          }
         });
         
         $('.display-cues').hover(function(event){
          var video_id = (this.id).substring(13);
          onHover[video_id] = true;
         }, function (){
          var video_id = (this.id).substring(13);
          onHover[video_id] = false;
         })
        }
       
        onTrackLoad(1);
       
       }
      }
     }
    }
    checkTracks();
   })
  </script>
 </body>
</html>
