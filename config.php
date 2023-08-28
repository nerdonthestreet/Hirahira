<?php
   if(session_status() == PHP_SESSION_NONE){
    session_start();
   }
   
   $sitename = ""; // Header to appear at the top of most pages.
   $sqlservername = ""; // SQL server hostname.
   $sqlusername = ""; // SQL username.
   $sqlpassword = ""; // SQL password.
   $sqldbname = ""; // SQL database name.
   $hashtag = ""; // Twitter hashtag to display on video playback pages.
   $vimeo_token = ""; // Vimeo API access token.
   $welcome_text = ""

   try {
    $pdo = new PDO("mysql:host=$sqlservername;dbname=$sqldbname", $sqlusername, $sqlpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
   catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
   }
?>
